<?php
use DrivDigital\Vagrant\Provisioners\ScriptInstaller;

class InstallLibApache2ModPhp7 extends ScriptInstaller {

  public function run() {
    $this->aptInstall( 'libapache2-mod-php7.0' );
  }

}

