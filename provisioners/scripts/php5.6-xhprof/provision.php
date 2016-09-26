<?php
use DrivDigital\Vagrant\Provisioners\ScriptInstaller;

class InstallXhprof extends ScriptInstaller {

  protected $dependsOn = [
      'php-pear',
      'php5.6-dev',
      'php-composer',
      'mongo-db'
  ];

  public function run() {

    // Enable mongo db for PHP.
    $this->exec( 'echo "" | sudo pecl install -f mongo' );
    $this->exec( 'sudo sh -c "echo \'extension=mongo.so\' >> /etc/php/5.6/mods-available/mongo.ini"' );
    $this->exec( 'sudo phpenmod -v5.6 mongo' );

    // Install and enable XHProf.
    $this->exec( 'sudo pecl install xhprof-beta' );
    $this->exec( 'sudo sh -c "echo \'extension=xhprof.so\' >> /etc/php/5.6/mods-available/xhprof.ini"' );
    $this->exec( 'sudo phpenmod -v5.6 xhprof' );

    // Deploy xhgui.
    $site       = $this->getSite();
    $site_path  = "/vagrant/$site";
    $xhgui_path = "$site_path/xhgui";

    if ( ! file_exists( $xhgui_path ) ) {
      chdir( $site_path );
      // Download for now. Could be fetched with composer in order to avoid the extra install php step.
      $this->exec( 'wget https://github.com/perftools/xhgui/archive/v0.4.0.tar.gz >/dev/null 2>&1' );
      $this->exec( 'tar xfz v0.4.0.tar.gz' );
      unlink( "v0.4.0.tar.gz" );
      rename( "xhgui-0.4.0", "xhgui" );
      copy( dirname( __FILE__ ) . "/xhgui.config.php", "$xhgui_path/config/config.php" );
      chdir( $xhgui_path );
      $this->exec( 'chmod -R 0777 cache' );

      $this->exec( 'composer update --prefer-dist' );
    }
    else {
      $this->log( "Seems like XHGui is already deployed." );
      $this->log( "You may run composer update." );
    }

    $this->log("^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^");
    $this->log("xhprof;");
    $this->log("Add the following line to $site vhost file in order to automatically enable xhprof for the site\n");
    $this->log("  php_admin_value auto_prepend_file '/vagrant/" . $site . "/xhgui/external/header.php");
    $this->log("^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^");

    $this->log("xhgui available at: http://$site:8080/xhgui");

  }

}

