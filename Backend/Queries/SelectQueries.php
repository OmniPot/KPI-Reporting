<?php

namespace KPIReporting\Queries;

class SelectQueries {

    const GET_ALL_PROJECTS =
        "SELECT
            p.id,
            p.name,
            p.description,
            p.duration,
            p.start_date,
            p.end_date
        FROM kpi_projects p";

    const GET_PROJECT_BY_ID =
        "SELECT
            p.id,
            p.name,
            p.description,
            p.duration,
            p.start_date,
            p.end_date
        FROM kpi_projects p
        WHERE p.id = ?";

    const CHECK_IF_PROJECT_IS_ALLOCATED =
        "SELECT
           IF(COUNT(p.id) > 0, 1, 0) AS 'isAllocated'
        FROM kpi_projects p
        JOIN kpi_days d ON d.project_id = p.id
        JOIN kpi_projects_users pu ON pu.project_id = p.id
        JOIN kpi_test_cases tc ON tc.project_id = p.id
        WHERE p.id = ?";

    const GET_PROJECT_TEST_CASES =
        "SELECT
            p.id AS 'projectId',
            p.name AS 'projectName',
            tc.id AS 'testCaseId',
            tc.title AS 'testCaseTitle',
            u.id AS 'userId',
            u.username AS 'username',
            d.id AS 'dayId',
            d.index AS 'dayIndex',
            d.date AS 'dayDate',
            CONCAT(d.date, ' (Day ', d.index, ')') as 'dayPreview',
            s.id AS 'statusId',
            s.name AS 'statusName',
            s.is_final AS 'isFinal',
            IF(d.date >= ?, 1, 0) AS 'canEdit'
        FROM kpi_test_cases tc
        JOIN kpi_projects p ON p.id = tc.project_id
        JOIN kpi_users u ON u.id = tc.user_id
        JOIN kpi_days d ON d.id = tc.day_id
        JOIN kpi_statuses s ON s.id = tc.status_id
        WHERE tc.project_id = ?
        ORDER BY d.index, u.username";

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

    const GET_EXISTING_USER = "SELECT u.id FROM kpi_users u WHERE u.username = ?";

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
            u.id,
            u.username
        FROM kpi_users u
        ORDER BY u.username";

    const GET_PROJECT_REMAINING_DAYS =
        "SELECT
            d.id AS 'dayId',
            d.index AS 'dayIndex',
            d.date AS 'dayDate',
            CONCAT(d.date, ' (Day ', d.index, ')') as 'dayPreview'
        FROM kpi_days d
        JOIN kpi_projects p ON p.id = d.project_id
        WHERE p.id = ?
            AND d.date >= p.start_date
            AND d.date <= p.end_date
            AND d.date >= ? ";

    const GET_TEST_CASE_EXECUTIONS =
        "SELECT
            e.timestamp,
            e.kpi_accountable AS 'eKpiAccountable',
            u.id AS 'eUserId',
            u.username AS 'eUsername',
            s1.name AS 'eOldStatusName',
            s2.name AS 'eNewStatusName'
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
            CONCAT(d1.date, ' (Day ', d1.index, ')') as 'dcOldDayPreview',
            d2.id AS 'dcNewDayId',
            CONCAT(d2.date, ' (Day ', d2.index, ')') as 'dcNewDayPreview'
        FROM kpi_day_changes dc
        JOIN kpi_days d1 ON d1.id = dc.old_day_id
        JOIN kpi_days d2 ON d2.id = dc.new_day_id
        WHERE dc.test_case_id = ?";

    const GET_TEST_CASE_USER_CHANGES =
        "SELECT
            uc.timestamp,
            u1.id AS 'ucOldUserId',
            u2.id AS 'ucNewUserId',
            u1.username AS 'ucOldUsername',
            u2.username AS 'ucNewUsername'
        FROM kpi_user_changes uc
        JOIN kpi_users u1 ON u1.id = uc.old_user_id
        JOIN kpi_users u2 ON u2.id = uc.new_user_id
        WHERE uc.test_case_id = ?";
}