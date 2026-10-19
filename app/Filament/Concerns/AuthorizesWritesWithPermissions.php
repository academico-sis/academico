<?php

namespace App\Filament\Concerns;

use BackedEnum;
use Illuminate\Auth\Access\Response;
use Illuminate\Database\Eloquent\Model;
use UnitEnum;

/**
 * Restricts the write operations of a resource to users holding a permission.
 *
 * The models have no policies, so Filament would otherwise allow every
 * operation to anyone who can access the resource. The resource declares:
 *
 *     protected static string $editPermission = 'courses.edit';      // create, edit, replicate...
 *     protected static string $deletePermission = 'courses.delete';  // delete, restore, force delete
 */
trait AuthorizesWritesWithPermissions
{
    public static function getAuthorizationResponse(string|UnitEnum $action, ?Model $record = null): Response
    {
        $action = match (true) {
            $action instanceof BackedEnum => $action->value,
            $action instanceof UnitEnum => $action->name,
            default => $action,
        };

        $permission = match ($action) {
            'viewAny', 'view' => null,
            'delete', 'deleteAny', 'forceDelete', 'forceDeleteAny', 'restore', 'restoreAny' => static::$deletePermission,
            default => static::$editPermission,
        };

        if ($permission !== null && ! (auth()->user()?->can($permission) ?? false)) {
            return Response::deny();
        }

        return parent::getAuthorizationResponse($action, $record);
    }
}
