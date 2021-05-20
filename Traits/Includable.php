<?php

namespace App\Traits;

use Illuminate\Http\Request;

trait Includable
{
    public function getAvailableIncludes(): array
    {
        return [];
    }

    protected function getIncludes($requestIncludes)
    {
        return array_intersect($requestIncludes, $this->getAvailableIncludes());
    }

    protected function getIncludesFromRequest(Request $request)
    {
        $requestIncludes = $request->get('includes') ?? [];

        return $this->getIncludes($requestIncludes);
    }
}
