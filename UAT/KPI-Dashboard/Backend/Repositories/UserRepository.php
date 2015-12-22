<?php

namespace KPIReporting\Repositories;

use KPIReporting\Queries\InsertQueries;
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
        $stmt = $this->getDatabaseInstance()->prepare( SelectQueries::GET_LOGIN_DATA );
        $stmt->execute( [ $username ] );
        if ( !$stmt ) {
            throw new ApplicationException( implode( "\n", $stmt->getErrorInfo() ), 500 );
        }

        if ( $stmt->rowCount() == 0 ) {
            throw new ApplicationException( 'Login failed', 400 );
        }

        $userRow = $stmt->fetch();
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
        $stmt = $this->databaseInstance->prepare( SelectQueries::GET_LOGGED_USER_INFO );
        $stmt->execute( [ $_SESSION[ 'id' ] ] );
        if ( !$stmt ) {
            throw new ApplicationException( implode( "\n", $stmt->getErrorInfo() ), 500 );
        }

        return $stmt->fetch();
    }

    public function getAllUsers() {
        $stmt = $this->databaseInstance->prepare( SelectQueries::GET_ALL_USERS );

        $stmt->execute();
        if ( !$stmt ) {
            throw new ApplicationException( implode( "\n", $stmt->getErrorInfo() ), 500 );
        }

        return $stmt->fetchAll();
    }

    public function getUserById( $id ) {
        $stmt = $this->getDatabaseInstance()->prepare( SelectQueries::GET_USER_BY_ID );

        $stmt->execute( [ $id ] );

        if ( !$stmt ) {
            throw new ApplicationException( 'Failed to fetch user.', 400 );
        }

        return $stmt->fetch();
    }

    public function getUserLoad( $userId ) {
        $stmt = $this->databaseInstance->prepare( SelectQueries::GET_USER_LOAD_BY_DAYS );

        $stmt->execute( [ $userId ] );
        if ( !$stmt ) {
            throw new ApplicationException( implode( "\n", $stmt->getErrorInfo() ), 500 );
        }

        return $stmt->fetchAll();
    }

    public function expandUserDay( $userId, $dayDate ) {
        $stmt = $this->databaseInstance->prepare( SelectQueries::EXPAND_USER_DAY );

        $stmt->execute( [ $userId, $dayDate ] );
        if ( !$stmt ) {
            throw new ApplicationException( implode( "\n", $stmt->getErrorInfo() ), 500 );
        }

        return $stmt->fetchAll();
    }

    public function getUsersLoad() {
        $stmt = $this->databaseInstance->prepare( SelectQueries::GET_USERS_LOAD );

        $stmt->execute();
        if ( !$stmt ) {
            throw new ApplicationException( implode( "\n", $stmt->getErrorInfo() ), 500 );
        }

        return $stmt->fetchAll();
    }

    public function assignUserToProject( $projectId, $userId, $userLoad, $userPerformance, $config ) {
        $stmt = $this->getDatabaseInstance()->prepare( InsertQueries::INSERT_INTO_PROJECTS_USERS );

        $stmt->execute( [ $projectId, $userId, $userLoad, $userPerformance, $config ] );
        if ( !$stmt ) {
            $this->rollback();
            throw new ApplicationException( implode( "\n", $stmt->getErrorInfo() ), 500 );
        }

        if ( $stmt->rowCount() == 0 ) {
            $this->rollback();
            throw new ApplicationException( "Assigning user with Id {$userId} to project with Id {$projectId} failed.", 400 );
        }
    }

    public static function getInstance() {
        if ( self::$_instance == null ) {
            self::$_instance = new self();
        }

        return self::$_instance;
    }
}