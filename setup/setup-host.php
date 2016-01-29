<?php

/**
 * This file is run by the provision step of vagrant up
 */
define( 'PROVISION', TRUE );

chdir( dirname( dirname( __FILE__ ) ) );

// Load utility class
require_once 'setup/class-setup.php';

$opts = getopt( '', ['after', 'before'] );

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
  foreach ( $sites as $slug => $site ) {

    // Ignore found sites
    if ( FALSE !== strpos( $hosts, $site ) )
      continue;

    $hosts .= "\n127.0.0.1\t$site";
    $save = true;
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
}
if ( isset( $opts['after'] ) ) {
  // Nothing yet…
}
