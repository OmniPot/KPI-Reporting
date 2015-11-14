<?php

namespace KPIReporting\Repositories;

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
            "INSERT INTO users(
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
        $query =
            "SELECT
                u.id,
                u.password,
                r.name as 'role'
            FROM users u
            JOIN roles r
                ON r.id = u.role_Id
            WHERE username = ?";

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
            'role' => $userRow[ 'role' ]
        ];
    }

    private function userExists( $username ) {
        $findUserQuery = "SELECT u.id FROM users u WHERE u.username = ?";
        $result = $this->getDatabaseInstance()->prepare( $findUserQuery );
        $result->execute( [ $username ] );

        return $result->rowCount() > 0;
    }

    public function getUserInfo( $username ) {
        if ( !$this->userExists( $username ) ) {
            throw new ApplicationException( "User {$username} does not exist" );
        }

        $query =
            "SELECT
                u.id,
                u.username,
                u.email,
                u.password
            FROM users u
            WHERE u.username = ?";

        $result = $this->getDatabaseInstance()->prepare( $query );
        $result->execute( [ $username ] );

        return $result->fetch();
    }

    public function getLoggedUserInfo() {
        $query = "SELECT u.id, u.username, u.email FROM users u WHERE u.id = ?";

        $result = $this->databaseInstance->prepare( $query );
        $result->execute( [ $_SESSION[ 'id' ] ] );

        return $result->fetch();
    }

    public static function getInstance() {
        if ( self::$_instance == null ) {
            self::$_instance = new self();
        }

        return self::$_instance;
    }
}