<?php

// A temporary way to add wpcli
// (Until the box is rebuilt with a propper apache config file)
if ( !file_exists( 'wp-cli.phar' ) )
  `curl -O https://raw.githubusercontent.com/wp-cli/builds/gh-pages/phar/wp-cli.phar -s`;
if ( file_exists( 'wp-cli.phar' ) && !file_exists( '/usr/local/bin/wp' ) )
  `sudo ln -s /vagrant/wp-cli.phar /usr/local/bin/wp`;
