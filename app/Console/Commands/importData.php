<?php

namespace App\Console\Commands;

use App\Models\Course;
use App\Models\Level;
use App\Models\Period;
use App\Models\Rhythm;
use App\Models\Student;
use App\Models\User;
use App\Models\Year;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use League\Csv\Reader;

class importData extends Command
{
    protected $signature = 'academico:import';

    protected $description = 'Command description';

    private function stripAccents($str)
    {
        return str_replace(' ', '', strtr(utf8_decode($str), utf8_decode('àáâãäçèéêëìíîïñòóôõöùúûüýÿÀÁÂÃÄÇÈÉÊËÌÍÎÏÑÒÓÔÕÖÙÚÛÜÝ'), 'aaaaaceeeeiiiinooooouuuuyyAAAAACEEEEIIIINOOOOOUUUUY'));
    }

    public function handle()
    {
        $reader = Reader::createFromPath(\Storage::path('enrollments.csv'), 'r');
        $reader->setHeaderOffset(0);
        $records = $reader->getRecords();

        foreach ($records as $offset => $record) {
            // if the course already exists, retrieve it
            $period = Period::firstWhere('name', $record['year']);

            // Retrieve or create the student
            $email = $record['email'] !== '' ? $record['email'] : $this->stripAccents($record['firstname']).'.'.$this->stripAccents($record['name']).'@academico.afsantiago.es';

            if (User::where('email', $email)->count() > 0) {
                $user = User::where('email', $email)->first();
                if (($user->firstname !== trim($record['firstname']) || $user->lastname !== trim($record['name']))) {
                    $email = $this->stripAccents($record['firstname']).'.'.$this->stripAccents($record['name']).'@academico.afsantiago.es';
                }
            }

            $user = User::firstOrCreate([
                'firstname' => trim($record['firstname']),
                'lastname' => trim($record['name']),
                'locale' => 'es',
                'email' => $email,
            ], [
                'password' => Hash::make(Str::random(12)),
            ]);

            $student = Student::firstOrCreate(
                ['id' => $user->id],
                [
                    'idnumber' => $record['idnumber'] !== '' ? $record['idnumber'] : null,
                    'address' => $record['address'] !== '' ? $record['address'] : null,
                    'city' => $record['city'] !== '' ? $record['city'] : null,
                    'birthdate' => $record['birthdate'] !== '' ? Carbon::createFromFormat('d/m/Y', $record['birthdate'])->toDateString() : null,
                    'zip_code' => $record['zip_code'] !== '' ? $record['zip_code'] : null,
                    'iban' => $record['IBAN'] !== '' ? $record['IBAN'] : null,
                    'bic' => $record['bic_code'] !== '' ? $record['bic_code'] : null,
                ]
            );

            // add phone number
            if ($record['phone'] !== '' && $student->phone()->where('phone_number', $record['phone'])->count() == 0) {
                $student->phone()->create(['phone_number' => $record['phone']]);
            }

            if ($period) {
                $course = Course::where('period_id', $period->id)->where('name', $record['course_name']);
                if ($course->exists()) {
                    $course = $course->first();
                } // otherwise try and build a course matching the info we have
                else {
                    // if the level name is contained in the course name, assign it.
                    foreach (Level::all() as $level) {
                        if (Str::contains($record['course_name'], $level->name)) {
                            $levelID = $level->id;
                            break;
                        }
                    }

                    // if the rhythm name is contained in the course name, assign it.
                    foreach (Rhythm::all() as $rhythm) {
                        if (Str::contains($record['course_name'], $rhythm->name)) {
                            $rhythmID = $rhythm->id;
                            break;
                        }
                    }

                    $course = Course::firstOrCreate([
                        'campus_id' => 1,
                        'rhythm_id' => $rhythmID ?? null,
                        'level_id' => $levelID ?? null,
                        'name' => $record['course_name'],
                        'period_id' => $period->id,
                        'start_date' => $period->start,
                        'end_date' => $period->end,
                    ]);

                    unset($rhythmID);
                    unset($levelID);
                }
                $student->enroll($course);
            }
        }

        /*$reader = Reader::createFromPath(\Storage::path('years.csv'), 'r');
        $records = $reader->getRecords();

        foreach ($records as $offset => $record) {
            Year::firstOrCreate([
                'name' => $record[0],
            ]);
        }

        foreach (Year::all() as $year) {
            Period::create([
                'name' => $year->name,
                'year_id' => $year->id,
                'start' => \Carbon\Carbon::parse('01/01/' . $year->name)->format('Y-m-d'),
                'end' => \Carbon\Carbon::parse('12/31/' . $year->name)->format('Y-m-d'),
            ]);
        }*/
    }
}
