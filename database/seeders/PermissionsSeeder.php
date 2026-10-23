<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class PermissionsSeeder extends Seeder
{
    public function run(): void
    {
        // courses permissions
        Permission::create(['name' => 'courses.view']);
        Permission::create(['name' => 'courses.edit']);
        Permission::create(['name' => 'courses.delete']);

        // enrollments
        Permission::create(['name' => 'enrollments.view']);
        Permission::create(['name' => 'enrollments.edit']);
        Permission::create(['name' => 'enrollments.delete']);

        // attendance permissions
        Permission::create(['name' => 'attendance.view']);
        Permission::create(['name' => 'attendance.edit']);

        // grades, skills, etc.
        Permission::create(['name' => 'evaluation.edit']);
        Permission::create(['name' => 'evaluation.view']);

        // reports
        Permission::create(['name' => 'reports.view']);

        // calendars
        Permission::create(['name' => 'calendars.view']);

        // HR
        Permission::create(['name' => 'hr.view']);
        Permission::create(['name' => 'hr.manage']);

        // edit and delete the comments of other users
        Permission::create(['name' => 'comments.edit']);

        // admins have all permissions
        $role = Role::create(['name' => 'admin']);
        foreach (Permission::all() as $permission) {
            $role->givePermissionTo($permission->name);
        }

        // secretaries typically deal with enrollments, course information, etc.
        $role = Role::create(['name' => 'secretary']);
        $role->givePermissionTo('calendars.view');
        $role->givePermissionTo('evaluation.view');
        $role->givePermissionTo('attendance.view');
        $role->givePermissionTo('attendance.edit');
        $role->givePermissionTo('enrollments.view');
        $role->givePermissionTo('enrollments.edit');
        $role->givePermissionTo('courses.view');

        // viewers can access the panel but have no resource permissions
        Role::create(['name' => 'viewer']);
    }
}
