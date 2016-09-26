<?php
use DrivDigital\Vagrant\Provisioners\ScriptInstaller;

class InstallPhpComposer extends ScriptInstaller {

  public function run() {
    $this->exec('curl -sS https://getcomposer.org/installer | sudo php -- --install-dir=/usr/local/bin --filename=composer');
  }

}

