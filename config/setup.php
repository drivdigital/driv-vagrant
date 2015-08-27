<?php

/**
 * This file is run by the provision step of vagrant up
 */


chdir( '/vagrant' );
$dirs = glob( '*.local' );

// A temporary fix for to add wpcli to the box
if ( file_exists( 'config/tmp-fix-wpcli.php' ) )
  require_once 'config/tmp-fix-wpcli.php';

// Load the vhost template
$vhost_template = file_get_contents( 'config/vhost.conf' );

// Create a tool to save databases
`echo "#!/bin/sh" > save-db`;

$sites = [];
foreach ( $dirs as $dir ) {
  // Skip non-dirs
  if ( !is_dir( $dir ) )
    continue;

  /**
   * APACHE2 - BEGIN
   */

  // Get the site name
  $site = basename( $dir );
  echo "Setting up vhost for $site\n";
  $sites[] = $site;

  // Create the site vhost file
  if ( !file_exists( "config/$site.conf" ) ) {
    $site_vhost = str_replace( '%SITE', $site, $vhost_template );
    file_put_contents( "config/$site.conf", $site_vhost );
  }

  // Link the vhost conf to apache2
  if ( !file_exists( "/etc/apache2/sites-available/$site.conf" ) )
    `ln -s '/vagrant/config/$site.conf' /etc/apache2/sites-available`;

  // Enable the site
  `a2ensite '$site'`;

  /**
   * APACHE2 - END
   */


  /**
   * DATABASE - BEGIN
   */
  // Check the db-lock file
  if ( file_exists( '/.db-installed' ) )
    continue;

  // No lock in place, go ahead.
  $db_name = preg_replace( '/\W/', '_', $site );
  // Remove the local bit at the end
  $db_name = preg_replace( '/_local$/', '', $db_name );
  `mysql -u root -e "CREATE DATABASE IF NOT EXISTS $db_name"`;
  if ( file_exists( "database/$db_name.sql" ) ) {
    `mysql -u root $db_name < "database/$db_name.sql"`;
  }
  echo "Set up database: '$db_name'\n";

  // Add the database to the saving tool
  `echo "mysqldump -u root $db_name > database/$db_name.sql" >> save-db`;

  /**
   * DATABASE - END
   */

}

// Create a lock file for databases
`touch /.db-installed`;
`chmod 744 save-db`;
// Add the command
`echo "alias save-db='/vagrant/save-db'" >> /home/vagrant/.zshrc`;

// Apply local changes
if ( file_exists( "database/local.sql" ) )
  `mysql -u root < "database/local.sql"`;

// Restart apache
`service apache2 restart`;

// Success
echo "Success, your sites will be available at the following urls:\n";
foreach ( $sites as $site ) {
  echo "http://$site:8080/\n";
}
echo "Make sure this line is in your hosts file:\n127.0.0.1\t". implode( ' ', $sites );
