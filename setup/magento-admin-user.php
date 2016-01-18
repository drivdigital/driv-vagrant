<?php
defined( 'PROVISION' ) || die();

$sql = file_get_contents( "setup/magento-admin-user.sql" );
$sql = strtr( $sql, [
  '%SLUG' => @$slug
] );
file_put_contents( "config/$slug/.admin-user.sql", $sql );

$this_file = file_get_contents( __FILE__ );
$code_pos = strpos( $this_file, '//'.' INSERTED CODE:' );
$code_to_insert = substr( $this_file, $code_pos );

$index_file = file_get_contents( "$site/index.php" );
$code_pos = strpos( $index_file, '//'.' INSERTED CODE:' );
if ( FALSE === $code_pos ) {
  echo "Inserting self-installer in '$site/index.php'\n";
  $code_to_insert = strtr( $code_to_insert, [
    '%SLUG' => $slug,
  ] );
  $index_file .= "\n$code_to_insert";
  file_put_contents( "$site/index.php", $index_file );
}
else {
  echo "Code is already inserted at $code_pos in file \n";
}

/*

// INSERTED CODE:
// Self installer - @see setup/magento-admin-user.php

`mysql -u root %SLUG < /vagrant/config/%SLUG/.admin-user.sql && rm /vagrant/config/%SLUG/.admin-user.sql`;
// Revert the inserted code:
$this_file = file_get_contents( __FILE__ );
$code_pos = strpos( $this_file, '//'.' INSERTED CODE:' );
$this_file = substr( $this_file, 0, $code_pos -1 );
file_put_contents( __FILE__, $this_file );

/* Self installer - end */
