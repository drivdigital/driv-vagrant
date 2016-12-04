<?php

class HostsParser {

    /**
     * @var string Path to the hosts file.
     */
    private $hostsFilePath;

    /**
     * @var array hosts file line entries.
     */
    private $lines;

    /**
     * Hosts constructor.
     * @param string $hostsFilePath
     * @throws Exception
     */
    public function __construct( $hostsFilePath ) {
        // Throw exception if the file does not exist exists or is not writable.
        if ( ! file_exists( $hostsFilePath ) ) {
            throw new Exception( 'File does not exist.' );
        }
        if ( ! is_writable( $hostsFilePath ) ) {
            throw new Exception( 'File is not writable.' );
        }

        $this->hostsFilePath    = $hostsFilePath;
        $this->originalContents = file_get_contents( $this->hostsFilePath );
        $this->lines            = $this->parseHostsFile();
    }

  /**
   * Returns true if a host line exists.
   * @param $ip string
   * @param $host string
   * @return bool
   */
    public function exists( $ip, $host ) {
        $entry = $this->getEntryInfo( $ip, $host );
        return $entry ? true: false;
    }

    /**
     * Adds a line to the hosts file.
     * @param $ip string
     * @param $host string
     */
    public function add( $ip, $host ) {
        $this->lines[] = $this->formatLine( $ip, $host );
    }

    /**
     * Removes a line to the hosts file.
     * @param $ip string
     * @param $host string
     */
    public function remove( $ip, $host ) {
        /* @todo */
    }

    /**
     * Activates a line based on ip and config.
     * @param $ip string
     * @param $host string
     */
    public function activate( $ip, $host ) {
        $entry = $this->getEntryInfo( $ip, $host );
        if ( ! $entry ) {
            return;
        }
        if ( $entry['activated'] ) {
            return;
        }
        $this->lines[$entry['index']] = $this->formatLine( $entry['ip'], $entry['hostname'] );
    }

    /**
     * De-activates a line based on ip and config.
     * @param $ip string
     * @param $host string
     */
    public function deactivate( $ip, $host ) {
        $entry = $this->getEntryInfo( $ip, $host );
        if ( ! $entry ) {
            return;
        }
        if ( ! $entry['activated'] ) {
            return;
        }
        $this->lines[$entry['index']] = "# " . $this->formatLine( $entry['ip'], $entry['hostname'] );
    }

    /**
     * Activate/Deactivate a line based on ip and config.
     * @param $ip string
     * @param $host string
     */
    public function toggle( $ip, $host ) {
        $entry = $this->getEntryInfo( $ip, $host );
        if ( ! $entry ) {
            return;
        }

        $this->lines[$entry['index']] = $entry['activated'] ?
                $this->formatLine( "# " . $entry['ip'], $entry['hostname'] ) :
                $this->formatLine( $entry['ip'], $entry['hostname'] );
    }

    /**
     * Saves the hosts file.
     */
    public function save() {
        $contents = '';
        foreach ( $this->lines as $line ) {
            $contents .= $line . PHP_EOL;
        }

        file_put_contents( $this->hostsFilePath, $contents );
    }

    /**
     * Returns a host file line formatted.
     * @param $ip string
     * @param $host string
     * @return string
     */
    public function formatLine( $ip, $host ) {
        return "$ip\t$host";
    }

    /**
     * Returns an array with data about an entry.
     * @param $ipToFind string
     * @param $hostToFind string
     * @return array
     */
    public function getEntryInfo( $ipToFind, $hostToFind ) {
        $result     = [];
        $ipToFind   = trim( $ipToFind );
        $hostToFind = trim( $hostToFind );
        $i          = 0;
        foreach ( $this->lines as $entry ) {
            if ( preg_match( "/$hostToFind/", $entry ) && preg_match( "/$ipToFind/", $entry ) ) {

                $parts    = preg_split( "/\s+/", $entry );
                $ip       = false;
                $hostname = false;
                foreach ( $parts as $part ) {
                    if ( preg_match( "/\d{1,3}\.\d{1,3}\.\d{1,3}/", $part ) ) {
                        $ip = trim( $part );
                    }
                    if ( ! preg_match( "/#+/", $part ) ) {
                        $hostname = trim( $part );
                    }
                }

                if ( $ip && $hostname ) {
                    $result = [
                            'index'     => $i,
                            'ip'        => $ip,
                            'hostname'  => $hostname,
                            'activated' => preg_match( "/(^\s|)+#/", $entry ) ? false : true,
                            'raw'       => $entry
                    ];
                    break;
                }
            }
            $i++;
        }
        return $result;
    }

    /**
     * Returns the hosts file lines as an array.
     * @return array
     */
    private function parseHostsFile() {
        $result   = [];
        $contents = file_get_contents( $this->hostsFilePath );
        $lines    = explode( PHP_EOL, $contents );
        foreach ( $lines as $line ) {
            $result[] = $line;
        }
        return $result;
    }

}