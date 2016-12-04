<?php

$tools_urls = [
    'PhpMyAdmin' => 'phpmyadmin.dev',
    'Logs'       => 'logs.dev',
    'Mail'       => 'mail.dev',
    'Profiler'   => 'profiler.dev',
];

function create_url( $host ) {
  $port_forwarding = $_SERVER['SERVER_PORT'] != 80;
  $protocol = ((!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off') ||
      $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
  return $protocol . $host . ( $port_forwarding ? ':8080' : '' );
}

function get_sites() {
  $sites = glob( '/vagrant/*.dev' );
  return array_map( function ( $site ) {
    return create_url( str_replace( '/vagrant/', '', $site ) );
  }, $sites );
}

function get_box_name() {
  $content = file_get_contents( '/vagrant/Vagrantfile' );
  preg_match( '/config\.vm\.box\s*=\s*"(.+)"/', $content, $matches );
  if ( ! ( $matches && $matches[1] ) ) {
    return false;
  }
  return $matches[1];
}