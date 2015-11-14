<?php

namespace KPIReporting\Framework\Database;

use KPIReporting\Framework\Drivers\DriverFactory;

class Database {

    /** @var Database[] $_instances */
    private static $_instances = [ ];

    /** @var \PDO $database */
    private $database = null;

    private function __construct( \PDO $pdo ) {
        $this->database = $pdo;
    }

    public static function setInstance( $instanceName, $driver, $user, $password, $databaseName, $host = null ) {
        $driver = DriverFactory::create( $driver, $user, $password, $databaseName, $host );
        $pdo = new \PDO( $driver->getDsn(), $user, $password );

        self::$_instances[ $instanceName ] = new self( $pdo );
    }

    /**
     * @param string $instanceName
     * @return Database
     * @throws \Exception
     */
    public static function getInstance( $instanceName = 'default' ) {
        if ( !isset( self::$_instances[ $instanceName ] ) ) {
            throw new \Exception( 'Invalid database configuration name specified' );
        }

        return self::$_instances[ $instanceName ];
    }

    /**
     * @param $statement
     * @param array $driverOptions
     * @return Statement
     */
    public function prepare( $statement, array $driverOptions = [ ] ) {
        $statement = $this->database->prepare( $statement, $driverOptions );

        return new Statement( $statement );
    }

    public function query( $query ) {
        $this->database->query( $query );
    }

    public function lastId( $name = null ) {
        return $this->database->lastInsertId( $name );
    }
}