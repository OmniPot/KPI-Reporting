<?php

namespace KPIReporting\Queries;

class DeleteQueries {
    const DELETE_PROJECT_REMAINING_DAYS =
        "DELETE FROM kpi_project_days
        WHERE project_external_id = ? AND DATE(day_date) > CURDATE()";

    const DELETE_PROJECT_DAY =
        "DELETE FROM kpi_project_days
        WHERE project_external_id = ? AND id = ?";
}