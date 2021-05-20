<?php

namespace App\Helpers;

use Carbon\Carbon;

class DatesHelper
{
    public function parseAndSetTimezone(string $dateString)
    {
        return Carbon::parse($dateString)->setTimezone(config('brikk.timezone'));
    }

    public function setTimezone(?Carbon $date)
    {
        if (is_null($date)) {
            return null;
        }

        return $date->setTimezone(config('brikk.timezone'));
    }

    public function setTimezoneToArray(&$array, $keys)
    {
        foreach ($keys as $key) {
            if (array_key_exists($key, $array)) {
                $array[$key] = $this->parseAndSetTimezone($array[$key]);
            }
        }
    }
}
