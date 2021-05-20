<?php

namespace App\Facades;

use Illuminate\Support\Facades\Facade;

class Uploader extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'UploaderUtility';
    }
}
