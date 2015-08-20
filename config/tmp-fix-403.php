<?php

// A temporary fix for the 403 by apache
// (Until the box is rebuilt with a propper apache config file)
$file = '/etc/apache2/apache2.conf';
$conf = file_get_contents( $file );
$conf = str_replace( '/var/www/', '/vagrant', $conf );
file_put_contents( $file, $conf );
