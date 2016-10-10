<?php

class TestSystem extends Test {

  public function run() {
    echo "  System\n";
    $zshrc        = file_get_contents( '/home/vagrant/.zshrc' );
    $bashrc       = file_get_contents( '/home/vagrant/.bashrc' );
    $dbConnection = new mysqli( 'localhost', 'root', '' );
    $this->assert( $zshrc != '', 'oh my zsh installed' );
    $this->assert( strpos( $zshrc, '[[ -e ~/.phpbrew/bashrc ]] && source ~/.phpbrew/bashrc' ) !== false, 'phpbrew should be set up in .zshrc', [ 'Used by phpbrew' ] );
    $this->assert( strpos( $bashrc, '[[ -e ~/.phpbrew/bashrc ]] && source ~/.phpbrew/bashrc' ) !== false, 'phpbrew should be set up in .bashrc', [ 'Used by phpbrew' ] );
    $this->assert( strpos( $zshrc, 'cd /vagrant' ) !== false, '.zshrc should cd /vagrant' );
    $this->assert( strpos( $bashrc, 'cd /vagrant' ) !== false, '.bashrc should cd /vagrant' );
    $this->assert( strpos( $bashrc, 'export PATH="$HOME/.rbenv/bin:$PATH"' ) !== false, '.bashrc should have Ruby environment path', [ 'Used by mailcatcher' ] );
    $this->assert( strpos( $zshrc, 'export PATH="$HOME/.rbenv/bin:$PATH"' ) !== false, '.zsh should have Ruby environment path', [ 'Used by mailcatcher' ] );
    $this->assert( strpos( $bashrc, '$(rbenv init -)' ) !== false, '.bashrc should have $(rbenv init -) command' );
    $this->assert( strpos( $zshrc, '$(rbenv init -)' ) !== false, '.zsh should have $(rbenv init -) command' );
    $this->assert( preg_match( '/DISABLE_UNTRACKED_FILES_DIRTY[\s|]*=[\s|]*"true"/m', $zshrc ), '.zshrc DISABLE_UNTRACKED_FILES_DIRTY should be true' );
    $this->assert( $dbConnection->connect_errno === 0, 'Should be possible to login to mysql without password' );
    $this->assert( trim( file_get_contents( '/etc/timezone' ) ) == 'UTC', 'System should have the expected timezone.' );
    $this->assert( Vagrant::commandExist( 'node' ), 'Node.js should be installed' );
    $this->assert( Vagrant::commandExist( 'composer' ), 'composer command should be available' );
    $this->assert( Vagrant::commandExist( 'wp' ), 'wp command should be available (wp-cli)' );
    $this->assert( Vagrant::commandExist( 'mongod' ), 'mongod command should be available' );
    $this->assert( Vagrant::commandExist( 'phpbrew' ), 'phpbrew command should be available' );
    $this->assert( Vagrant::commandExist( 'mailcatcher' ), 'mailcatcher command should be available' );
    $this->assert( Vagrant::commandExist( 'phpswitch' ), 'phpswitch command should be available', [ 'Command Used for simultaneously switch php on cli, apache and change xhgui repo' ] );

    echo "  PHP " . phpversion() . "\n";
    $loadedPhpExtensions = get_loaded_extensions();
    $this->assert( file_exists( '/vagrant/config/php-config.ini' ), 'php-config.ini should have been created (/vagrant/setup/setup-php.php)' );
    $this->assert( in_array( 'openssl', $loadedPhpExtensions ), 'PHP openssl extension should have loaded' );
    $this->assert( in_array( 'mbstring', $loadedPhpExtensions ), 'PHP mbstring extension should have loaded' );
    $this->assert( in_array( 'PDO', $loadedPhpExtensions ), 'PHP PDO extension should have loaded' );
    $this->assert( in_array( 'pdo_mysql', $loadedPhpExtensions ), 'PHP pdo_mysql extension should have loaded' );
    $this->assert( in_array( 'mysqli', $loadedPhpExtensions ), 'PHP mysqli extension should have loaded' );
    $this->assert( in_array( 'mysqlnd', $loadedPhpExtensions ), 'PHP mysqlnd extension should have loaded' );
    $this->assert( in_array( 'soap', $loadedPhpExtensions ), 'PHP soap extension should have loaded' );
    // PHP 5 and PHP 7 uses different mongo db drivers.
    $this->assert( in_array( ( phpversion() == '5.6.26' ? 'mongo' : 'mongodb' ), $loadedPhpExtensions ), 'PHP mongodb extension should have loaded', [ 'PHP 5 uses mongo and PHP7 uses mongodb' ] );
    $this->assert( in_array( 'tideways', $loadedPhpExtensions ), 'PHP tideways extension should have loaded' );
    $this->assert( ini_get( 'date.timezone' ) == 'Europe/Oslo', 'php.ini: date.timezone should have expected result.' );
    $this->assert( ini_get( 'pdo_mysql.default_socket' ) == '/var/run/mysqld/mysqld.sock', 'php.ini: pdo_mysql.default_socket should have expected result.' );
    $this->assert( ini_get( 'mysqli.default_socket' ) == '/var/run/mysqld/mysqld.sock', 'php.ini: mysqli.default_socket should have expected result.' );
    $this->assert( ini_get( 'display_errors' ) == '1', 'php.ini: display_errors should have expected result.' );
    $this->assert( ini_get( 'log_errors' ) == '1', 'php.ini: log_errors should have expected result.' );
    $this->assert( ini_get( 'error_log' ) == '/var/log/php-errors.log', 'php.ini: error_log should have expected result.' );
    $this->assert( ini_get( 'tideways.auto_prepend_library' ) == '0', 'php.ini: tideways.auto_prepend_library should have expected result.' );

    echo "  Apache\n";
    $loadedApacheModules = Vagrant::getLoadedApacheModules();
    $enabledVhosts       = Vagrant::getEnabledApacheVhosts();
    $this->assert( in_array( 'Vagrant', $enabledVhosts ), 'Default ServerName Vagrant should be in enabled vhosts' );

    $this->assert( strpos( shell_exec( 'curl -I -s box.dev' ), 'HTTP/1.1 200 OK' ) !== false, 'box.dev should be running' );
    $this->assert( strpos( shell_exec( 'curl -I -s phpmyadmin.dev' ), 'HTTP/1.1 200 OK' ) !== false, 'phpmyadmin.dev should be running' );
    $this->assert( strpos( shell_exec( 'curl -I -s logs.dev' ), 'HTTP/1.1 200 OK' ) !== false, 'logs.dev should be running' );
    $this->assert( strpos( shell_exec( 'curl -I -s profiler.dev' ), 'HTTP/1.1 200 OK' ) !== false, 'logs.dev should be running' );
    $this->assert( strpos( shell_exec( 'curl -I -s mail.dev' ), 'HTTP/1.1 200 OK' ) !== false, 'mail.dev should be running' );

    $this->assert( in_array( 'proxy_module', $loadedApacheModules ), 'Apache proxy_module should have loaded' );
    $this->assert( in_array( 'proxy_http_module', $loadedApacheModules ), 'Apache proxy_http_module should have loaded' );
    $this->assert( in_array( 'proxy_wstunnel_module', $loadedApacheModules ), 'Apache proxy_wstunnel_module should have loaded' );
    $this->assert( in_array( 'rewrite_module', $loadedApacheModules ), 'Apache rewrite_module should have loaded' );

    echo "  Misc\n";
    $vagrantFile = file_get_contents( '/vagrant/Vagrantfile' );
    $this->assert( is_readable( '/var/log/apache2/access.log' ), 'Apache access log should be readable', [ 'Used by PimpMyLog' ] );
    $this->assert( is_readable( '/var/log/apache2/error.log' ), 'Apache error log should be readable (pimpmylog)', [ 'Used by PimpMyLog' ] );

    $this->assert( strpos( $vagrantFile, 'config.vm.define "vm1", primary: true do |vm1|' ), 'Vagrantfile should have vm1' );
    $this->assert( strpos( $vagrantFile, 'config.vm.define "vm2", autostart: false do |vm2|' ), 'Vagrantfile should have vm2' );
    $this->assert( strpos( $vagrantFile, '"private_network", ip: "192.168.33.10"' ), 'Vagrantfile should have expected private network ip address' );

  }

}


