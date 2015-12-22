<?php

const DB_HOST = 'localhost';
const DB_NAME = 'ooredoo_reporting_db';
const DB_USERNAME = 'root';
const DB_PASSWORD = '';

$dsn = 'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8';
$pdo = new \PDO( $dsn, DB_USERNAME, DB_PASSWORD );
$getInvalidProjectsQuery =
    "SELECT
        p.product_id AS 'projectId',
        p.product_code AS 'projectName'
    FROM kpi_configurations config
    JOIN ooredoo_products_pipeline p ON p.product_id = config.external_project_id
    WHERE TIMESTAMPDIFF(HOUR, config.effective_to, NOW()) >= 1";

$result = $pdo->query( $getInvalidProjectsQuery );
$result = $result->fetchAll( PDO::FETCH_ASSOC );

foreach ( $result as $config ) {
    $projectId = $config[ 'projectId' ];
    $projectName = $config[ 'projectName' ];

    $insertNotificationQuery =
        "INSERT INTO ooredoo_notifications (
            type,
            STATUS,
            notify_at_date,
            mail_to,
            mail_cc,
            mail_bcc,
            mail_subject,
            mail_sender_name,
            mail_sender_email,
            mail_body_template,
            mail_body,
            embeded_attachments,
            PARAM_PRODUCT_ID)
        VALUES ( ?, ?, NOW(), ?, ?, ?, ?, ?, ?, ?, ?, NULL, ?)";

    $pdo->beginTransaction();

    $stmt = $pdo->prepare( $insertNotificationQuery );
    $result = executeInsert( $stmt, $projectId, $projectName );

    if ( !$result ) {
        $pdo->rollBack();
        $result = executeInsert( $stmt, $projectId, $projectId );
    }

    $pdo->commit();
}

function executeInsert( PDOStatement $stmt, $projectId, $param ) {
    $type = 1;
    $status = 2;
    $mailTo = 'nrezachev@consultants.ooredoo.qa';
    $mailCC = 'skiran@consultants.ooredoo.qa';
    $mailBCC = '';
    $mailSubject = 'KPI Reporting System';
    $mailSenderName = 'KPI Dashboard';
    $mailSenderEmail = 'UATAnalysesGroup@ooredoo.qa';
    $mailBodyTemplate = 'KPI_CLOSED_CONFIGURATION';
    $mailBody = "Project {$param} has initiated a configuration reset but did not open a new one for more than one hour.";

    $stmt->bindParam( 1, $type, PDO::PARAM_INT );
    $stmt->bindParam( 2, $status, PDO::PARAM_INT );
    $stmt->bindParam( 3, $mailTo, PDO::PARAM_STR );
    $stmt->bindParam( 4, $mailCC, PDO::PARAM_STR );
    $stmt->bindParam( 5, $mailBCC, PDO::PARAM_STR );
    $stmt->bindParam( 6, $mailSubject, PDO::PARAM_STR );
    $stmt->bindParam( 7, $mailSenderName, PDO::PARAM_STR );
    $stmt->bindParam( 8, $mailSenderEmail, PDO::PARAM_STR );
    $stmt->bindParam( 9, $mailBodyTemplate, PDO::PARAM_STR );
    $stmt->bindParam( 10, $mailBody, PDO::PARAM_STR );
    $stmt->bindParam( 11, $projectId, PDO::PARAM_INT );

    return $stmt->execute();
}