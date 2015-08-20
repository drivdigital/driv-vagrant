<?php

/**
 * This file is run by the provision step of vagrant up
 */

chdir( '/vagrant' );
$dirs = glob( '*.local' );

// Load the vhost template
$vhost_template = file_get_contents( 'config/vhost.conf' );

$sites = [];
foreach ( $dirs as $dir ) {
  // Skip non-dirs
  if ( !is_dir( $dir ) )
    continue;

  // Get the site name
  $site = basename( $dir );
  echo "Creating vhost for $site";

  // Create the site vhost file
  $site_vhost = str_replace( '%SITE', $site, $vhost_template );
  file_put_contents( "config/$site.conf", $site_vhost );

  // Remove vhost file if it exists
  if ( file_exists( "/etc/apache2/sites-available/$site.conf" ) )
    unlink( "/etc/apache2/sites-available/$site.conf" );

  // Enable the site
  `ln -s '/vagrant/config/$site.conf' /etc/apache2/sites-available`;
  `a2ensite '$site'`;

  $sites[] = $site;
}

// Restart apache
`service apache2 restart`;

// Success
echo "Success, your sites will be available at the following urls:";
foreach ( $sites as $site ) {
  echo "http://$site:8080/";
}
echo "Make sure this line is in your hosts file:\n127.0.0.1\t". implode( ' ', $sites );
