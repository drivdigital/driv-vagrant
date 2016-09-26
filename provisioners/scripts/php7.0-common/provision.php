<?php
use DrivDigital\Vagrant\Provisioners\ScriptInstaller;

class InstallPhp7Common extends ScriptInstaller {

  public function run() {
    $this->aptInstall( 'php7.0-common' );
  }

}
