<?php
// APACHE SERVERNAME
$contents = file_get_contents( '/etc/apache2/apache2.conf' );
$add = "\nServerName Vagrant";
$contents = str_replace( '#ServerRoot "/etc/apache2"', '#ServerRoot "/etc/apache2"'. $add, $contents );
file_put_contents( '/etc/apache2/apache2.conf', $contents );

// WP-CLI
// A temporary way to add wpcli
// (Until the box is rebuilt with a propper apache config file)
if ( !file_exists( 'wp-cli.phar' ) || filemtime( 'wp-cli.phar') < 1450185638 )
  `curl -O https://raw.githubusercontent.com/wp-cli/builds/gh-pages/phar/wp-cli.phar -s`;
if ( file_exists( 'wp-cli.phar' ) && !file_exists( '/usr/local/bin/wp' ) )
  `sudo ln -s /vagrant/wp-cli.phar /usr/local/bin/wp`;
