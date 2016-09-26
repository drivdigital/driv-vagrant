<?php
use DrivDigital\Vagrant\Provisioners\ScriptInstaller;

class InstallPhpPear extends ScriptInstaller {

  public function run() {
    $this->aptInstall( 'php-pear' );
  }
}