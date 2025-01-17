<?php

namespace App\Http\Controllers;

use App\Traits\ApiResponser;
use App\Traits\ManagesUserSession;

abstract class Controller
{
    use ApiResponser, ManagesUserSession;
}
