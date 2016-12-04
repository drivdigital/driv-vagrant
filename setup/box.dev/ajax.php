<?php
header( 'Content-Type: text/plain' );

function getAction() {
  $headers = getallheaders();
  return $headers['x-action'];
}

function result( $status ) {
  echo $status;
}

$action = getAction();

if ( ! $action ) {
  echo "x-action header missing";
  die();
}

if ( $action == 'uptime' ) {
  echo shell_exec( 'uptime' );
}
