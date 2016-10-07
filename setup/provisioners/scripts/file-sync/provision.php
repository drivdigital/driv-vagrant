<?php
use DrivDigital\Vagrant\Provisioners\Provision;

class InstallFileSync extends Provision {

  public function run() {

    // Path to where files will
    $base_sync_dir = '/file-sync-sites';

    $self_path = $this->getSelfPath();

    // Make node.js command available
    if ( ! file_exists( '/usr/bin/node' ) ) {
      $this->exec( 'sudo ln -s /usr/bin/nodejs /usr/bin/node' );
    }

    // Install forever
    // Used later for starting the sync script as a service.
    $has_forever = $this->which( 'forever' );

    if ( ! $has_forever ) {
      $this->log( "Installing foreverjs/forever" );
      $this->exec( 'sudo npm install forever -g --silent' );
    }

    // Copy site / files
    if ( ! file_exists( $base_sync_dir ) ) {
      $this->exec( "sudo mkdir $base_sync_dir" );
    }

    $site = $this->getSite();
    $this->log( "Copying files from /vagrant/$site to $base_sync_dir/$site" );
    $this->exec( "cp -R /vagrant/$site $base_sync_dir" );
    // Remove git directory.
    $this->exec( "rm -rf $base_sync_dir/$site/.git" );
    // Set vagrant user as owner of the web root
    $this->exec( "sudo chown -R vagrant:vagrant $base_sync_dir" );

    $this->log( "Updating node dependencies" );
    chdir( $self_path );
    $this->exec( "npm install --silent" );

    //@todo check if node / forever is already running.
    $this->log( "Starting file sync watch" );

    // @todo: https://github.com/foreverjs/forever/issues/483
    $this->exec( "forever start watch.js /vagrant $base_sync_dir" );

    // Use `forever stop watch.js` in order to stop the watch process.
  }

}

