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

    public function register( $username, $password, $email ) {
        if ( $this->userExists( $username ) ) {
            throw new ApplicationException( 'Username already taken', 400 );
        }

        $registerUserQuery =
            "INSERT INTO kpi_users(
                username,
                password,
                role_Id,
                email
            ) VALUES(?, ?, ?, ?)";

        $result = $this->getDatabaseInstance()->prepare( $registerUserQuery );
        $result->execute(
            [
                $username,
                password_hash( $password, PASSWORD_DEFAULT ),
                self::DEFAULT_USER_ROLE_ID,
                $email
            ]
        );

        if ( !$this->getLastId() ) {
            throw new ApplicationException( 'Registration failed', 400 );
        }
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

    private function userExists( $username ) {
        $findUserQuery = SelectQueries::GET_EXISTING_USER;
        $result = $this->getDatabaseInstance()->prepare( $findUserQuery );
        $result->execute( [ $username ] );

        return $result->rowCount() > 0;
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

    public function getUserPerformanceIndex( $userId ) {
        $indexQuery = SelectQueries::GET_USER_PERFORMANCE_INDEX;
        $result = $this->databaseInstance->prepare( $indexQuery );
        $result->execute( [ $userId ] );

        return $result->fetch();
    }

    public static function getInstance() {
        if ( self::$_instance == null ) {
            self::$_instance = new self();
        }

        return self::$_instance;
    }
}