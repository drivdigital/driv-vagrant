<?php
use DrivDigital\Vagrant\Provisioners\ScriptInstaller;

class InstallPhp56MbString extends ScriptInstaller {

  public function run() {
    $this->aptInstall( 'php5.6-mbstring' );
  }

}

