<?php

class File_Sync {

  static function setup() {

    $sync_dir = '/file-sync-sites';

    // Make node.js command available
    // todo: make node available by default and remove this?
    `sudo ln -s /usr/bin/nodejs /usr/bin/node`;

    // Install forever
    // Used later for starting the sync script as a service.
    echo "File Sync: Installing foreverjs/forever";
    `sudo npm install forever -g --silent`;

    // Copy site / files
    `sudo mkdir $sync_dir`;
    $sites = setup::get_sites();
    foreach ( $sites as $slug => $site ) {
      echo "File Sync: Copying files from /vagrant/$site to $sync_dir/$site";
      `cp -R /vagrant/$site $sync_dir`;
      // Remove git
      `rm -rf $sync_dir/$site/.git`;
    }

    // Set vagrant user as owner of the web root
    `sudo chown -R vagrant:vagrant $sync_dir`;

    echo "File Sync: Updating node dependencies";
    `cd setup/plugins/file-sync && npm install --silent`;

    echo "File Sync: Starting file sync watch";
    `forever start setup/plugins/file-sync/watch.js /vagrant $sync_dir`;

    // Use `forever stop watch.js` in order to stop the watch process.
  }
}
