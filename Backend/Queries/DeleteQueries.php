<?php

namespace KPIReporting\Queries;

class DeleteQueries {
    const DELETE_PROJECT_REMAINING_DAYS_ON_PLAN_RESET =
        "DELETE FROM kpi_project_days
        WHERE project_external_id = ? AND DATE(day_date) > CURDATE()";
}