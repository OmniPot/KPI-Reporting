<?php

namespace KPIReporting\BindingModels;

class ProjectBindingModel {

    /** @required */
    public $name;

    public $description;

    /** @required */
    public $duration;

    public $start_date;

    public $end_date;
}