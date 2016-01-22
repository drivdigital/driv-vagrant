<?php

/**
 * This file is run by the provision step of vagrant up
 */
define( 'PROVISION', TRUE );


chdir( '/vagrant' );

// Load utility class
require_once 'setup/class-setup.php';

// A temporary fix for to add wpcli to the box
require_once 'setup/tmp-fix.php';

// Load the vhost template
$vhost_template = file_get_contents( 'setup/vhost-template.conf' );

// Create a tool to save databases
`echo "#!/bin/sh" > save-db`;

$sites = setup::get_sites();

foreach ( $sites as $slug => $site ) {
  echo "Setting up vhost for $site\n";
  // Create the config folder if it doesnt exist yet
  if ( !file_exists( "config/" ) )
    mkdir( "config" );
  if ( !file_exists( "config/$slug/" ) )
    mkdir( "config/$slug" );

  // Run update and setup a few utiliy variables
  setup::update( $slug, $site );
  $system = setup::identify( $slug, $site );
  $db_prefix = setup::db_prefix( $slug, $site, $system );

  // Config files
  $config_file = setup::get_config_file( $system );
  if ( $config_file ) {
    $base_config = basename( $config_file );
    // Make a copy of the config file
    if ( file_exists( "$site/$config_file" ) ) {
      `cp "$site/$config_file" "config/$slug/$base_config"`;
    }
    // Insert an existing config file if found
    if ( !file_exists( "$site/$config_file" ) && file_exists( "config/$slug/$base_config" ) ) {
      `cp "config/$slug/$base_config" "$site/$config_file"`;
    }
  }

  if ( FALSE !== $db_prefix )
    setup::create_config( $slug, $site, $system, $db_prefix );

  // Apache2
  // Create the site vhost file
  if ( !file_exists( "config/$slug/$slug.dev.conf" ) ) {
    $site_vhost = str_replace( '%SITE', $site, $vhost_template );
    file_put_contents( "config/$slug/$slug.dev.conf", $site_vhost );
  }
  // Link the vhost conf to apache2
  if ( !file_exists( "/etc/apache2/sites-available/$slug.dev.conf" ) )
    `ln -s '/vagrant/config/$slug/$slug.dev.conf' /etc/apache2/sites-available`;
  // Enable the site
  `a2ensite '$site'`;


  // Database
  // Check the db-lock file

  if ( file_exists( '/.db-installed' ) )
    continue;

  echo "Seting up database, '$slug'.\n";
  `mysql -u root -e "CREATE DATABASE IF NOT EXISTS $slug"`;
  echo "Checking dev.sql $system";
  $dev_sql_created = false;
  if ( 'magento' == $system && !file_exists( "config/$slug/dev.sql" ) ) {
    echo "Copying magento dev.sql\n";
    require_once 'setup/magento-admin-user.php';
    $dev_sql_created = true;
  }
  if ( file_exists( "config/$slug.sql" ) ) {
    `echo 'SET foreign_key_checks=0;' > .tmp.sql`;
    `cat "config/$slug.sql" >> .tmp.sql`;
    echo "Importing database for $slug";
    `mysql -u root $slug < .tmp.sql`;
    if ( file_exists( "config/$slug/dev.sql" ) ) {
      echo "Running dev.sql for '$slug'.\n";
      `mysql -u root < "config/$slug/dev.sql"`;
    }
  }
  // Add the database to the saving tool
  `echo "mysqldump -u root $slug > /vagrant/config/$slug/$slug.sql" >> save-db`;
}
// Remove temporary sql file
if ( file_exists( '.tmp.sql' ) )
  `rm .tmp.sql`;

// Apply dev changes
if ( file_exists( "config/dev.sql" ) && !file_exists( '/.db-installed' ) )
  `mysql -u root < "config/dev.sql"`;

// Create a lock file for databases
`touch /.db-installed`;
`chmod 744 save-db`;
// Add the command
`echo "alias save-db='/vagrant/save-db'" >> /home/vagrant/.zshrc`;

foreach ( $sites as $slug => $site ) {
  // Vagrant files should start with `defined( 'PROVISION' ) || die();`
  if ( file_exists( "config/$slug/provision.php" ) ) {
    //@TODO: && setup::check_provision_file( … ) ) {
    echo "Running post provision script for $site\n";
    require_once( "config/$slug/provision.php" );
  }
  elseif ( file_exists( "$site/vagrant.php" ) ) {
    echo "Running post provision script for $site\n";
    require_once( "$site/vagrant.php" );
  }
}
// Restart apache
`service apache2 restart`;

// Success
echo "Success, your sites are now available at the following urls:\n";

foreach ( $sites as $site ) {
  echo "http://$site:8080/\n";
}
