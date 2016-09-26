<?php
use DrivDigital\Vagrant\Provisioners\ScriptInstaller;

class InstallPhp7MbZip extends ScriptInstaller {

  public function run() {
    $this->aptInstall( 'php7.0-zip' );
  }

}

