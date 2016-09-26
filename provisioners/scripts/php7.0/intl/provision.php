<?php
use DrivDigital\Vagrant\Provisioners\ScriptInstaller;

class InstallPhp7Intl extends ScriptInstaller {

  public function run() {
    $this->aptInstall( 'php7.0-intl' );
  }

}

