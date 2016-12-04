<?php

class TestSetupPhp extends Test {

  public function run() {

    $userIni = __DIR__ .'/user.ini';
    $sampleIni = __DIR__ .'/fixtures/sample.ini';

    require_once '../class-setup-php.php';
    $setup_php = new Setup_Php( $userIni, [ $sampleIni ] );
    $setup_php->update_ini_settings();

    $this->assert( file_exists( $userIni ), 'user.ini should be created' );
    $user_ini_parsed = parse_ini_file( $userIni, true );
    $this->assert( $user_ini_parsed['box']['version'] == 7, 'user.ini should have the expected php version setting' );

    $parsed = parse_ini_file( 'fixtures/sample.ini' );
    $this->assert( $parsed['date.timezone'] == 'Europe/Oslo', 'date.timezone should have expected value' );
    $this->assert( $parsed['pdo_mysql.default_socket'] == '/var/run/mysqld/mysqld.sock', 'pdo_mysql.default_socket should be updated with the expected value' );
    $this->assert( $parsed['mysqli.default_socket'] == '/var/run/mysqld/mysqld.sock', 'mysqli.default_socket should be updated with the expected value' );
    $this->assert( $parsed['display_errors'] == '1', 'display_errors should be updated and have the expected value' );
    $this->assert( $parsed['log_errors'] == '1', 'log_errors should be updated the expected value' );
    $this->assert( $parsed['error_log'] == '/var/log/php-errors.log', 'error_log updated be added and have the expected value' );
    $this->assert( $parsed['mysql.default_socket'] == '/var/run/mysqld/mysqld.sock', 'mysql.default_socket should be added and have the expected value' );
    $this->assert( $parsed['tideways.auto_prepend_library'] == 0, 'tideways.auto_prepend_library should be added and and have the expected value' );

    // Clean up.
    unlink( $userIni );
    copy( "$sampleIni.bak", "$sampleIni" );

  }
}

