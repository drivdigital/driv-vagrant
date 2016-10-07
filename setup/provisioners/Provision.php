<?php

namespace DrivDigital\Vagrant\Provisioners;

class Provision {

  /**
   * For testing purposes.
   *
   * @var bool
   */
  public $debug;

  /**
   * Site setting. See config/<site>/config.json
   *
   * @var array
   */
  protected $siteSetting;

  /**
   * Array of scripts that a provisioner includes
   *
   * @var array
   */
  protected $includes = [];

  /**
   * Path to the current script
   *
   * @var string
   */
  private $selfPath;

  /**
   * Path to the scripts folder
   *
   * @var string
   */
  private $scriptsPath;

  /**
   * @var bool
   */
  private static $aptPackagesUpdated = false;

  /**
   * Keeps track of loaded scripts.
   *
   * @var array
   */
  private static $loadedScripts = [];

  /**
   * Keeps track of instantiated classes.
   *
   * @var array
   */
  private static $instantiatedClasses = [];

  /**
   * Keeps track of added apt repositories.
   *
   * @var array
   */
  private static $addedAptRepositories = [];

  /**
   * Keeps track of installed apt packages. No need to install a package twice.
   *
   * @var array
   */
  private static $installedAptPackages = [];

  /**
   * ScriptRunner constructor.
   *
   * @param array $siteSetting
   */
  public function __construct( $siteSetting ) {
    $reflection = new \ReflectionClass( $this );

    $this->debug       = getenv( 'VAGRANT_PROVISIONER_DEBUG' ) == 1;
    $this->siteSetting = $siteSetting;
    $this->scriptsPath = dirname( __FILE__ ) . '/scripts';
    $this->selfPath    = dirname( $reflection->getFileName() );
  }

  /**
   * Installs a script.
   *
   * @param string $provisionScript
   */
  public function install( $provisionScript ) {

    $path = $this->scriptsPath . '/' . $provisionScript;

    // Return if the script does not exist.
    if ( ! file_exists( $path ) ) {
      $this->notice( "$provisionScript does not exist." );
      return;
    }

    // Return if already loaded.
    if ( ! in_array( $provisionScript, self::$loadedScripts ) ) {
      require $path . '/provision.php';
      self::$loadedScripts[] = $provisionScript;
    }
    else {
      $this->notice( "$provisionScript is already installed." );
    }

    // Instantiate new classes.
    $classes = get_declared_classes();
    foreach ( $classes as $class ) {

      // Continue if already instantiated.
      if ( in_array( $class, self::$instantiatedClasses ) ) {
        continue;
      }

      // Must extend Script Runner.
      if ( ! is_subclass_of( $class, '\DrivDigital\Vagrant\Provisioners\Provision' ) ) {
        continue;
      }

      /**
       * @var \DrivDigital\Vagrant\Provisioners\Provision $provision
       */
      $provision = new $class( $this->siteSetting );

      // Must have the method run()
      if ( ! method_exists( $provision, 'run' ) ) {
        continue;
      }

      if ( method_exists( $provision, 'beforeRun' ) ) {
        $provision->beforeRun();
      }

      self::$instantiatedClasses[] = $class;

      // Install any dependencies first.
      if ( $provision->includes && ! empty( $provision->includes ) ) {
        foreach ( $provision->includes as $scriptDependency ) {
          $this->install( $scriptDependency );
        }
      }

      $this->fancyBanner( $provisionScript );

      $provision->run();

      if ( method_exists( $provision, 'runAfter' ) ) {
        $provision->runAfter();
      }

      $this->log( ' ' );
    }
  }

  /**
   * @param string $str
   */
  public function log( $str ) {
    $s = trim( $str );
    if ( ! empty( $s ) ) {
      echo "Provisioner: $str\n";
    }
  }

  public function notice( $str ) {
    $s = trim( $str );
    if ( ! empty( $s ) ) {
      echo "\033[31mProvisioner: $str \e[0m\n";
    }
  }

  /**
   * Updates apt packages.
   * Ideally this should only be done once during provision.
   * Use the force to re-run the update.
   *
   * @param bool $force
   */
  public function aptUpdateList( $force = false ) {
    if ( self::$aptPackagesUpdated && ! $force ) {
      $this->notice( 'Package list already updated.' );
      return;
    }
    $this->log( 'Updating apt list...' . ( $force ? ' (forced)' : '' ) );
    $this->exec( 'sudo apt-get update' );
    self::$aptPackagesUpdated = true;
  }

  public function getAddedScripts() {
    return self::$loadedScripts;
  }

  /**
   * Returns the name of the site running the script.
   *
   * @return string
   */
  protected function getSite() {
    return $this->siteSetting['name'];
  }

  /**
   * Returns the slug of the site running the script.
   *
   * @return string
   */
  protected function getSlug() {
    return $this->siteSetting['slug'];
  }

  /**
   * Returns the path to the script.
   *
   * @return string
   */
  protected function getSelfPath() {
    return $this->selfPath;
  }

  /**
   * Executes a shell command.
   *
   * @param string $command
   */
  protected function exec( $command ) {
    $c = trim( $command );
    if ( $this->debug ) {
      $this->log( $c );
      return;
    }
    $this->log( $c );
    $this->log( exec( $c ) );
  }

  /**
   * Adds a repository to apt.
   *
   * @param $repo
   */
  protected function aptAddRepository( $repo ) {
    $r = trim( $repo );
    if ( in_array( $r, self::$addedAptRepositories ) ) {
      $this->notice( "Repository $r is already added." );
      return;
    }
    $this->log( 'Adding apt repository: ' . $r );

    $this->exec( 'echo "" | sudo LC_ALL=C.UTF-8 add-apt-repository ppa:' . $r . ' > /dev/null 2>&1' );

    self::$addedAptRepositories[] = $r;
  }

  /**
   * Install package with apt-get
   *
   * @param string $package
   */
  protected function aptInstall( $package ) {
    $p = trim( $package );
    if ( in_array( $p, self::$installedAptPackages ) ) {
      $this->log( "$p is already installed." );
      return;
    }
    $this->exec( 'sudo apt-get --yes --force-yes install ' . $p );
    self::$installedAptPackages[] = $p;
  }

  /**
   * Returns true if the shell command exists;
   *
   * @param $command
   * @return bool
   */
  protected function which( $command ) {
    return shell_exec( 'which ' . $command );
  }

  protected function fancyBanner( $str ) {

    $repeats = 75 - strlen( $str );
    $result  = '--- ' . $str . ' ' . str_repeat( '-', $repeats );
    $this->log( $result );
  }

}