<?php
use DrivDigital\Vagrant\Provisioners\ScriptInstaller;

class InstallLibApache2ModPhp56 extends ScriptInstaller {

  public function run() {
    $this->aptInstall( 'libapache2-mod-php5.6' );
  }

}

