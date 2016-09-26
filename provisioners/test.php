<?php

putenv( "VAGRANT_TEST=1" );

require_once( 'Provisioner.php' );
//
//$sitesJson = '[
//  {
//    "package": "",
//    "name": "logfetcher.dev",
//    "git": "git@github.com:drivdigital\/driv-vagrant.git",
//    "provisioners": ["php-7.0"]
//  }, {
//    "package": "",
//    "name": "logfetcher2.dev",
//    "git": "git@github.com:drivdigital\/driv-vagrant.git",
//    "provisioners": ["file-sync"]
//  }
//]';

$sitesJson = '[
  {
    "package": "",
    "name": "logfetcher.dev",
    "git": "git@github.com:drivdigital\/driv-vagrant.git",
    "provisioners": ["xhprof"]
  }
   
]';

DrivDigital\Vagrant\Provisioners\Provisioner::install( json_decode( $sitesJson, true ) );

