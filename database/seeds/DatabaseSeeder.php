<?php

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
use App\Models\Period;
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

        Campus::create([
            'id' => 2,
            'name' => [
                'en' => 'External',
                'es' => 'Externo',
                'fr' => 'Externe',
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

        EnrollmentStatusType::create([
            'id' => 3,
            'name' => [
                'es' => 'ANULADA',
                'en' => 'CANCELED',
                'fr' => 'ANNULÉ',
            ],
        ]);

        EnrollmentStatusType::create([
            'id' => 4,
            'name' => [
                'es' => 'TRASPASO',
                'en' => 'TRANSFERED',
                'fr' => 'TRANSFÉRÉ',
            ],
        ]);

        EnrollmentStatusType::create([
            'id' => 5,
            'name' => [
                'es' => 'DEVOLUCION',
                'en' => 'REFUND',
                'fr' => 'REMBOURSÉ',
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

        ResultType::create([
            'id' => 3,
            'name' => [
                'fr' => 'VOIR COORD. PEDA',
                'es' => 'VER COORD. PEDA',
                'en' => 'SEE DIR.',

            ],
            'description' => [
                'fr' => 'Vérifier le résultat avec la direction pédagogique',
                'es' => 'Ver con la dirección pedagógica',
                'en' => 'Check results with the Pedagogy department',
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

        EvaluationType::create([
            'id' => 2,
            'name' => [
                'fr' => 'COMPÉTENCES',
                'es' => 'COMPETENCIAS',
                'en' => 'SKILLS',
            ],
        ]);

        AttendanceType::create([
            'id' => 1,
            'name' => ['fr' => 'PRÉSENT(E)', 'es' => 'PRESENTE', 'en' => 'PRESENT'],
        ]);

        AttendanceType::create([
            'id' => 2,
            'name' => ['fr' => 'PRÉSENCE PARTIELLE', 'es' => 'PRESENCIA PARCIAL', 'en' => 'PARTIAL PRESENCE'],
        ]);

        AttendanceType::create([
            'id' => 3,
            'name' => ['fr' => 'EXCUSÉ(E)', 'es' => 'JUSTIFICADO', 'en' => 'EXCUSED'],
        ]);

        AttendanceType::create([
            'id' => 4,
            'name' => ['fr' => 'ABSENT(E)', 'es' => 'AUSENTE', 'en' => 'ABSENT'],
        ]);

        ContactRelationship::create([
            'id' => 1,
            'name' => ['fr' => 'FAMILLE', 'es' => 'FAMILIA', 'en' => 'FAMILY'],
        ]);

        ContactRelationship::create([
            'id' => 2,
            'name' => ['fr' => 'TRAVAIL', 'es' => 'TRABAJO', 'en' => 'WORK'],
        ]);

        DB::table('fees')->insert(
            [
                'id' => 1,
                'name' => 'Matricula',
                'price' => '20',
            ]
        );

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

        LeadType::create(['id' => '1', 'name' => 'Active']);
        LeadType::create(['id' => '2', 'name' => 'Inactive']);
        LeadType::create(['id' => '3', 'name' => 'FormerClient']);
        LeadType::create(['id' => '4', 'name' => 'Potential']);
        //LeadType::create(['id' => '5', 'name' => 'Call']); // merged with 4
        //LeadType::create(['id' => '6', 'name' => 'exAlumno']);  // merged with 3
        //LeadType::create(['id' => '7', 'name' => 'oldStudent']);  // merged with 3

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

        factory(Rhythm::class)->create(['name' => 'Intensive']);
        factory(Rhythm::class)->create(['name' => 'Remote']);
        factory(Rhythm::class)->create(['name' => 'Evening']);
        factory(Rhythm::class)->create(['name' => 'Weekend']);

        factory(Room::class)->create(['name' => 'Room 1A']);
        factory(Room::class)->create(['name' => 'Room 1B']);
        factory(Room::class)->create(['name' => 'Computer lab']);
        factory(Room::class)->create(['name' => 'Library']);

        factory(Level::class)->create(['name' => 'Beginner']);
        factory(Level::class)->create(['name' => 'Intermediate']);
        factory(Level::class)->create(['name' => 'Advanced']);

        factory(Teacher::class)->create();
        factory(Teacher::class)->create();
        factory(Teacher::class)->create();

        factory(Year::class)->create([
            'name' => '2020',
        ]);

        foreach (Year::all() as $year) {

            // seed 4 periods inside that year
            DB::table('periods')->insert([
                'name' => 'Period 2 in year '.$year->name,
                'start' => date('Y-m-d', strtotime('first day of april this year')),
                'end' => date('Y-m-d', strtotime('last day of june this year')),
                'year_id' => $year->id,
            ]);

            DB::table('periods')->insert([
                'name' => 'Period 3 in year '.$year->name,
                'start' => date('Y-m-d', strtotime('first day of july this year')),
                'end' => date('Y-m-d', strtotime('last day of august this year')),
                'year_id' => $year->id,
            ]);

            DB::table('periods')->insert([
                'name' => 'Period 4 in year '.$year->name,
                'start' => date('Y-m-d', strtotime('first day of september this year')),
                'end' => date('Y-m-d', strtotime('last day of december this year')),
                'year_id' => $year->id,
            ]);

            // foreach period in year, seed some courses
            foreach ($year->periods as $period) {
                $p1course1 = factory(Course::class)->create([
                    'campus_id' => 1,
                    'rhythm_id' => 1,
                    'level_id' => 1,
                    'volume' => 10,
                    'price' => 100,
                    'start_date' => $period->start,
                    'end_date' => $period->end,
                    'room_id' => 1,
                    'teacher_id' => 3,
                    'period_id' => $period->id,
                ]);

                $p1course1->times()->create(['day' => 1, 'start' => '09:00:00', 'end' => '17:00:00']);
                $p1course1->times()->create(['day' => 2, 'start' => '09:00:00', 'end' => '17:00:00']);
                $p1course1->times()->create(['day' => 3, 'start' => '09:00:00', 'end' => '17:00:00']);
                $p1course1->times()->create(['day' => 4, 'start' => '09:00:00', 'end' => '17:00:00']);
                $p1course1->times()->create(['day' => 5, 'start' => '09:00:00', 'end' => '17:00:00']);

                $p1course2 = factory(Course::class)->create([
                    'campus_id' => 1,
                    'rhythm_id' => 1,
                    'level_id' => 2,
                    'volume' => 10,
                    'price' => 100,
                    'start_date' => $period->start,
                    'end_date' => $period->end,
                    'room_id' => 2,
                    'teacher_id' => 2,
                    'period_id' => $period->id,
                ]);

                $p1course2->times()->create(['day' => 1, 'start' => '14:00:00', 'end' => '17:00:00']);
                $p1course2->times()->create(['day' => 3, 'start' => '14:00:00', 'end' => '17:00:00']);
                $p1course2->times()->create(['day' => 5, 'start' => '14:00:00', 'end' => '17:00:00']);

                $p1course3 = factory(Course::class)->create([
                    'campus_id' => 1,
                    'rhythm_id' => 4,
                    'level_id' => 3,
                    'volume' => 20,
                    'price' => 100,
                    'start_date' => $period->start,
                    'end_date' => $period->end,
                    'room_id' => 3,
                    'teacher_id' => 3,
                    'period_id' => $period->id,
                ]);

                $p1course3->times()->create(['day' => 6, 'start' => '09:00:00', 'end' => '13:00:00']);
                $p1course3->times()->create(['day' => 0, 'start' => '09:00:00', 'end' => '13:00:00']);

                // create some "random" enrollments so that reports apear to have real data

                for ($i = 0; $i < random_int(5, 19); $i++) {
                    $student = factory(Student::class)->create();
                    $student->enroll($p1course1);
                }

                for ($i = 0; $i < random_int(5, 19); $i++) {
                    $student = factory(Student::class)->create();
                    $student->enroll($p1course2);
                }

                for ($i = 0; $i < random_int(5, 19); $i++) {
                    $student = factory(Student::class)->create();
                    $student->enroll($p1course3);
                }
            }
        }
    }
}
