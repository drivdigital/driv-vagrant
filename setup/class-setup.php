<?php
defined( 'PROVISION' ) || die();

class setup {
  static function get_sites() {
    $sites = [];
    $dirs = glob( '*.dev' );
    foreach ( $dirs as $dir ) {
      // Skip non-dirs
      if ( !is_dir( $dir ) )
        continue;
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
      $contents = file_get_contents( $sample );
      $contents = strtr( [
        '' => ''
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
    $content = `head config/$slug/$slug.sql`;
    echo "\n\n----------------\n$content\n----------------\n\n";
    if ( 'wordpress' == $system ) {

    }
    else if ( 'magento' == $system ) {

    }
    return false;
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
}
