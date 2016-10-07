<?php
/**
 * Class VagrantBoxTest
 */
class Vagrant {

  static public function getLoadedApacheModules() {
    $result  = [];
    $modules = shell_exec( 'apache2ctl -M' );
    $modules = explode( PHP_EOL, $modules );
    foreach ( $modules as $module ) {
      if ( preg_match( "/^\s/", $module ) ) {
        $result[] = preg_replace( "/\s\(.+\)/", "", trim( $module ) );
      }
    }
    return $result;
  }

  static function getEnabledApacheVhosts() {
    $result = [];
    $hosts  = shell_exec( 'apache2ctl -S' );
    $hosts  = explode( PHP_EOL, $hosts );

    $count = 0;
    foreach ( $hosts as $host ) {
      if ( preg_match( "/^\s/", $host ) ) {
        $count++;
        if ( $count == 1 ) {
          continue;
        }
        $parts      = explode( " ", trim( $host ) );
        $serverName = $parts[3];
        $result[]   = $serverName;
      }
    }
    return $result;
  }

  /**
   * Check if a console command exists.
   * @param string $cmd
   * @return bool
   */
  static function commandExist( $cmd ) {
    return ! empty( shell_exec( "which $cmd" ) );
  }


}