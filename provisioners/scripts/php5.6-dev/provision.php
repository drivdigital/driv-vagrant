<?php
use DrivDigital\Vagrant\Provisioners\ScriptInstaller;

class InstallPhp56Dev extends ScriptInstaller {

  public $include = [
      'php5.6-dev/libapache2-mod-php5.6',
      'php5.6-dev/common',
      'php5.6-dev/gd',
      'php5.6-dev/mysql',
      'php5.6-dev/mcrypt',
      'php5.6-dev/curl',
      'php5.6-dev/intl',
      'php5.6-dev/xsl',
      'php5.6-dev/mbstring',
      'php5.6-dev/zip',
      'php5.6-dev/bcmath',
      'php5.6-dev/iconv',
      'php5.6-dev/xml'
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
