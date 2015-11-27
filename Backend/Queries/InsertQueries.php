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
            comment)
          VALUES(?, ?, ?, ?, ?, ?, ?)";

    const INSERT_USER_CHANGE =
        "INSERT INTO kpi_user_changes(
            timestamp,
            test_case_id,
            old_user_id,
            new_user_id)
          VALUES(?, ?, ?, ?)";

    const INSERT_DAY_CHANGE =
        "INSERT INTO kpi_day_changes(
            timestamp,
            test_case_id,
            old_day_id,
            new_day_id,
            reason_id)
          VALUES(?, ?, ?, ?, ?)";

}