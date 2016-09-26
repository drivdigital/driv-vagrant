<?php
use DrivDigital\Vagrant\Provisioners\ScriptInstaller;

class InstallXdebug extends ScriptInstaller {

  public function run() {

    $php_version = `php -v`;

    if ( strpos( strtolower( $php_version ), 'xdebug' ) === false ) {
      $this->aptInstall( 'php5-xdebug' );

      $this->exec( 'sudo sh -c "echo \'[xdebug]\' >> /etc/php5/apache2/php.ini"' );
      $this->exec( 'sudo sh -c "echo \'zend_extension="/usr/lib/php5/20121212/xdebug.so"\' >> /etc/php5/apache2/php.ini"' );
      $this->exec( 'sudo sh -c "echo \'xdebug.remote_enable=1\' >> /etc/php5/apache2/php.ini"' );
      $this->exec( 'sudo sh -c "echo \'xdebug.remote_host=10.0.2.2\' >> /etc/php5/apache2/php.ini"' );
      $this->exec( 'sudo sh -c "echo \'xdebug.remote_port=9000\' >> /etc/php5/apache2/php.ini"' );
    }

  }

}

