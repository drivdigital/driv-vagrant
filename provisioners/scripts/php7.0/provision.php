<?php
use DrivDigital\Vagrant\Provisioners\ScriptInstaller;

class InstallPhp7 extends ScriptInstaller {

  public $dependsOn = [
      'libapache2-mod-php7.0',
      'php7.0-common',
      'php7.0-gd',
      'php7.0-mysql',
      'php7.0-mcrypt',
      'php7.0-curl',
      'php7.0-intl',
      'php7.0-xsl',
      'php7.0-mbstring',
      'php7.0-zip',
      'php7.0-bcmath',
      'php7.0-iconv',
      'php7.0-xml'
  ];

  public function runBefore() {
    $this->aptInstall( 'python-software-properties' );
    $this->aptAddRepository( 'ondrej/php' );
    $this->aptUpdateList( true );
    $this->exec( 'sudo apt-get purge php5-common -y' );
  }

  public function run() {
    $this->aptInstall( 'php7.0' );
  }

}
