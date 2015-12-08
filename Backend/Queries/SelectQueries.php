<?php

namespace KPIReporting\Queries;

class SelectQueries {

    const CHECK_IF_PROJECT_SOURCE_EXISTS =
        "SELECT
            p.product_id
        FROM ooredoo_products_pipeline p
        WHERE p.product_id = ?";

    const CHECK_IF_PROJECT_IS_REPLICATED =
        "SELECT
            p.external_id
        FROM kpi_projects p
        WHERE p.external_id = ?";

    const GET_PROJECT_BY_ID =
        "SELECT
           opp.product_id AS 'id',
           opp.task_duration AS 'taskDuration',
           opp.product_description AS 'name',
           COUNT(tc.id) AS 'nonFinalTestCasesCount',
           (SELECT COUNT(*)
            FROM kpi_test_cases tc2
            WHERE tc2.external_status = 1)  AS 'unallocatedTestCasesCount'
        FROM ooredoo_products_pipeline opp
        JOIN kpi_test_cases tc ON tc.project_external_id = opp.product_id
        JOIN kpi_statuses s ON s.id = tc.status_id
        WHERE opp.product_id = ? AND s.is_final = 0";

    const GET_PROJECT_ASSIGNED_DAYS =
        "SELECT
           pd.id AS 'dayId',
           pd.project_external_id AS 'projectExternalId',
           pd.day_index AS 'dayIndex',
           pd.day_date AS 'dayDate',
           pd.expected_test_cases AS 'expectedTestCases',
           COUNT(tc.id) as 'allocatedTestCases',
           CONCAT(pd.day_date, ' (Day ', pd.day_index, ')') as 'dayPreview'
        FROM kpi_project_days pd
        LEFT JOIN kpi_test_cases tc on tc.day_id = pd.id
        WHERE pd.project_external_id = ? AND pd.configuration_id = ?
        GROUP BY pd.id";

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

    const GET_PROJECT_REMAINING_DAYS =
        "SELECT
            d.id AS 'dayId',
            d.day_index AS 'dayIndex',
            d.day_date AS 'dayDate',
            CONCAT(d.day_date, ' (Day ', d.day_index, ')') as 'dayPreview'
        FROM kpi_project_days d
        JOIN kpi_projects p ON p.external_id = d.project_external_id
        WHERE p.external_id = ? AND d.day_date >= ? AND d.configuration_id = ?";

    const GET_PROJECT_TEST_CASES =
        "SELECT
            p.external_id AS 'projectId',
            tc.id AS 'testCaseId',
            tc.title AS 'testCaseTitle',
            u.id AS 'userId',
            u.username AS 'username',
            d.id AS 'dayId',
            d.day_index AS 'dayIndex',
            d.day_date AS 'dayDate',
            CONCAT(d.day_date, ' (Day ', d.day_index, ')') as 'dayPreview',
            s.id AS 'statusId',
            s.name AS 'statusName',
            tc.external_status AS 'externalStatus',
            s.is_final AS 'isFinal',
            IF(d.day_date >= ? AND tc.user_id IS NOT NULL, 1, 0) AS 'canEdit'
        FROM kpi_test_cases tc
        LEFT JOIN kpi_projects p ON p.external_id = tc.project_external_id
        LEFT JOIN kpi_users u ON u.id = tc.user_id
        LEFT JOIN kpi_project_days d ON d.id = tc.day_id
        LEFT JOIN kpi_statuses s ON s.id = tc.status_id
        WHERE tc.project_external_id = ?
        ORDER BY d.day_index, u.username";

    const GET_UNALLOCATED_TEST_CASES =
        "SELECT
            tc.id AS 'testCaseId'
        FROM kpi_test_cases tc
        WHERE tc.project_external_id = ? AND tc.external_status = 1";

    const GET_ALL_STATUSES =
        "SELECT
            s.id,
            s.name
        FROM kpi_statuses s
        ORDER BY s.id";

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

    const GET_TEST_CASE_EVENTS =
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
            config.is_parked AS 'isParked'
        FROM kpi_configurations config
        WHERE config.external_project_id = ? AND config.effective_to IS NULL";

    const GET_PROJECT_DURATIONS =
        "SELECT
            p.initial_commitment AS 'initialDuration',
            (SELECT COUNT(*)
            FROM kpi_project_days pd
            WHERE pd.project_external_id = p.external_id AND pd.configuration_id = ?) AS 'currentDuration',
            (SELECT opp.task_duration
            FROM ooredoo_products_pipeline opp
            WHERE opp.product_id = p.external_id) AS 'wrikeDuration'
        FROM kpi_projects p
        WHERE p.external_id = ?";
}