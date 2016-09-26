<?php
use DrivDigital\Vagrant\Provisioners\ScriptInstaller;

class Install56Memcached extends ScriptInstaller {

  public function run() {
    $this->aptInstall( 'memcached' );
    $this->aptInstall( 'php5.6-memcached' );
  }

}
