<?php
use DrivDigital\Vagrant\Provisioners\ScriptInstaller;

class InstallPhp56Common extends ScriptInstaller {

  public function run() {
    $this->aptInstall( 'php5.6-common' );
  }

}
