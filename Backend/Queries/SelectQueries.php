<?php

namespace KPIReporting\Queries;

class SelectQueries {

    const GET_NEXT_EXTENSION_KEY =
        "SELECT IFNULL(MAX(chng.extension_key), 0) + 1 as 'nextExtensionKey'
        FROM kpi_plan_changes chng";

    const GET_LAST_PROJECT_CONFIG_RESET =
        "SELECT
           MAX(DATE(chng.timestamp)) AS 'lastConfigReset'
        FROM kpi_plan_changes chng
        WHERE chng.project_external_id = ? AND DATE(chng.timestamp) = CURDATE()";

    const CHECK_IF_PROJECT_IS_REPLICATED =
        "SELECT
            p.external_id
        FROM kpi_projects p
        WHERE p.external_id = ?";

    const GET_PROJECT_BY_ID =
        "SELECT
            opp.product_id AS 'id',
            opp.product_description AS 'name',
            opp.task_duration AS 'taskDuration',
            p.initial_commitment AS 'initialCommitment'
        FROM kpi_projects p
        JOIN ooredoo_products_pipeline opp ON opp.product_id = p.external_id
        WHERE p.external_id = ?";

    const GET_PROJECT_REMAINING_DAYS =
        "SELECT
            d.id AS 'dayId',
            d.day_index AS 'dayIndex',
            d.day_date AS 'dayDate',
            CONCAT(d.day_date, ' (Day ', d.day_index, ')') as 'dayPreview'
        FROM kpi_project_days d
        JOIN kpi_projects p ON p.external_id = d.project_external_id
        WHERE p.external_id = ? AND DATE(d.day_date) >= CURDATE()";

    const GET_LAST_PROJECT_DAY =
        "SELECT
            CASE
                WHEN MAX(pd.day_date) IS NULL THEN CURDATE()
                WHEN MAX(pd.day_date) IS NOT NULL THEN DATE_ADD(MAX(pd.day_date), INTERVAL 1 DAY)
            END AS 'startDayDate',
            CASE
                WHEN MAX(pd.day_date) IS NULL THEN 0
                WHEN MAX(pd.day_date) IS NOT NULL THEN MAX(pd.day_index)
            END AS 'startDayIndex'
        FROM kpi_project_days pd
        WHERE pd.project_external_id = ?";

    const GET_PROJECT_ASSIGNED_DAYS =
        "SELECT
            pd.id AS 'dayId',
            pd.project_external_id AS 'projectExternalId',
            pd.day_index AS 'dayIndex',
            pd.day_date AS 'dayDate',
            pd.expected_test_cases AS 'expected',
            CASE
                WHEN DATE(pd.day_date) < CURDATE() THEN 1
                WHEN DATE(pd.day_date) = CURDATE() THEN 2
            ELSE 3
            END AS 'period',

            (SELECT COUNT(tc.id) FROM kpi_test_cases tc WHERE tc.day_id = pd.id) AS 'allocated',

            (SELECT COUNT(tc.id) FROM kpi_test_cases tc
            JOIN kpi_statuses s ON s.id = tc.status_id
            WHERE tc.day_id = pd.id AND s.is_final = 0) AS 'nonFinal',

            (SELECT COUNT(exec.id) FROM kpi_executions exec
            JOIN kpi_test_cases tc on exec.test_case_id = tc.id
            WHERE tc.project_external_id = pd.project_external_id AND DATE(exec.timestamp) = DATE(pd.day_date)) AS 'executed',

            (SELECT COUNT(tc.id) FROM kpi_test_cases tc
            WHERE tc.status_id in (SELECT id FROM kpi_statuses WHERE is_final = 1) AND tc.day_id = pd.id) AS 'passed',

            (SELECT COUNT(tc.id) FROM kpi_test_cases tc
            WHERE tc.status_id in (SELECT id FROM kpi_statuses WHERE is_final = 0 AND is_blocked = 0 AND id != 1) AND tc.day_id = pd.id) AS 'failed',

            (SELECT COUNT(tc.id) FROM kpi_test_cases tc
            WHERE tc.status_id in (SELECT id FROM kpi_statuses WHERE is_blocked = 1) AND tc.day_id = pd.id) AS 'blocked',

            IF(pd.extension_key IS NULL, NULL,
                (SELECT GROUP_CONCAT(CONCAT(rsn1.description, ': ', chng1.duration, IF(chng1.duration != 1,' days',' day'))SEPARATOR ', ')
                FROM kpi_plan_changes chng1 JOIN kpi_plan_change_reasons rsn1 on rsn1.id = chng1.reason_id
                WHERE chng1.project_external_id = pd.project_external_id AND chng1.extension_key = pd.extension_key)) AS 'extension',

            (SELECT CONCAT(rsn2.description, ': ', chng2.explanation)
            FROM kpi_plan_changes chng2 JOIN kpi_plan_change_reasons rsn2 ON rsn2.id = chng2.reason_id
            WHERE chng2.project_external_id = pd.project_external_id AND rsn2.type = 2 AND DATE(chng2.timestamp) = DATE(pd.day_date)
            ORDER BY chng2.timestamp desc LIMIT 1) AS 'reset',

            (SELECT CONCAT(config.parked_duration, IF(config.parked_duration = 1, ' day', ' days'), ' ( ', rsn3.description , ' )')
            FROM kpi_plan_changes chng3
            JOIN kpi_plan_change_reasons rsn3 ON rsn3.id = chng3.reason_id
            JOIN kpi_configurations config ON config.id = chng3.configuration_id
            WHERE chng3.project_external_id = pd.project_external_id AND rsn3.type = 3 AND DATE(chng3.timestamp) = DATE(pd.day_date)
				ORDER BY chng3.timestamp desc LIMIT 1) AS 'park'

        FROM kpi_project_days pd
        WHERE pd.project_external_id = ?
        GROUP BY pd.day_date
        ORDER BY pd.day_date, pd.day_index
";

    const GET_PROJECT_ASSIGNED_USERS =
        "SELECT
           pu.user_id AS 'userId',
           u.performance_index AS 'performanceIndex',
           pu.user_load_indicator AS 'loadIndicator',
           pu.user_performance_indicator AS 'performanceIndicator',
           pu.configuration_id AS 'configId'
        FROM kpi_projects_users pu
        JOIN kpi_users u ON u.id = pu.user_id
        WHERE pu.project_external_id = ? AND pu.configuration_id = ?";

    const GET_PROJECT_INITIAL_COMMITMENT =
        "SELECT
            p.initial_commitment AS 'initialCommitment'
        FROM kpi_projects p
        WHERE p.external_id = ?";

    const GET_PROJECT_ALLOCATION_MAP_TEST_CASES =
        "SELECT
            p.external_id AS 'projectId',
            tc.id AS 'testCaseId',
            tc.title AS 'testCaseTitle',
            tc.external_id AS 'externalId',
            tc.external_status AS 'externalStatus',
            u.id AS 'userId',
            u.username AS 'username',
            d.id AS 'dayId',
            d.day_index AS 'dayIndex',
            d.day_date AS 'dayDate',
            CONCAT(d.day_date, ' (Day ', d.day_index, ')') as 'dayPreview',
            s.id AS 'statusId',
            s.name AS 'statusName',
            s.is_final AS 'isFinal',
            IF(DATE(d.day_date) >= CURDATE() AND tc.user_id IS NOT NULL, 1, 0) AS 'canEdit'
        FROM kpi_test_cases tc
        LEFT JOIN kpi_projects p ON p.external_id = tc.project_external_id
        LEFT JOIN kpi_users u ON u.id = tc.user_id
        LEFT JOIN kpi_project_days d ON d.id = tc.day_id
        LEFT JOIN kpi_statuses s ON s.id = tc.status_id
        WHERE tc.project_external_id = ? AND tc.external_status in (1, 2)
        ORDER BY d.day_index, tc.id";

    const GET_PROJECT_SYNC_TEST_CASES =
        "SELECT
            tc.id AS 'testCaseId',
            tc.title AS 'testCaseTitle',
            tc.external_id AS 'externalId',
            tc.external_status AS 'externalStatus'
        FROM kpi_test_cases tc
        WHERE tc.project_external_id = ?";

    const GET_PROJECT_UNALLOCATED_TEST_CASES =
        "SELECT
            tc.id AS 'testCaseId'
        FROM kpi_test_cases tc
        WHERE tc.project_external_id = ? AND tc.external_status = 1";

    const GET_PROJECT_EXPIRED_TEST_CASES =
        "SELECT
           tc.id AS 'testCaseId',
           tc.user_id AS 'userId',
           tc.status_id AS 'statusId'
        FROM kpi_test_cases tc
        JOIN kpi_project_days pd ON pd.id = tc.day_id
        JOIN kpi_statuses s ON s.id = tc.status_id
        WHERE tc.project_external_id = ? AND tc.external_status = 2 AND s.is_final = 0 AND DATE(pd.day_date) < CURDATE() ";

    const GET_TESTLINK_PROJECT =
        "SELECT
			h.id AS 'nodeId',
			h.name AS 'nodeName',
			h.node_type_id AS 'nodeTypeId',
			h.parent_id AS 'nodeParentId'
		FROM ooredoo_testlink_db.nodes_hierarchy h
		WHERE UPPER(h.name) LIKE UPPER(?) AND h.node_type_id = 1";

    const GET_CHILD_NODES =
        "SELECT
			h.id AS 'nodeId',
			h.name AS 'nodeName',
			h.node_type_id AS 'nodeTypeId',
			h.parent_id AS 'nodeParentId'
		FROM ooredoo_testlink_db.nodes_hierarchy h
		WHERE h.node_type_id in (2, 3) AND h.parent_id = ?";

    const GET_ALL_STATUSES =
        "SELECT
            s.id,
            s.name,
            s.is_final AS 'isFinal',
            s.is_blocked AS 'isBlocked'
        FROM kpi_statuses s
        ORDER BY s.id";

    const GET_EXTENSION_REASONS =
        "SELECT
            r.id,
            r.description
        FROM kpi_plan_change_reasons r
        WHERE r.type = 1
        ORDER BY r.id";

    const GET_RESET_REASONS =
        "SELECT
            r.id,
            r.description
        FROM kpi_plan_change_reasons r
        WHERE r.type = 2
        ORDER BY r.id";

    const GET_PARK_REASONS =
        "SELECT
            r.id,
            r.description
        FROM kpi_plan_change_reasons r
        WHERE r.type = 3
        ORDER BY r.id";

    const GET_LOGGED_USER_INFO =
        "SELECT
            u.id,
            u.username,
            u.email
        FROM kpi_users u
        WHERE u.id = ?";

    const GET_LOGIN_DATA =
        "SELECT
            u.id,
            u.password,
            r.name as 'role'
        FROM kpi_users u
        JOIN kpi_roles r
            ON r.id = u.role_Id
        WHERE username = ?";

    const GET_ALL_USERS =
        "SELECT
            u.id AS 'id',
            u.username AS 'username',
            u.performance_index AS 'performanceIndex'
        FROM kpi_users u
        ORDER BY u.username";

    const GET_TEST_CASE_EXECUTIONS =
        "SELECT
            e.timestamp,
            e.kpi_accountable AS 'eKpiAccountable',
            u.id AS 'eUserId',
            u.username AS 'eUsername',
            s1.name AS 'eOldStatusName',
            s2.name AS 'eNewStatusName',
            e.configuration_id AS 'configId'
        FROM kpi_executions e
        JOIN kpi_test_cases tc ON tc.id = e.test_case_id
        JOIN kpi_users u ON u.id = e.user_id
        JOIN kpi_statuses s1 ON s1.id = e.old_status_id
        JOIN kpi_statuses s2 ON s2.id = e.new_status_id
        WHERE tc.id = ?";

    const GET_TEST_CASE_DAY_CHANGES =
        "SELECT
            dc.timestamp,
            d1.id AS 'dcOldDayId',
            CONCAT(d1.day_date, ' (Day ', d1.day_index, ')') as 'dcOldDayPreview',
            d2.id AS 'dcNewDayId',
            CONCAT(d2.day_date, ' (Day ', d2.day_index, ')') as 'dcNewDayPreview',
            dc.configuration_id AS 'configId'
        FROM kpi_day_changes dc
        JOIN kpi_project_days d1 ON d1.id = dc.old_day_id
        JOIN kpi_project_days d2 ON d2.id = dc.new_day_id
        WHERE dc.test_case_id = ?";

    const GET_TEST_CASE_USER_CHANGES =
        "SELECT
            uc.timestamp,
            u1.id AS 'ucOldUserId',
            u2.id AS 'ucNewUserId',
            u1.username AS 'ucOldUsername',
            u2.username AS 'ucNewUsername',
            uc.configuration_id AS 'configId'
        FROM kpi_user_changes uc
        JOIN kpi_users u1 ON u1.id = uc.old_user_id
        JOIN kpi_users u2 ON u2.id = uc.new_user_id
        WHERE uc.test_case_id = ?";

    const GET_ACTIVE_CONFIG =
        "SELECT
          config.id AS 'configId',
          config.effective_from AS 'effectiveFrom',
          config.effective_to AS 'effectiveTo',
          config.parked AS 'isParked',
          config.parked_at AS 'parkedAt',
          config.parked_duration AS 'parkedDuration'
        FROM kpi_configurations config
        WHERE config.external_project_id = ? AND config.effective_to IS NULL";

    const GET_EXISTING_CONFIG =
        "SELECT config.id AS 'configId',
        FROM kpi_configurations config
        WHERE config.external_project_id = ? LIMIT 1";

    const GET_PARKED_CONFIGURATIONS =
        "SELECT
            config.id AS 'configId',
            config.parked_at AS 'parkedAt',
            config.parked_duration AS 'parkedDuration'
        FROM kpi_configurations config
        WHERE config.parked = 1 AND config.effective_to IS NULL";
}