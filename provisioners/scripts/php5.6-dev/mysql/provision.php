<?php
use DrivDigital\Vagrant\Provisioners\ScriptInstaller;

class InstallPhp56Mysql extends ScriptInstaller {

  public function run() {
    $this->aptInstall( 'php5.6-mysql' );
  }

}

