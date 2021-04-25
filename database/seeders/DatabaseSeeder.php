<?php

namespace Database\Seeders;

use App\Models\AttendanceType;
use App\Models\Campus;
use App\Models\ContactRelationship;
use App\Models\Course;
use App\Models\EnrollmentStatusType;
use App\Models\EvaluationType;
use App\Models\LeadType;
use App\Models\LeaveType;
use App\Models\Level;
use App\Models\Paymentmethod;
use App\Models\ResultType;
use App\Models\Rhythm;
use App\Models\Room;
use App\Models\Skills\SkillScale;
use App\Models\Student;
use App\Models\Teacher;
use App\Models\User;
use App\Models\Year;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        Campus::create([
            'id' => 1,
            'name' => [
                'en' => 'Internal',
                'es' => 'Interno',
                'fr' => 'Interne',
            ],
        ]);

        EnrollmentStatusType::create([
            'id' => 1,
            'name' => [
                'es' => 'PENDIENTE',
                'en' => 'PENDING',
                'fr' => 'NON-PAYÉ',
            ],
        ]);

        EnrollmentStatusType::create([
            'id' => 2,
            'name' => [
                'es' => 'PAGADA',
                'en' => 'PAID',
                'fr' => 'PAYÉ',
            ],
        ]);

        ResultType::create([
            'id' => 1,
            'name' => [
                'fr' => 'VALIDÉ',
                'es' => 'APROBADO',
                'en' => 'PASS',

            ],
            'description' => [
                'fr' => 'Peut passer au niveau suivant',
                'es' => 'Puede pasar al nivel siguiente',
                'en' => 'May go to the next level',
            ],
        ]);

        ResultType::create([
            'id' => 2,
            'name' => [
                'fr' => 'NON-VALIDÉ',
                'es' => 'REPROBADO',
                'en' => 'FAIL',

            ],
            'description' => [
                'fr' => 'Ne peut pas passer au niveau suivant',
                'es' => 'No puede pasar al nivel siguiente',
                'en' => 'Cannot go to the next level',
            ],
        ]);

        EvaluationType::create([
            'id' => 1,
            'name' => [
                'fr' => 'NOTES',
                'es' => 'NOTAS',
                'en' => 'GRADES',
            ],
        ]);


        AttendanceType::create([
            'id' => 1,
            'name' => ['fr' => 'PRÉSENT(E)', 'es' => 'PRESENTE', 'en' => 'PRESENT'],
            'class' => 'success',
            'icon' => '<i class="la la-user"></i>',
        ]);

        AttendanceType::create([
            'id' => 2,
            'name' => ['fr' => 'PRÉSENCE PARTIELLE', 'es' => 'PRESENCIA PARCIAL', 'en' => 'PARTIAL PRESENCE'],
            'class' => 'warning',
            'icon' => '<i class="la la-clock-o"></i>',
        ]);

        AttendanceType::create([
            'id' => 3,
            'name' => ['fr' => 'EXCUSÉ(E)', 'es' => 'JUSTIFICADO', 'en' => 'EXCUSED'],
            'class' => 'info',
            'icon' => '<i class="la la-exclamation"></i>',
        ]);

        AttendanceType::create([
            'id' => 4,
            'name' => ['fr' => 'ABSENT(E)', 'es' => 'AUSENTE', 'en' => 'ABSENT'],
            'class' => 'danger',
            'icon' => '<i class="la la-user-times"></i>',
        ]);

        ContactRelationship::create([
            'id' => 1,
            'name' => ['fr' => 'FAMILLE', 'es' => 'FAMILIA', 'en' => 'FAMILY'],
        ]);

        ContactRelationship::create([
            'id' => 2,
            'name' => ['fr' => 'TRAVAIL', 'es' => 'TRABAJO', 'en' => 'WORK'],
        ]);

        SkillScale::create([
            'id' => 1,
            'shortname' => ['fr' => 'NON', 'es' => 'NO', 'en' => 'NO'],
            'name' => ['fr' => 'NON-ACQUIS', 'es' => 'NO ADQUIRIDO', 'en' => 'NOT ACQUIRED'],
            'value' => 0,
        ]);

        SkillScale::create([
            'id' => 2,
            'shortname' => ['fr' => 'EC', 'es' => 'EC', 'en' => 'WIP'],
            'name' => ['fr' => 'EN COURS', 'es' => 'EN CURSO DE ADQUISICIÓN', 'en' => 'IN PROGRESS'],
            'value' => 0.4,
        ]);

        SkillScale::create([
            'id' => 3,
            'shortname' => ['fr' => 'OUI', 'es' => 'SI', 'en' => 'YES'],
            'name' => ['fr' => 'ACQUIS', 'es' => 'ADQUIRIDO', 'en' => 'ACQUIRED'],
            'value' => 1,
        ]);

        LeaveType::create([
            'id' => 1,
            'name' => ['fr' => 'JOUR FÉRIÉ', 'es' => 'FERIADO', 'en' => 'NATIONAL HOLIDAY'],
        ]);

        LeaveType::create([
            'id' => 2,
            'name' => ['fr' => 'CONGÉ', 'es' => 'VACACIONES', 'en' => 'LEAVE'],
        ]);

        LeaveType::create([
            'id' => 3,
            'name' => ['fr' => 'SPÉCIAL', 'es' => 'ESPECIAL', 'en' => 'SPECIAL'],
        ]);

        LeaveType::create([
            'id' => 4,
            'name' => ['fr' => 'RÉCUPÉRATION', 'es' => 'RECUPERACIÓN', 'en' => 'RECOVERY'],
        ]);

        LeaveType::create([
            'id' => 5,
            'name' => ['fr' => 'MALADIE', 'es' => 'ENFERMEDAD', 'en' => 'SICK LEAVE'],
        ]);

        LeadType::create([
            'id' => '1',
            'name' => [
                'fr' => 'Actif',
                'en' => 'Active',
                'es' => 'Activo',
            ],
            'description' => [
                'fr' => 'Inscrit maintenant',
                'en' => 'Currently enrolled',
                'es' => 'Matriculado ahora',
            ],
            'icon' => 'la-check',
        ]);

        LeadType::create([
            'id' => '2',
            'name' => [
                'fr' => 'Inactif',
                'en' => 'Inactive',
                'es' => 'Inactivo',
            ],
            'description' => [
                'fr' => 'Non disponible maintenant',
                'en' => 'Currently unavailable',
                'es' => 'No esta disponible ahora',
            ],
            'icon' => 'la-calendar-times',
        ]);

        LeadType::create([
            'id' => '3',
            'name' => [
                'fr' => 'Ancien client',
                'en' => 'Former client',
                'es' => 'Clientes antiguos',
            ],
            'description' => [
                'fr' => 'Cursus terminé ou abandon définitif',
                'en' => 'Permanently ended their learning',
                'es' => 'Acabó su aprendizaje o no regresará',
            ],
            'icon' => 'la-certificate',
        ]);

        LeadType::create([
            'id' => '4',
            'name' => [
                'fr' => 'Client potentiel',
                'en' => 'Potential Client',
                'es' => 'Cliente potencial',
            ],
            'description' => [
                'fr' => 'Inscription attendue pour ce cycle',
                'en' => 'Expected enrollment for this session',
                'es' => 'Deberia matricularse este ciclo',
            ],
            'icon' => 'la-user-circle',
        ]);

        Paymentmethod::create(['id' => '1', 'name' => 'Tarjeta de Crédito', 'code' => 'TC']);
        Paymentmethod::create(['id' => '2', 'name' => 'Crédito', 'code' => 'CRC']);
        Paymentmethod::create(['id' => '3', 'name' => 'Efectivo', 'code' => 'EFECT']);
        Paymentmethod::create(['id' => '4', 'name' => 'Cheque', 'code' => 'CHR']);

        // create required permissions

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

        // student related permissions
        Permission::create(['name' => 'student.edit']);
        Permission::create(['name' => 'comments.edit']);

        Permission::create(['name' => 'leads.manage']);

        // create core roles and assign permissions

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
        $role->givePermissionTo('leads.manage');

        $admin = factory(User::class)->create([
            'email' => 'admin@academico.site',
            'password' => '$2y$10$TKh8H1.PfQx37YgCzwiKb.KjNyWgaHb9cbcoQgdIVFlYg7B77UdFm', // secret
        ]);

        $admin->assignRole('admin');

    }
}
