<?php

namespace Medieval\Framework\Database;

class Statement {

    /** @var \PDOStatement */
    private $statement;

    public function __construct( \PDOStatement $statement ) {
        $this->statement = $statement;
    }

    public function fetch( $fetchStyle = \PDO::FETCH_ASSOC ) {
        return $this->statement->fetch( $fetchStyle );
    }

    public function fetchAll( $fetchStyle = \PDO::FETCH_ASSOC ) {
        return $this->statement->fetchAll( $fetchStyle );
    }

    public function bindParam( $parameter, $variable, $dataType = \PDO::PARAM_STR, $length ) {
        return $this->statement->bindParam( $parameter, $variable, $dataType, $length );
    }

    public function execute( array $parameters = null ) {
        return $this->statement->execute( $parameters );
    }

    public function rowCount() {
        return $this->statement->rowCount();
    }
}