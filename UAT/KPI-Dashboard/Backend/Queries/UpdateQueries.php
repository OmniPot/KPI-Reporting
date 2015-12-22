<?php

namespace KPIReporting\Queries;

class UpdateQueries {

    const UPDATE_TEST_CASE_STATUS =
        "UPDATE kpi_test_cases tc
        SET tc.status_id = ?
        WHERE tc.id = ?";

    const UPDATE_TEST_CASE_USER =
        "UPDATE kpi_test_cases tc
        SET tc.user_id = ?,
        tc.external_status = ?
        WHERE tc.id = ?";

    const UPDATE_TEST_CASE_DAY =
        "UPDATE kpi_test_cases tc
        SET tc.day_id = ?,
        tc.external_status = ?
        WHERE tc.id = ?";

    const UPDATE_TEST_CASE_EXTERNAL_STATUS =
        "UPDATE kpi_test_cases tc
        SET tc.external_status = ?
        WHERE tc.external_id = ?";

    const UPDATE_DAY_DATE =
        "UPDATE kpi_project_days pd
        SET pd.day_date = ?
        WHERE pd.id = ?";

    const ALLOCATE_TEST_CASE =
        "UPDATE kpi_test_cases tc
        SET tc.external_status = 2,
            tc.user_id = ?,
            tc.day_id = ?,
            tc.status_id = ?
        WHERE tc.id = ?";

    const CLEAR_PROJECT_REMAINING_TEST_CASES =
        "UPDATE kpi_test_cases tc
            JOIN kpi_project_days tcd ON tcd.id = tc.day_id
            JOIN kpi_statuses tcs ON tcs.id = tc.status_id
        SET
            tc.external_status = 1,
            tc.status_id = 1,
            tc.user_id = null,
            tc.day_id = null
        WHERE tc.project_external_id = ? AND tcs.is_final = 0 AND DATE(tcd.day_date) >= CURDATE()";

    const CLEAR_EXPIRED_TEST_CASES_ON_DAY_END =
        "UPDATE kpi_test_cases tc
            JOIN kpi_project_days tcd ON tcd.id = tc.day_id
            JOIN kpi_statuses tcs ON tcs.id = tc.status_id
        SET
            tc.external_status = 1,
            tc.status_id = 1,
            tc.user_id = null,
            tc.day_id = null
        WHERE tcs.is_final = 0 AND DATE(tcd.day_date) < CURDATE()";

    const OVERRIDE_PROJECT_CONFIGURATION =
        "UPDATE kpi_project_days pd
            LEFT JOIN kpi_test_cases tc ON (tc.day_id = pd.id)
        SET pd.expected_test_cases = (SELECT COUNT(tc.id) FROM kpi_test_cases tc WHERE tc.day_id = pd.id)
        WHERE pd.project_external_id = ? AND DATE(pd.day_date) > CURDATE()";

    const PROJECT_INITIAL_COMMITMENT =
        "UPDATE kpi_projects p
        SET p.initial_commitment = ?
        WHERE p.external_id = ?";

    const CLOSE_CONFIGURATION =
        "UPDATE kpi_configurations config
        SET config.effective_to = NOW()
        WHERE config.id = ?";

    const STOP_EXECUTION =
        "UPDATE kpi_configurations config
        SET parked = 1,
            config.parked_at = NOW(),
            config.parked_duration = 1
        WHERE config.id = ?";

    const UPDATE_PARKED_CONFIGURATION =
        "UPDATE kpi_configurations config
		SET config.parked_duration = ?
		WHERE config.id = ?";

    const RESUME_EXECUTION =
        "UPDATE kpi_configurations config
        SET config.parked = 0
        WHERE config.id = ?";
}