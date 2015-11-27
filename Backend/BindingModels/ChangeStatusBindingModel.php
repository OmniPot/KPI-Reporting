<?php

namespace KPIReporting\BindingModels;

class ChangeStatusBindingModel {

    /** @required */
    public $userId;

    /** @required */
    public $testCaseId;

    /** @required */
    public $oldStatusId;

    /** @required */
    public $newStatusId;

}