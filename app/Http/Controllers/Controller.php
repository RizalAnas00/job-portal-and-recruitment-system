<?php

namespace App\Http\Controllers;

use Illuminate\Routing\Controller as BaseController;

abstract class Controller extends BaseController
{
    // Base controller now extends Laravel's routing controller so controllers
    // can use $this->middleware() in constructors.
}
