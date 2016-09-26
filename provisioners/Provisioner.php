<?php

namespace DrivDigital\Vagrant\Provisioners;

require 'ScriptInstaller.php';

class Provisioner {

  /**
   * Installs provisioners for each site in setup:global/settings/sites
   *
   * @param array $sites
   */
  public static function install( $sites ) {

    foreach ( $sites as $siteSetting ) {
      if ( array_key_exists( 'provisioners', $siteSetting ) && count( $siteSetting['provisioners'] ) > 0 ) {

        $timeStart = microtime( true );

        // Save the current working directory so we can restore it
        // after each script
        $originalDir = getcwd();

        $installer = new ScriptInstaller( $siteSetting );

        $installer->log( str_repeat( '-', 80 ) );
        $installer->log( $siteSetting['name'] . ' provisioner started' );


        $installer->aptUpdateList();
        foreach ( $siteSetting['provisioners'] as $script ) {
          if ( ! empty( $script ) ) {
            $installer->install( $script );
          }
        }

        chdir( $originalDir );

        $timeEnd       = microtime( true );
        $executionTime = ( $timeEnd - $timeStart );

        $installer->log( $siteSetting['name'] . ' provisioner done in ' . $executionTime . ' seconds.' );
        $installer->log( 'Added: ' );

        foreach ( $installer->getAddedScripts() as $script ) {
          $installer->log( ' * ' . $script );
        }


      }
    }
  }

}



