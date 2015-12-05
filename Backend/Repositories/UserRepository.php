<?php

namespace KPIReporting\Repositories;

use KPIReporting\Queries\SelectQueries;
use KPIReporting\Exceptions\ApplicationException;
use KPIReporting\Framework\BaseRepository;

class UserRepository extends BaseRepository {

    const DEFAULT_USER_ROLE_ID = 1;

    private static $_instance = null;

    protected function __construct() {
        parent::__construct();
    }

    public function login( $username, $password ) {
        $query = SelectQueries::GET_LOGIN_DATA;
        $result = $this->getDatabaseInstance()->prepare( $query );
        $result->execute( [ $username ] );

        if ( $result->rowCount() == 0 ) {
            throw new ApplicationException( 'Login failed', 400 );
        }

        $userRow = $result->fetch();
        if ( !password_verify( $password, $userRow[ 'password' ] ) ) {
            throw new ApplicationException( 'Login failed', 400 );
        }

        return [
            'id' => $userRow[ 'id' ],
            'username' => $username,
            'role' => $userRow[ 'role' ]
        ];
    }

    public function getLoggedUserInfo() {
        $query = SelectQueries::GET_LOGGED_USER_INFO;
        $result = $this->databaseInstance->prepare( $query );
        $result->execute( [ $_SESSION[ 'id' ] ] );

        return $result->fetch();
    }

    public function getAllUsers() {
        $usersQuery = SelectQueries::GET_ALL_USERS;
        $result = $this->databaseInstance->prepare( $usersQuery );
        $result->execute();

        return $result->fetchAll();
    }

    public static function getInstance() {
        if ( self::$_instance == null ) {
            self::$_instance = new self();
        }

        return self::$_instance;
    }
}