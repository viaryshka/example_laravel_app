<?php

namespace App\Facades;

use Illuminate\Support\Facades\Facade;

class Languages extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'LanguagesUtility';
    }
}
