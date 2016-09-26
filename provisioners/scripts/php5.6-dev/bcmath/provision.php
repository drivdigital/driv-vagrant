<?php
use DrivDigital\Vagrant\Provisioners\ScriptInstaller;

class InstallPhp56BcMath extends ScriptInstaller {

  public function run() {
    $this->aptInstall( 'php5.6-bcmath' );
  }

}

