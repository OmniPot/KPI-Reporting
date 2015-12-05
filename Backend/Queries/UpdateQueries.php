<?php

namespace KPIReporting\Queries;

class UpdateQueries {

    const UPDATE_TEST_CASE_STATUS =
        "UPDATE kpi_test_cases tc
        SET tc.status_id = ?
        WHERE tc.id = ?";

    const UPDATE_TEST_CASE_USER =
        "UPDATE kpi_test_cases tc
        SET tc.user_id = ?
        WHERE tc.id = ?";

    const UPDATE_TEST_CASE_DAY =
        "UPDATE kpi_test_cases tc
        SET tc.day_id = ?
        WHERE tc.id = ?";

    const ALLOCATE_TEST_CASE =
        "UPDATE kpi_test_cases tc
        SET tc.external_status = 2,
            tc.user_id = ?,
            tc.day_id = ?,
            tc.configuration_id = ?
        WHERE tc.id = ?";

    const PROJECT_INITIAL_COMMITMENT =
        "UPDATE kpi_projects p
        SET p.initial_commitment = ?
        WHERE p.external_id = ?";

    const CLOSE_CONFIGURATION =
        "UPDATE kpi_configurations config
        SET config.effective_to = ?
        WHERE config.id = ?";
}