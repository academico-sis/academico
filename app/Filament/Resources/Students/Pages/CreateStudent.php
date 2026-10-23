<?php

namespace App\Filament\Resources\Students\Pages;

use App\Filament\Resources\Students\StudentResource;
use App\Models\Student;
use App\Models\User;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class CreateStudent extends CreateRecord
{
    protected static string $resource = StudentResource::class;

    protected function handleRecordCreation(array $data): Model
    {
        $user = User::create([
            'firstname' => $data['firstname'],
            'lastname' => $data['lastname'],
            'email' => $data['email'] ?? null,
            'username' => User::generateUniqueUsername($data['firstname'], $data['lastname']),
            'password' => bcrypt(Str::random(16)),
        ]);

        return Student::create([
            'id' => $user->id,
            'idnumber' => $data['idnumber'] ?? null,
            'birthdate' => $data['birthdate'] ?? null,
            'gender_id' => $data['gender_id'],
            'address' => $data['address'] ?? null,
            'zip_code' => $data['zip_code'] ?? null,
            'city' => $data['city'] ?? null,
            'state' => $data['state'] ?? null,
            'country' => $data['country'] ?? null,
            'iban' => $data['iban'] ?? null,
            'bic' => $data['bic'] ?? null,
            'profession_id' => $data['profession_id'] ?? null,
            'institution_id' => $data['institution_id'] ?? null,
        ]);
    }
}
