<?php
/**
 * Created by PhpStorm.
 * User: Rumen
 * Date: 12/11/2015
 * Time: 3:22 PM
 */

namespace KPIReporting\BindingModels;

class ExtendDurationBindingModel {

    /** @required */
    public $startDuration;

    /** @required */
    public $startDate;

    /** @required */
    public $endDuration;

    /** @required */
    public $expectedTestCases;

    /** @required */
    public $extensionReasons;
}