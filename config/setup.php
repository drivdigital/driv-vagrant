<?php

/**
 * This file is run by the provision step of vagrant up
 */


chdir( '/vagrant' );
$dirs = glob( '*.local' );

// A temporary fix for the 403 by apache
// (Until the box is rebuilt with a propper apache config file)
if ( file_exists( 'config/tmp-fix-403.php' ) )
  require_once 'config/tmp-fix-403.php';

// Load the vhost template
$vhost_template = file_get_contents( 'config/vhost.conf' );

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
  if ( file_exists( "database/$site.sql" ) ) {
    `mysql -u root $db_name < "database/$site.sql"`;
  }
  echo "Set up database: '$db_name'\n";

  /**
   * DATABASE - END
   */

}

// Create a lock file for databases
`touch /.db-installed`;

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
