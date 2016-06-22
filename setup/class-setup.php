<?php
defined( 'PROVISION' ) || die();

// Get settings

$GLOBALS['settings'] = [
  'sites' => [],
];

// Load plugins
// @TODO: glob( 'setup/plugins/*' );
require_once 'setup/plugins/file-sync/setup.php';

if ( file_exists( 'config/vagrant-config.json' ) ) {
  $json = json_decode( file_get_contents( 'config/vagrant-config.json' ), true );
  foreach ( $json as $key => $value ) {
    $GLOBALS['settings'][$key] = $value;
  }
}
$sites = setup::get_sites();
foreach ( $sites as $slug => $site ) {
  $base_path = setup::get_path( '', $slug, $site );
  $config_json = setup::get_path( 'config.json', $slug, $site );
  if ( ! $base_path ) {
    continue;
  }
  if ( $config_json ) {
    $GLOBALS['settings']['sites'][] = json_decode( file_get_contents( $config_json ), true );
  }
}

// Make settings a global
class setup {
  static function get_sites() {
    $sites = [];
    $dirs = glob( '*.dev' );
    foreach ( $dirs as $dir ) {
      // Skip non-dirs
      if ( ! is_dir( $dir ) ) {
        continue;
      }
      // Get the site name
      $site = basename( $dir );
      if ( preg_match( '/\s/', $site ) ) {
        self::error( "Error: project '$site' contains whitespace." );
        continue;
      }

      // No lock in place, go ahead.
      $slug = preg_replace( '/\W/', '_', $site );
      // Remove the dev bit at the end
      $slug = preg_replace( '/_dev$/', '', $slug );
      $sites[$slug] = $site;
    }
    return $sites;
  }
  static function update( $slug, $site ) {
    // Restructured the config system
    if ( file_exists( "setup/$site.conf" ) ) {
      `mv "setup/$site.conf" "config/$slug/$slug.dev.conf"`;
    }
    if ( file_exists( "database/$slug.sql" ) ) {
      `mv "database/$slug.sql" "config/$slug/$slug.sql"`;
    }
    if ( file_exists( "database/dev.sql" ) ) {
      `mv "database/dev.sql" "config/dev.sql"`;
    }
    if ( file_exists( "config/$slug/$slug.sql" ) ) {
      `mv "config/$slug/$slug.sql" "config/$slug.sql"`;
    }

  }

  static function get_config_file( $system ) {
    if ( 'wordpress' == $system )
      return 'wp-config.php';
    if ( 'magento' == $system )
      return 'app/etc/local.xml';
    return FALSE;
  }

  static function identify( $slug, $site ) {
    if ( file_exists( "$site/wp-config-sample.php" ) ) {
      echo "WordPress identified";
      return 'wordpress';
    }
    if ( file_exists( "$site/app/etc/local.xml.template" ) ) {
      echo "Magento identified";
      return 'magento';
    }
    echo "The CMS could not be identified";
    return false;
  }
  static function create_config( $slug, $site, $system, $db_prefix ) {
    if ( 'wordpress' == $system ) {
      $sample = "$site/wp-config-sample.php";
      $config = "$site/wp-config.php";
      if ( file_exists( $config ) ) {
        echo "Config file already exists";
        return;
      }
      // Not implemented yet. Bail early
      return;
      $contents = file_get_contents( $sample );
      $contents = strtr( $contents, [
        // @TODO:
        '' => '',
      ] );
      file_put_contents( $config, $contents );
      echo "Creating a config file for $system";
      return;
    }
    if ( 'magento' == $system ) {
      if ( !file_exists( "$site/errors/local.xml" ) )
        `cp "$site/errors/local.xml.sample" "$site/errors/local.xml"`;
      $sample = "$site/app/etc/local.xml.template";
      $config = "$site/app/etc/local.xml";
      if ( file_exists( $config ) ) {
        echo "Config file already exists";
        return;
      }
      $contents = file_get_contents( $sample );
      $contents = strtr( $contents, [
        '{{date}}'               => '<![CDATA[Mon, 01 Jan 2015 00:00:00 +0000]]>',
        '{{key}}'                => '<![CDATA[vagrant]]>',
        '{{db_prefix}}'          => '<![CDATA['.$db_prefix.']]>',
        '{{db_host}}'            => '<![CDATA[localhost]]>',
        '{{db_user}}'            => '<![CDATA[root]]>',
        '{{db_pass}}'            => '<![CDATA[]]>',
        '{{db_name}}'            => '<![CDATA['. $slug. ']]>',
        '{{db_init_statemants}}' => '<![CDATA[SET NAMES utf8]]>',
        '{{db_model}}'           => '<![CDATA[mysql4]]>',
        '{{db_type}}'            => '<![CDATA[pdo_mysql]]>',
        '{{db_pdo_type}}'        => '<![CDATA[]]>',
        '{{session_save}}'       => '<![CDATA[files]]>',
        '{{admin_frontname}}'    => '<![CDATA[admin]]>',
      ] );
      file_put_contents( $config, $contents );
      echo "Creating a config file for $system";

      return;
    }
    echo "No config file will be created";
    return;
  }
  static function db_prefix( $slug, $site, $system ) {
    if ( !file_exists( "config/$slug/$slug.sql" ) )
      return '';
    $content = `head -n1000 config/$slug/$slug.sql | grep "CREATE TABLE"`;
    // echo "\n\n----------------\n$content\n----------------\n\n";
    $lines = explode( "\n", $content );
    $assumed_prefix = FALSE;
    $wrong_assumption = FALSE;
    // Loop through each CREATE TABLE line
    foreach ($lines as $line) {
      // Detect the table name with some fancy regex
      if ( preg_match( '/\s`?(\S+_\w+)\W*$/', $line, $matches ) ) {
        // Break the table name into parts
        $table = $matches[1];
        $parts = explode( '_', $table );

        // No assumption made yet. Make one now
        if ( !$assumed_prefix )
          $assumed_prefix = $parts[0];

        // If any table is not prefixed then we have an incorrect assumption
        if ( $parts[0] != $assumed_prefix )
          $wrong_assumption = TRUE;
      }
    }

    if ( !$wrong_assumption ) {
      echo "Detected \"$assumed_prefix\" as the table prefix";
      return $assumed_prefix;
    }
    return FALSE;
  }

  static function error( $message ) {
    $cols = 120;
    echo "\x1b[31m";
    for ( $i = 0; $i <= $cols; $i++ )
      echo "=";
    echo "\n";
    echo "$message\n";
    for ( $i = 0; $i <= $cols; $i++ )
      echo "=";
    echo "\n";
    echo "\x1b[0m";
  }

  static function check_path( $path, $slug, $site ) {
    return (boolean) self::get_path( $path, $slug, $site );
  }

  static function get_path( $path, $slug, $site ) {
    $best_path = "$site/vagrant-config/$path";
    if ( file_exists( "$best_path" ) ) {
      return "$best_path";
    }
    $best_path = "config/$slug/$path";
    if ( file_exists( $best_path ) ) {
      return $best_path;
    }
    return false;
  }
}
