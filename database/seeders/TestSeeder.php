<?php

namespace Database\Seeders;

use App\Models\Attendance;
use App\Models\Book;
use App\Models\Campus;
use App\Models\Comment;
use App\Models\Contact;
use App\Models\Course;
use App\Models\CourseTime;
use App\Models\Enrollment;
use App\Models\Event;
use App\Models\Fee;
use App\Models\Grade;
use App\Models\GradeType;
use App\Models\GradeTypeCategory;
use App\Models\Level;
use App\Models\Partner;
use App\Models\Period;
use App\Models\PhoneNumber;
use App\Models\Rhythm;
use App\Models\Room;
use App\Models\Scholarship;
use App\Models\Skills\Skill;
use App\Models\Skills\SkillEvaluation;
use App\Models\Skills\SkillType;
use App\Models\Student;
use App\Models\Teacher;
use App\Models\User;
use App\Models\Year;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TestSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->call([
            ReferenceDataSeeder::class,
            PermissionsSeeder::class,
        ]);

        // extra campus for tests
        Campus::create([
            'id' => 2,
            'name' => [
                'en' => 'External',
                'es' => 'Externo',
                'fr' => 'Externe',
            ],
        ]);

        DB::table('fees')->insert([
            'id' => 1,
            'name' => 'Matricula',
            'price' => '20',
        ]);

        Fee::factory()->create(['name' => 'Late Registration Fee', 'price' => 15, 'product_code' => 'LATE']);

        // Admin user
        $admin = User::factory()->create([
            'email' => 'contact@thomasdebay.com',
            'firstname' => 'Admin',
            'lastname' => 'User',
            'password' => '$2y$10$TKh8H1.PfQx37YgCzwiKb.KjNyWgaHb9cbcoQgdIVFlYg7B77UdFm', // secret
        ]);
        $admin->assignRole('admin');

        // Secretary user
        $secretary = User::factory()->create([
            'firstname' => 'Marie',
            'lastname' => 'Dupont',
            'email' => 'secretary@academico.test',
            'password' => '$2y$10$TKh8H1.PfQx37YgCzwiKb.KjNyWgaHb9cbcoQgdIVFlYg7B77UdFm',
        ]);
        $secretary->assignRole('secretary');

        // Rhythms
        $intensive = Rhythm::factory()->create(['name' => 'Intensive']);
        $remote = Rhythm::factory()->create(['name' => 'Remote']);
        $evening = Rhythm::factory()->create(['name' => 'Evening']);
        $weekend = Rhythm::factory()->create(['name' => 'Weekend']);
        $rhythms = [$intensive, $remote, $evening, $weekend];

        // Rooms
        $rooms = collect([
            Room::factory()->create(['name' => 'Room 1A', 'campus_id' => 1]),
            Room::factory()->create(['name' => 'Room 1B', 'campus_id' => 1]),
            Room::factory()->create(['name' => 'Computer lab', 'campus_id' => 1]),
            Room::factory()->create(['name' => 'Library', 'campus_id' => 1]),
            Room::factory()->create(['name' => 'Room 2A', 'campus_id' => 2]),
        ]);

        // Levels
        $beginner = Level::factory()->create(['name' => 'Beginner']);
        $intermediate = Level::factory()->create(['name' => 'Intermediate']);
        $advanced = Level::factory()->create(['name' => 'Advanced']);
        $levels = [$beginner, $intermediate, $advanced];

        // Books
        $books = collect([
            Book::factory()->create(['name' => 'Alter Ego+ A1', 'price' => 35, 'product_code' => 'AE-A1']),
            Book::factory()->create(['name' => 'Alter Ego+ A2', 'price' => 35, 'product_code' => 'AE-A2']),
            Book::factory()->create(['name' => 'Alter Ego+ B1', 'price' => 38, 'product_code' => 'AE-B1']),
        ]);

        // Scholarships
        $scholarship = Scholarship::factory()->create(['name' => 'Merit Scholarship']);
        Scholarship::factory()->create(['name' => 'Staff Discount']);

        // Partner for external courses
        $partner = Partner::factory()->create(['name' => 'City Business School']);

        // Year and periods
        $year = Year::create(['name' => (string) now()->year]);

        $currentPeriod = Period::factory()->create([
            'name' => 'Semester 1',
            'start' => now()->subDays(45)->format('Y-m-d'),
            'end' => now()->addDays(45)->format('Y-m-d'),
            'year_id' => $year->id,
            'order' => 1,
        ]);

        $pastPeriod = Period::factory()->create([
            'name' => 'Previous Semester',
            'start' => now()->subMonths(6)->format('Y-m-d'),
            'end' => now()->subMonths(3)->format('Y-m-d'),
            'year_id' => $year->id,
            'order' => 0,
        ]);

        // Teachers (5)
        $teachers = Teacher::factory()->count(5)->create();

        // Grade types
        $gradeCategories = collect([
            GradeTypeCategory::factory()->create(['name' => 'Exams']),
            GradeTypeCategory::factory()->create(['name' => 'Participation']),
            GradeTypeCategory::factory()->create(['name' => 'Assignments']),
        ]);

        $gradeTypes = collect([
            GradeType::factory()->create(['name' => 'Midterm Exam', 'total' => 20, 'grade_type_category_id' => $gradeCategories[0]->id]),
            GradeType::factory()->create(['name' => 'Final Exam', 'total' => 20, 'grade_type_category_id' => $gradeCategories[0]->id]),
            GradeType::factory()->create(['name' => 'Class Participation', 'total' => 20, 'grade_type_category_id' => $gradeCategories[1]->id]),
            GradeType::factory()->create(['name' => 'Homework', 'total' => 20, 'grade_type_category_id' => $gradeCategories[2]->id]),
        ]);

        // Skill types and skills
        $skillTypes = collect([
            SkillType::factory()->create(['shortname' => 'CO', 'name' => 'Oral Comprehension']),
            SkillType::factory()->create(['shortname' => 'CE', 'name' => 'Written Comprehension']),
            SkillType::factory()->create(['shortname' => 'PO', 'name' => 'Oral Production']),
            SkillType::factory()->create(['shortname' => 'PE', 'name' => 'Written Production']),
        ]);

        $skills = collect();
        foreach ($skillTypes as $skillType) {
            foreach ($levels as $level) {
                $skills->push(Skill::factory()->create([
                    'name' => $skillType->name.' - '.$level->name,
                    'skill_type_id' => $skillType->id,
                    'level_id' => $level->id,
                    'default_weight' => 1,
                    'order' => $skills->count() + 1,
                ]));
            }
        }

        // Students (30)
        $students = Student::factory()->count(30)->create();

        // Give some students phone numbers and contacts
        $students->take(15)->each(function (Student $student) {
            PhoneNumber::factory()->create([
                'phoneable_id' => $student->id,
                'phoneable_type' => Student::class,
            ]);

            Contact::factory()->create([
                'student_id' => $student->id,
                'relationship_id' => 1, // Family
            ]);
        });

        // Create current period courses (6 internal courses)
        $courseNames = [
            ['French A1 Intensive', $beginner, $intensive],
            ['French A1 Evening', $beginner, $evening],
            ['French A2 Intensive', $intermediate, $intensive],
            ['French A2 Remote', $intermediate, $remote],
            ['French B1 Intensive', $advanced, $intensive],
            ['French B1 Weekend', $advanced, $weekend],
        ];

        $courses = collect();
        foreach ($courseNames as $i => [$name, $level, $rhythm]) {
            $teacher = $teachers[$i % $teachers->count()];
            $room = $rooms[$i % $rooms->count()];

            $course = Course::factory()->create([
                'name' => $name,
                'campus_id' => 1,
                'rhythm_id' => $rhythm->id,
                'level_id' => $level->id,
                'room_id' => $room->id,
                'teacher_id' => $teacher->id,
                'period_id' => $currentPeriod->id,
                'volume' => fake()->randomElement([30, 40, 60, 80]),
                'price' => fake()->randomElement([150, 200, 250, 300]),
                'spots' => fake()->numberBetween(8, 20),
                'start_date' => $currentPeriod->start,
                'end_date' => $currentPeriod->end,
            ]);

            // Schedule: 2 course time slots per course
            $days = fake()->randomElements([1, 2, 3, 4, 5], 2);
            foreach ($days as $day) {
                $startHour = $rhythm === $evening ? 18 : ($rhythm === $weekend ? 9 : fake()->randomElement([8, 10, 14]));
                CourseTime::factory()->create([
                    'course_id' => $course->id,
                    'day' => $day,
                    'start' => sprintf('%02d:00:00', $startHour),
                    'end' => sprintf('%02d:00:00', $startHour + 2),
                ]);
            }

            $courses->push($course);
        }

        // One external course with partner
        $externalCourse = Course::factory()->create([
            'name' => 'French for Business (Partner)',
            'campus_id' => 2,
            'rhythm_id' => $evening->id,
            'level_id' => $intermediate->id,
            'room_id' => $rooms->last()->id,
            'teacher_id' => $teachers->last()->id,
            'period_id' => $currentPeriod->id,
            'partner_id' => $partner->id,
            'volume' => 40,
            'price' => 180,
            'spots' => 15,
            'start_date' => $currentPeriod->start,
            'end_date' => $currentPeriod->end,
        ]);
        $courses->push($externalCourse);

        // Past period course (archived feel)
        $pastCourse = Course::factory()->create([
            'name' => 'French A1 Previous Semester',
            'campus_id' => 1,
            'rhythm_id' => $intensive->id,
            'level_id' => $beginner->id,
            'room_id' => $rooms->first()->id,
            'teacher_id' => $teachers->first()->id,
            'period_id' => $pastPeriod->id,
            'volume' => 60,
            'price' => 250,
            'spots' => 15,
            'start_date' => $pastPeriod->start,
            'end_date' => $pastPeriod->end,
        ]);

        // its sessions are created from its weekly schedule
        CourseTime::factory()->create([
            'course_id' => $pastCourse->id,
            'day' => 2,
            'start' => '10:00:00',
            'end' => '12:00:00',
        ]);

        // Enroll students in current courses (4-8 students per course)
        $allEnrollments = collect();
        $studentIndex = 0;

        foreach ($courses as $course) {
            $enrollCount = fake()->numberBetween(4, 8);

            for ($j = 0; $j < $enrollCount; $j++) {
                $student = $students[$studentIndex % $students->count()];
                $studentIndex++;

                // Mix of paid and pending statuses
                $statusId = fake()->randomElement([1, 1, 2, 2, 2]);

                $enrollment = Enrollment::factory()->create([
                    'student_id' => $student->id,
                    'course_id' => $course->id,
                    'status_id' => $statusId,
                    'responsible_id' => $admin->id,
                ]);

                $allEnrollments->push($enrollment);
            }
        }

        // Past course enrollments (all paid)
        $students->take(6)->each(function (Student $student) use ($pastCourse, $admin, $allEnrollments) {
            $enrollment = Enrollment::factory()->create([
                'student_id' => $student->id,
                'course_id' => $pastCourse->id,
                'status_id' => 2,
                'responsible_id' => $admin->id,
            ]);
            $allEnrollments->push($enrollment);
        });

        // Attach a scholarship to a few enrollments
        $allEnrollments->random(3)->each(function (Enrollment $enrollment) use ($scholarship) {
            $enrollment->scholarships()->attach($scholarship->id);
        });

        // Create attendance for past events (all past events get attendance)
        $pastEvents = Event::where('course_id', $pastCourse->id)->get();
        $pastEnrollments = Enrollment::where('course_id', $pastCourse->id)->get();

        foreach ($pastEvents as $event) {
            foreach ($pastEnrollments as $enrollment) {
                Attendance::factory()->create([
                    'student_id' => $enrollment->student_id,
                    'event_id' => $event->id,
                    'attendance_type_id' => fake()->randomElement([1, 1, 1, 1, 2, 3, 4]),
                ]);
            }
        }

        // Current events: attendance for events that are in the past
        foreach ($courses as $course) {
            $courseEnrollments = $allEnrollments->where('course_id', $course->id);
            $pastCourseEvents = $course->events()->where('start', '<', now())->get();

            foreach ($pastCourseEvents as $event) {
                foreach ($courseEnrollments as $enrollment) {
                    Attendance::factory()->create([
                        'student_id' => $enrollment->student_id,
                        'event_id' => $event->id,
                        'attendance_type_id' => fake()->randomElement([1, 1, 1, 1, 2, 3, 4]),
                    ]);
                }
            }
        }

        // Grades for past course enrollments
        foreach ($pastEnrollments as $enrollment) {
            foreach ($gradeTypes as $gradeType) {
                Grade::factory()->create([
                    'enrollment_id' => $enrollment->id,
                    'grade_type_id' => $gradeType->id,
                    'grade' => fake()->randomFloat(1, 5, 20),
                ]);
            }
        }

        // Grades for some current enrollments (midterm done, final pending)
        $allEnrollments->filter(fn ($e) => $e->course_id !== $pastCourse->id)
            ->random(min(15, $allEnrollments->count()))
            ->each(function (Enrollment $enrollment) use ($gradeTypes) {
                // Midterm + participation grades
                Grade::factory()->create([
                    'enrollment_id' => $enrollment->id,
                    'grade_type_id' => $gradeTypes[0]->id,
                    'grade' => fake()->randomFloat(1, 8, 19),
                ]);
                Grade::factory()->create([
                    'enrollment_id' => $enrollment->id,
                    'grade_type_id' => $gradeTypes[2]->id,
                    'grade' => fake()->randomFloat(1, 10, 20),
                ]);
            });

        // Skill evaluations for a few enrollments
        $allEnrollments->random(min(8, $allEnrollments->count()))
            ->each(function (Enrollment $enrollment) use ($skills) {
                $courseLevel = $enrollment->course->level_id;
                $levelSkills = $skills->where('level_id', $courseLevel);

                foreach ($levelSkills as $skill) {
                    SkillEvaluation::factory()->create([
                        'enrollment_id' => $enrollment->id,
                        'skill_id' => $skill->id,
                        'skill_scale_id' => fake()->numberBetween(1, 3),
                    ]);
                }
            });

        // Comments on some enrollments and students
        $allEnrollments->random(min(5, $allEnrollments->count()))
            ->each(function (Enrollment $enrollment) use ($admin) {
                Comment::factory()->create([
                    'commentable_id' => $enrollment->id,
                    'commentable_type' => Enrollment::class,
                    'body' => fake()->sentence(10),
                    'action' => false,
                    'author_id' => $admin->id,
                ]);
            });

        $students->random(5)->each(function (Student $student) use ($admin) {
            Comment::factory()->create([
                'commentable_id' => $student->id,
                'commentable_type' => Student::class,
                'body' => fake()->paragraph(),
                'action' => false,
                'author_id' => $admin->id,
            ]);
        });
    }
}
