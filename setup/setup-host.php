<?php

/**
 * This file is run by the provision step of vagrant up
 */
define( 'PROVISION', TRUE );

chdir( dirname( dirname( __FILE__ ) ) );

// Load utility class
require_once 'setup/class-setup.php';

$opts = getopt( '', ['after', 'before'] );

$built_in_sites = [
    'vagrant.dev',
    'mail.dev',
    'phpmyadmin.dev',
    'logs.dev',
    'profiler.dev'
];

if ( isset( $opts['before'] ) ) {
  // Parse through the sites in the settings and clone any repo not existing
  foreach ( $GLOBALS['settings']['sites'] as $site ) {
    if (  '.dev' !== substr( @$site['name'], -4 ) ) {
      // Is not a .dev, skip
      continue;
    }
    if ( file_exists( $site['name'] ) ) {
      // Folder exists already, skip
      continue;
    }
    if ( @$site['git'] ) {
      // Clone the git repo
      `git clone "{$site['git']}" "{$site['name']}"`;
    }
  }
}


$sites = setup::get_sites();

if ( isset( $opts['before'] ) ) {
  $hosts = trim( file_get_contents( '/etc/hosts' ) );
  $save = false;

  // pre installed.
  foreach ( $sites as $slug => $site ) {

    // For 80 -> 8080 port forwarding.
    if ( ! preg_match_all("/127\.0\.0\.1\s+$site/", $hosts) ) {
      $hosts .= "\n127.0.0.1\t$site";
      $save = true;
    }

    // For private networks. No port forwarding.
    if ( ! preg_match_all("/192\.168\.33\.10\s+$site/", $hosts) ) {
      $hosts .= "\n192.168.33.10\t$site";
      $save = true;
    }
  }
  if ( $save ) {
    if ( is_writable( '/etc/hosts' ) ) {
      $hosts .= "\n";
      file_put_contents( '/etc/hosts', $hosts );
    }
    else {
      setup::error( "Error: '/etc/hosts' is not writeable. Make sure this line is in your hosts file:\n127.0.0.1\t". implode( ' ', $sites ) );
    }
  }

  $save = false;
  foreach ( $built_in_sites as $built_in_site ) {
    if ( ! preg_match_all("/127\.0\.0\.1\s+$built_in_site/", $hosts) ) {
      $hosts .= "\n127.0.0.1\t$built_in_site";
      $save = true;
    }
    if ( ! preg_match_all("/192\.168\.33\.10\s+$built_in_site/", $hosts) ) {
      $hosts .= "\n192.168.33.10\t$built_in_site";
      $save = true;
    }

  }
  if ( $save ) {
    file_put_contents( '/etc/hosts', $hosts );
  }

}
if ( isset( $opts['after'] ) ) {
  // Nothing yet…
}
