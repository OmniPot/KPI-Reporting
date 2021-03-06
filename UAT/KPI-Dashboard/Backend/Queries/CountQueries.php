<?php

namespace KPIReporting\Queries;

class CountQueries {
    const GET_PROJECT_UNALLOCATED_TEST_CASES_COUNT =
        "SELECT
            COUNT(*) AS 'unAllocatedTestCasesCount'
        FROM kpi_test_cases tc
        WHERE tc.project_external_id = ? AND tc.external_status = 1";

    const GET_PROJECT_EXPIRED_NON_FINAL_TEST_CASES_COUNT =
        "SELECT
            COUNT(*) AS 'expiredNonFinalTestCasesCount'
        FROM kpi_test_cases tc
        JOIN kpi_statuses st ON st.id = tc.status_id
        JOIN kpi_project_days d ON d.id = tc.day_id
        WHERE tc.project_external_id = ?
        AND st.is_final = 0 AND DATE(d.day_date) < CURDATE() AND d.configuration_id = ?";

    const GET_PROJECT_CURRENT_DURATION =
        "SELECT
            COUNT(*) AS 'currentDuration'
        FROM kpi_project_days pd
        WHERE pd.project_external_id = ? AND pd.configuration_id = ?";
}