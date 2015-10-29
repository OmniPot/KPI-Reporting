<?php

namespace Medieval\Framework\Drivers;

class DriverFactory {

    /**
     * @param string $driverName
     * @param $user
     * @param $password
     * @param $databaseName
     * @param $host
     * @return DriverAbstract
     * @throws \Exception
     */
    public static function create( $driverName = 'default', $user, $password, $databaseName, $host ) {
        if ( !$driverName ) {
            throw new \Exception( 'Invalid driver specified for creation' );
        }

        switch ( $driverName ) {
            case 'mysql':
                return new MySQLDriver( $user, $password, $databaseName, $host );
                break;
            default:
                return new MySQLDriver( $user, $password, $databaseName, $host );
                break;
        }
    }
}