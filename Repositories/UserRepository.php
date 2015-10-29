<?php

namespace Medieval\Repositories;

use Medieval\Framework\BaseRepository;

class UserRepository extends BaseRepository {

    const DEFAULT_USER_ROLE_ID = 1;

    private static $_instance = null;

    protected function __construct() {
        parent::__construct();
    }

    public function register( $username, $password ) {
        if ( $this->exists( $username ) ) {
            throw new \Exception( 'Username already taken' );
        }

        $registerUserQuery =
            "INSERT INTO users(username, password, role_Id)  VALUES(?, ?, ?)";
        $result = $this->getDatabaseInstance()->prepare( $registerUserQuery );

        $result->execute(
            [
                $username,
                password_hash( $password, PASSWORD_DEFAULT ),
                self::DEFAULT_USER_ROLE_ID
            ]
        );

        if ( $result->rowCount() > 0 ) {
            $this->login( $username, $password );
        } else {
            throw new \Exception( 'Unsuccessful registration' );
        }
    }

    public function exists( $username ) {
        $findUserQuery = "SELECT id FROM users WHERE username = ?";
        $result = $this->getDatabaseInstance()->prepare( $findUserQuery );
        $result->execute( [ $username ] );

        return $result->rowCount() > 0;
    }

    /**
     * @param $username
     * @param $password
     * @return UserRepository
     * @throws \Exception
     */
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

        if ( $result->rowCount() > 0 ) {
            $userRow = $result->fetch();

            if ( password_verify( $password, $userRow[ 'password' ] ) ) {
                return [
                    'id' => $userRow[ 'id' ],
                    'role' => $userRow[ 'role' ]
                ];
            }

            throw new \Exception( 'Login failed' );
        }

        throw new \Exception( 'Login failed' );
    }

    public function getUserInfo( $userId ) {
        $query =
            "SELECT
                id,
                username,
                email,
                password
            FROM users
            WHERE id = ?";

        $result = $this->getDatabaseInstance()->prepare( $query );
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