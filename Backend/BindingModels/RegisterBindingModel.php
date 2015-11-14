<?php

namespace KPIReporting\BindingModels;

class RegisterBindingModel {

    /** @required */
    public $username;

    /** @required */
    public $password;

    /** @required */
    public $confirm;

    public $email;
}