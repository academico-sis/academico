<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\PermissionRegistrar;

/**
 * Removes permissions that nothing checks: `leads.manage` (the leads feature is gone) and `student.edit`
 * (editing students requires `enrollments.edit`).
 */
return new class extends Migration
{
    /**
     * @var list<string>
     */
    private array $permissions = ['leads.manage', 'student.edit'];

    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $tables = config('permission.table_names');
        $ids = DB::table($tables['permissions'])->whereIn('name', $this->permissions)->pluck('id');

        DB::table($tables['role_has_permissions'])->whereIn(config('permission.column_names.permission_pivot_key') ?? 'permission_id', $ids)->delete();
        DB::table($tables['model_has_permissions'])->whereIn(config('permission.column_names.permission_pivot_key') ?? 'permission_id', $ids)->delete();
        DB::table($tables['permissions'])->whereIn('id', $ids)->delete();

        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $tables = config('permission.table_names');

        foreach ($this->permissions as $permission) {
            if (! DB::table($tables['permissions'])->where('name', $permission)->exists()) {
                DB::table($tables['permissions'])->insert(['name' => $permission, 'guard_name' => 'web', 'created_at' => now(), 'updated_at' => now()]);
            }
        }

        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }
};
