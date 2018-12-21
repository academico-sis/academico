<?php

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        //factory(App\User::class, 20)->create();
        //factory(App\Models\Period::class, 1)->create();
        //factory(App\Models\Course::class, 5)->create();
        
        // create required permissions
        
        // courses permissions
        Permission::create(['name' => 'courses.view']);
        Permission::create(['name' => 'courses.edit']);
        Permission::create(['name' => 'courses.delete']);
        Permission::create(['name' => 'courses.own']);
        
        // enrollments
        Permission::create(['name' => 'enrollments.view']);
        Permission::create(['name' => 'enrollments.create']);
        Permission::create(['name' => 'enrollments.edit']);
        Permission::create(['name' => 'enrollments.extraordinaryedit']);
        Permission::create(['name' => 'enrollments.delete']);

        // attendance permissions
        Permission::create(['name' => 'attendance.view']);
        Permission::create(['name' => 'attendance.edit']);

        // grades
        Permission::create(['name' => 'grades.edit']);
        Permission::create(['name' => 'grades.view']);
        Permission::create(['name' => 'results.edit']);
        Permission::create(['name' => 'results.view']);
        Permission::create(['name' => 'evaluation.edit']); // course evaluation method, etc.

        // reports
        Permission::create(['name' => 'reports.view']);
        Permission::create(['name' => 'reports.edit']);

        // calendars
        Permission::create(['name' => 'calendars.view']);

        // HR
        Permission::create(['name' => 'hr.view']);
        Permission::create(['name' => 'hr.manage']);

        // System
        Permission::create(['name' => 'system.view']);
        Permission::create(['name' => 'system.edit']);


        // create core roles and assign permissions

        $role = Role::create(['name' => 'admin']);
        // todo give all permissions to admins
        
        $role = Role::create(['name' => 'manager']);
        $role->givePermissionTo('system.view');
        $role->givePermissionTo('hr.view');
        $role->givePermissionTo('calendars.view');
        $role->givePermissionTo('reports.view');
        $role->givePermissionTo('courses.view');

        $role = Role::create(['name' => 'teacher']);
        $role->givePermissionTo('courses.own');
        $role->givePermissionTo('enrollments.create');
        $role->givePermissionTo('attendance.edit');
        $role->givePermissionTo('grades.edit');
        $role->givePermissionTo('results.edit');

        $role = Role::create(['name' => 'secretary']);
        $role->givePermissionTo('calendars.view');
        $role->givePermissionTo('results.view');
        $role->givePermissionTo('grades.view');
        $role->givePermissionTo('attendance.view');
        $role->givePermissionTo('enrollments.view');
        $role->givePermissionTo('enrollments.create');
        $role->givePermissionTo('courses.view');

    }
}
