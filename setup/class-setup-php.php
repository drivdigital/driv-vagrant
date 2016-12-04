<?php

// Defaults.
const PHP_SETUP_DEFAULTS = <<<EOD
[box]
version=7

[settings]
date.timezone=Europe/Oslo
sendmail_path=/home/vagrant/.rbenv/shims/catchmail -f vagrant@drivdigital.dev
pdo_mysql.default_socket=/var/run/mysqld/mysqld.sock
mysqli.default_socket=/var/run/mysqld/mysqld.sock
mysql.default_socket=/var/run/mysqld/mysqld.sock
display_errors="1"
log_errors="1"
error_log=/var/log/php-errors.log
tideways.auto_prepend_library=0
EOD;

class Setup_Php {

  /**
   * @var string Path to the php-config.ini
   */
  private $user_ini_file;

  /**
   * @var array User ini config, parsed
   */
  private $user_config;

  /**
   * @var array
   */
  private $box_ini_files = [];

  public function __construct( $user_ini_file, $box_ini_files ) {
    // Create user config file if it has not been created.
    if ( ! file_exists( $user_ini_file ) ) {
      file_put_contents( $user_ini_file, PHP_SETUP_DEFAULTS );
    }

    $this->user_ini_file = $user_ini_file;
    $this->user_config   = parse_ini_file( $this->user_ini_file, true );
    $this->box_ini_files = $box_ini_files;
  }

  /**
   * Read the user's config and update the box's php inis.
   * @return $this
   */
  public function update_ini_settings() {
    foreach ( $this->user_config['settings'] as $key => $value ) {
      foreach ( $this->box_ini_files as $box_ini ) {
        $this->update_ini_setting( $box_ini, $key, $value );
      }
    }
    return $this;
  }

  /**
   * Updates a setting in the ini file.
   * @param $ini_file
   * @param $key
   * @param $value
   * @return $this
   */
  public function update_ini_setting( $ini_file, $key, $value ) {
    if ( file_exists( $ini_file ) ) {
      // Create a back-up.
      copy( $ini_file, $ini_file . '.bak' );
      $contents = file_get_contents( $ini_file );
      $contents = preg_replace( "/^[;|\s]*$key.+?$/m", "\n" . $key . " = " . $value, $contents, -1, $count );

      if ( $count == 0 ) {
        $contents .= "\n$key = $value";
      }

      file_put_contents( $ini_file, $contents );
    }
    return $this;
  }

  /**
   * Switches the php version.
   * @return $this
   */
  public function switch_php_version() {
    // Switch the version on the box.
    $php_version = $this->user_config['box']['version'];
    echo `/home/vagrant/scripts/phpswitch.sh $php_version`;
    return $this;
  }
}

if ( isset( $argv[1] ) && $argv[1] == 'provision' ) {
  $php_setup = new Setup_Php( '/vagrant/config/php-config.ini', [
      '/home/vagrant/.phpbrew/php/php-5.6.26/etc/php.ini',
      '/home/vagrant/.phpbrew/php/php-7.0.11/etc/php.ini'
  ] );
  $php_setup->update_ini_settings()->switch_php_version();
}


