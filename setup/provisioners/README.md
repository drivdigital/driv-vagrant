# Provisioners

TBD

## Usage

Add script (directory name) to your `<site>/vagrant-config/config.json` / `provisioners` array.

`"provisioners" : ["file-sync"]`

Full example config.json:
```
{
  "package": "",
  "name": "site.dev",
  "git": "git@github.com:drivdigital\/site.git",
  "provisioners": [
    "file-sync" 
  ]
}
```

If the provisioner scripts exists, this will install `file-sync` during 
the vagrant up provision step.

## Hacking 

Add a new provision script

1. Create a new directory in the scripts folder. Eg. `scripts/postgresql`
2. Create a file named `provision.php` in the directory.

scripts/postgresql/provision.php:
```php
<?php
use DrivDigital\Vagrant\Provisioners\Provision;

/**
 * Sample Installer.
 */
class InstallPgSql extends Provision {

  /**
   * @var array
   * Optional. Array of scripts to include. These will be installed first.
   */
  public $include = [];

  /**
   * Optional. Runs before #includes and #run()
   */
  public function runBefore() {
  }

  /**
   * Required. Will be called after #ncludes has been installed
   */
  public function run() {

    // It's recomended to use the ScriptInstaller's methods as it will  
    // keep track of installed packages etc. in order to not install 
    // the same package twice, and fix some of the kludges in the OS.  

    // add-apt-repository
    $this->aptAddRepository( 'ondrej/php' );

    // apt-get install
    $this->aptInstall( 'postgresql' );
    $this->aptInstall( 'postgresql-contrib' );

    // Log something to the console.
    $this->log( 'PostgreSQL installed' );

    // Execute a command.
    $this->exec( 'sudo sh -c "echo \'Hello World\' >> /vagrant/hello.txt"' );

    // The instance has access to #siteConfig which has the 
    // configuration array for the current site.
    $siteName = $this->siteConfig['name'];

    // Full path to the script's directory.
    $selfPath = $this->selfPath;

    // PHP
    if ( file_exists( "$selfPath/save-pgsql-db.sh" ) ) {
      copy( "$selfPath/save-pgsql-db.sh", "/vagrant/$siteName/save-pgsql-db.sh" );
    }

    // Which
    if ( $this->which( 'node' ) ) {
      echo "has node";
    }
  }
}
```
