<?php

namespace Medieval\Framework\Drivers;

abstract class DriverAbstract {

    protected $user;
    protected $password;
    protected $databaseName;
    protected $host;

    public function __construct( $user, $password, $databaseName, $host = null ) {
        $this->user = $user;
        $this->password = $password;
        $this->databaseName = $databaseName;
        $this->host = $host;
    }

    /**
     * @return string
     */
    public abstract function getDsn();
}