<?php
use DrivDigital\Vagrant\Provisioners\ScriptInstaller;

class InstallPhp56Dev extends ScriptInstaller {

  public $dependsOn = [
      'libapache2-mod-php5.6',
      'php5.6-common',
      'php5.6-gd',
      'php5.6-mysql',
      'php5.6-mcrypt',
      'php5.6-curl',
      'php5.6-intl',
      'php5.6-xsl',
      'php5.6-mbstring',
      'php5.6-zip',
      'php5.6-bcmath',
      'php5.6-iconv',
      'php5.6-xml'
  ];

  public function runBefore() {
    $this->aptInstall( 'python-software-properties' );
    $this->aptAddRepository( 'ondrej/php' );
    $this->aptUpdateList( true );
    $this->exec( 'sudo apt-get purge php5-common -y' );
  }

  public function run() {
    $this->aptInstall( 'php5.6-dev' );

    // Somehow apt-get install php5.6 makes the cli php version 7.0.
    // Switch to 5.6 on the command line.
    $this->exec('sudo ln -sfn /usr/bin/php5.6 /etc/alternatives/php');
  }


}
