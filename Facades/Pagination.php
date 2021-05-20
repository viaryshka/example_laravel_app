<?php

namespace App\Facades;

use Illuminate\Support\Facades\Facade;

class Pagination extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'PaginationUtility';
    }
}
