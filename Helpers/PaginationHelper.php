<?php

namespace App\Helpers;

class PaginationHelper
{
    /**
     * @param  bool  $enabled
     * @return int
     */
    public function getPerPage($enabled = true): int
    {
        if (isset($enabled) && ! $enabled) {
            return 0;
        }

        return config('brikk.per_page');
    }
}
