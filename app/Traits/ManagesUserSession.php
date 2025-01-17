<?php

namespace App\Traits;

use stdClass;

trait ManagesUserSession
{
    public function getUserSession(): ?stdClass
    {
        return session('user_session');
    }
}
