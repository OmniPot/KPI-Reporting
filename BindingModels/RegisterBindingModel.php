<?php

namespace Medieval\BindingModels;

class RegisterBindingModel {

    /** @required */
    public $username;

    /** @required */
    public $password;

    /** @required */
    public $confirm;

    public $name;
}