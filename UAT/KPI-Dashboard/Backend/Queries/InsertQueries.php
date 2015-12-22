<?php

namespace KPIReporting\Queries;

class InsertQueries {

    const INSERT_STATUS_CHANGE =
        "INSERT INTO kpi_executions(
            timestamp,
            kpi_accountable,
            user_id,
            test_case_id,
            new_status_id,
            old_status_id,
            comment,
            configuration_id)
          VALUES(NOW(), ?, ?, ?, ?, ?, ?, ?)";

    const INSERT_USER_CHANGE =
        "INSERT INTO kpi_user_changes(
            timestamp,
            test_case_id,
            old_user_id,
            new_user_id,
            configuration_id)
          VALUES(NOW(), ?, ?, ?, ?)";

    const INSERT_DAY_CHANGE =
        "INSERT INTO kpi_day_changes(
            timestamp,
            test_case_id,
            old_day_id,
            new_day_id,
            reason_id,
            configuration_id)
          VALUES(NOW(), ?, ?, ?, ?, ?)";

    const INSERT_PLAN_CHANGE =
        "INSERT INTO kpi_plan_changes(
            timestamp,
            duration,
            extension_key,
            explanation,
            project_external_id,
            reason_id,
            configuration_id)
          VALUES(NOW(), ?, ?, ?, ?, ?, ?)";

    const REPLICATE_PROJECT =
        "INSERT INTO kpi_projects (external_id)
          VALUES(?)";

    const INSERT_TEST_CASE =
        "INSERT INTO kpi_test_cases ( title, external_id, external_status, project_external_id, status_id )
         VALUES (?, ?, ?, ?, ?)";

    const INSERT_INTO_PROJECTS_USERS =
        "INSERT INTO kpi_projects_users (
            project_external_id,
            user_id,
            user_load_indicator,
            user_performance_indicator,
            configuration_id)
        VALUES(?, ?, ?, ?, ?)";

    const INSERT_INTO_PROJECT_DAYS =
        "INSERT INTO kpi_project_days(
            project_external_id,
            day_index,
            day_date,
            expected_test_cases,
            extension_key,
            configuration_id)
          VALUES (?, ?, ?, ?, ?, ?)";

    const CREATE_CONFIGURATION =
        "INSERT INTO kpi_configurations(
            external_project_id,
            effective_from,
            effective_to,
            parked,
            parked_at,
            parked_duration)
        VALUES(?, NOW(), NULL, 0, NULL, NULL)";
}