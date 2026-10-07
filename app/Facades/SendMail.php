<?php

namespace App\Facades;

use Illuminate\Support\Facades\Facade;

class SendMail extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'mail-service';
    }
}
