<?php
ini_set( 'display_errors', '1' );
set_time_limit( 300 );
date_default_timezone_set( 'Asia/Qatar' );

use KPIReporting\Framework\App;

session_start();

require_once '../Framework/App.php';

$app = App::getInstance();

$app->start();
