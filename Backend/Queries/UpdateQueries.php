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
}