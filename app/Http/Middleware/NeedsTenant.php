<?php

namespace App\Http\Middleware;

use Spatie\Multitenancy\Http\Middleware\NeedsTenant as BaseNeedsTenant;

/**
 * A host that matches no tenant gets a 404.
 */
class NeedsTenant extends BaseNeedsTenant
{
    public function handleInvalidRequest()
    {
        abort(404);
    }
}
