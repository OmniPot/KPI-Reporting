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
          VALUES(?, ?, ?, ?, ?, ?, ?, ?)";

    const INSERT_USER_CHANGE =
        "INSERT INTO kpi_user_changes(
            timestamp,
            test_case_id,
            old_user_id,
            new_user_id,
            configuration_id)
          VALUES(?, ?, ?, ?, ?)";

    const INSERT_DAY_CHANGE =
        "INSERT INTO kpi_day_changes(
            timestamp,
            test_case_id,
            old_day_id,
            new_day_id,
            reason_id,
            configuration_id)
          VALUES(?, ?, ?, ?, ?, ?)";

    const REPLICATE_PROJECT =
        "INSERT INTO kpi_projects (external_id)
          VALUES(?)";

    const INSERT_INTO_PROJECTS_USERS =
        "INSERT INTO kpi_projects_users(
            project_external_id,
            user_id,
            user_load_indicator,
            user_performance_indicator,
            configuration_id
        ) VALUES(?, ?, ?, ?, ?)";

    const INSERT_INTO_DAYS =
        "INSERT INTO kpi_project_days(
            project_external_id,
            day_index,
            day_date)
          VALUES (?, ?, ?)";
}