<?php

namespace App\Filament\Concerns;

use Illuminate\Auth\Access\Response;
use Illuminate\Database\Eloquent\Model;
use UnitEnum;

/**
 * Filament only uses the cluster's canAccess() to hide it from the navigation:
 * the pages of the resources inside the cluster stay reachable by URL.
 * This denies every operation, including viewing, to users who cannot access the cluster.
 */
trait RequiresClusterAccess
{
    public static function getAuthorizationResponse(string|UnitEnum $action, ?Model $record = null): Response
    {
        if (! static::getCluster()::canAccess()) {
            return Response::deny();
        }

        return parent::getAuthorizationResponse($action, $record);
    }
}
