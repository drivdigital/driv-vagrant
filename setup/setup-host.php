<?php

/**
 * This file is run by the provision step of vagrant up
 */
define( 'PROVISION', TRUE );

chdir( dirname( dirname( __FILE__ ) ) );

// Load utility class
require_once 'setup/class-setup.php';
require_once 'setup/class-hosts-parser.php';

$opts = getopt( '', ['after', 'before'] );

$is_private_network_vm = empty( getopt( '', [ 'port_forwarding' ] ) );

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


$sites = setup::get_all_sites();

if ( isset( $opts['before'] ) ) {
//  $hosts = trim( file_get_contents( '/etc/hosts' ) );
  try {
    $hosts = new HostsParser('/etc/hosts');
  }
  catch (Exception $e) {
    echo "Hosts: " . $e->getMessage() . "\n";
    return;
  }

  foreach ( $sites as $slug => $site ) {

    if ( $is_private_network_vm ) {
      if ( ! $hosts->exists( '192.168.33.10', $site ) ) {
        $hosts->add( '192.168.33.10', $site );
      }
      $hosts->activate( '192.168.33.10', $site );
      $hosts->deactivate( '127.0.0.1', $site );
    }
    else {
      if ( ! $hosts->exists( '127.0.0.1', $site ) ) {
        $hosts->add( '127.0.0.1', $site );
      }
      $hosts->activate( '127.0.0.1', $site );
      $hosts->deactivate( '192.168.33.10', $site );
    }
    $hosts->save();

  }

}
if ( isset( $opts['after'] ) ) {
  // Nothing yet…
}
