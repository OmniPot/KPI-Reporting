<?php

namespace KPIReporting\BindingModels;

class ChangeDayBindingModel {

    /** @required */
    public $testCaseId;

    /** @required */
    public $oldDayId;

    /** @required */
    public $newDayId;

    /** @required */
    public $externalStatus;

    /** @required */
    public $reasonId;
}