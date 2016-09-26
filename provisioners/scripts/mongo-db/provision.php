<?php
use DrivDigital\Vagrant\Provisioners\ScriptInstaller;

class InstallMongo extends ScriptInstaller {

  public function run() {
    $this->aptInstall( 'mongodb' );
  }

}

