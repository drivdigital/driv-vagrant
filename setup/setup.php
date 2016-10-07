<?php

/**
 * This file is run by the provision step of vagrant up
 */
define( 'PROVISION', true );

chdir( '/vagrant' );


// Load utility class
require_once 'setup/class-setup.php';

// Load the vhost template
$vhost_template = file_get_contents( 'setup/vhost-template.conf' );

// Create a tool to save databases
`echo "#!/bin/sh" > save-db`;

$sites = setup::get_sites();

foreach ( $sites as $slug => $site ) {
  echo "Setting up vhost for $site\n";
  // Create the config folder if it doesnt exist yet
  if ( ! file_exists( 'config/' ) ) {
    mkdir( 'config' );
  }
  if ( ! setup::check_path( '', $slug, $site ) ) {
    mkdir( "config/$slug" );
  }

  $base_path = setup::get_path( '', $slug, $site );

  // Create config.json if it doesn't exist yet
  $config_json = setup::get_path( 'config.json', $slug, $site );
  if ( ! $config_json ) {
    // Json file has not been created
    // Get the clone url
    $git_url = `cd /vagrant/$site/ && git config --get remote.origin.url`;
    if ( $git_url ) {
      $settings = [
        'package' => '', // @TODO: Find the package name
        'name' => $site,
        'git'  => trim( $git_url ),
      ];
      $config_json = "$base_path/config.json";
      file_put_contents( $config_json, json_encode( $settings ) );
    }
  }

  // Run update and setup a few utiliy variables
  setup::update( $slug, $site );
  $system = setup::identify( $slug, $site );
  $db_prefix = setup::db_prefix( $slug, $site, $system );

  // Config files
  $config_path = setup::get_config_file( $system );
  if ( $config_path ) {
    $config_file = basename( $config_path );
    // Make a copy of the config file
    $base_config_file = setup::get_path( "$config_file", $slug, $site );
    if ( ! $base_config_file ) {
      $base_config_file = "$base_path/$config_file";
    }
    if ( file_exists( "$site/$config_path" ) ) {
      `cp "$site/$config_file" "$base_config_file"`;
    }
    // Insert an existing config file if found
    if ( ! file_exists( "$site/$config_path" ) && setup::check_path( $config_file, $slug, $site ) ) {
      $base_config_file = setup::get_path( $config_file, $slug, $site );
      `cp "$base_config_file" "$site/$config_path"`;
    }
  }

  if ( false !== $db_prefix ) {
    setup::create_config( $slug, $site, $system, $db_prefix );
  }

  // Apache2
  // Create the site vhost file
  $vhost_file = setup::get_path( "$slug.dev.conf", $slug, $site );
  if ( ! $vhost_file ) {
    $site_vhost = str_replace( '%SITE', $site, $vhost_template );
    $vhost_file = $base_path ."/$slug.dev.conf";
    file_put_contents( $vhost_file, $site_vhost );
  }
  // Link the vhost conf to apache2
  if ( ! file_exists( "/etc/apache2/sites-available/$slug.dev.conf" ) ) {
    `ln -s '/vagrant/$vhost_file' /etc/apache2/sites-available`;
  }
  // Enable the site
  `a2ensite '$site'`;


  // Database
  // Check the db-lock file

  if ( file_exists( '/.db-installed' ) ) {
    continue;
  }

  echo "Seting up database, '$slug'.\n";
  `mysql -u root -e "CREATE DATABASE IF NOT EXISTS $slug"`;
  echo "Checking dev.sql $system";
  $dev_sql_created = false;
  if ( file_exists( "config/$slug.sql" ) ) {
    `echo 'SET foreign_key_checks=0;' > .tmp.sql`;
    `cat "config/$slug.sql" >> .tmp.sql`;
    echo "Importing database for $slug";
    `mysql -u root $slug < .tmp.sql`;
    $dev_sql = setup::get_path( 'dev.sql', $slug, $site );
    if ( $dev_sql ) {
      echo "Running dev.sql for '$slug'.\n";
      `mysql -u root < "$dev_sql"`;
    }
  }
  // Add the database to the saving tool
  `echo "mysqldump -u root $slug > /vagrant/config/$slug.sql" >> save-db`;
}
// Remove temporary sql file
if ( file_exists( '.tmp.sql' ) ) {
  `rm .tmp.sql`;
}

// Apply dev changes
if ( file_exists( 'config/dev.sql' ) && ! file_exists( '/.db-installed' ) ) {
  `mysql -u root < "config/dev.sql"`;
}

// Create a lock file for databases
`touch /.db-installed`;
`chmod 744 save-db`;
// Add the command
// @todo: check if already added.
`echo "alias save-db='/vagrant/save-db'" >> /home/vagrant/.zshrc`;
`echo "alias save-db='/vagrant/save-db'" >> /home/vagrant/.bashrc`;

foreach ( $sites as $slug => $site ) {
  // Vagrant files should start with `defined( 'PROVISION' ) || die();`
  $provision_script = setup::get_path( 'provision.php', $slug, $site );
  if ( $provision_script ) {
    //@TODO: && setup::check_provision_file( … ) ) {
    echo "Running post provision script for $site\n";
    require_once( $provision_script );
  }
  elseif ( file_exists( "$site/vagrant.php" ) ) {
    echo "Running post provision script for $site\n";
    require_once( "$site/vagrant.php" );
  }
}

if ( ! empty( $GLOBALS['settings']['sites'] ) ) {
  require_once( __DIR__ . '/provisioners/Installer.php' );
  DrivDigital\Vagrant\Provisioners\Installer::install( $GLOBALS['settings']['sites'] );
}

// Restart apache
`service apache2 restart`;

// Success
echo "Success, your sites are now available at the following urls:\n";

foreach ( $sites as $site ) {
  echo "http://$site:8080/\n";
}
