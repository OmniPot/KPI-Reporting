<?php
ini_set( 'display_errors', '1' );
date_default_timezone_set('Asia/Qatar');

use KPIReporting\Framework\App;

session_start();

require_once '../Framework/App.php';

$app = App::getInstance();

$app->start();
