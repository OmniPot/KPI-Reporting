<?php

namespace KPIReporting\Config;

class Queries {

    const ALL_PROJECTS =
        "SELECT
            p.id,
            p.name,
            p.description,
            p.duration,
            p.start_date,
            p.end_date
        FROM projects p";

    const PROJECT_BY_ID =
        "SELECT
            p.id,
            p.name,
            p.description,
            p.duration,
            p.start_date,
            p.end_date
        FROM projects p
        WHERE p.id = ?";

    const PROJECT_TEST_CASES =
        "SELECT
            p.id AS 'projectId',
            p.name AS 'projectName',
            tc.id AS 'testCaseId',
            tc.title AS 'testCaseTitle',
            u.id AS 'userId',
            u.username AS 'username',
            d.index AS 'dayIndex',
            d.date AS 'dayDate',
            s.id AS 'statusId',
            s.name AS 'statusName',
            s.is_final AS 'isFinal'
        FROM test_cases tc
        JOIN projects p ON p.id = tc.project_id
        JOIN users u ON u.id = tc.user_id
        JOIN days d ON d.id = tc.day_id
        JOIN statuses s ON s.id = tc.status_id
        WHERE tc.project_id = ?
        ORDER BY d.index";

    const ALL_STATUSES =
        "SELECT
            s.id,
            s.name
        FROM statuses s
        ORDER BY s.id";

    const INSERT_EXECUTION =
        "INSERT INTO executions(
            timestamp,
            kpi_accountable,
            user_id,
            test_case_id,
            new_status_id,
            old_status_id,
            comment)
          VALUES(?, ?, ?, ?, ?, ?, ?)";

    const TEST_CASE_STATUS_UPDATE =
        "UPDATE test_cases tc
        SET tc.status_id = ?
        WHERE tc.id = ?";
}