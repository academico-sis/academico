<?php

namespace App\Models\Policies;

use App\Models\Contact;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class ContactPolicy
{
    use HandlesAuthorization;

    /**
     * the user may update this information if they are the student whom the contact is related to,
     * or if they have explicit permission to update any contact (students).
     */
    public function update(User $user, Contact $contact)
    {
        if ($user->can('student.edit')) {
            return true;
        }

        if ($user->student) {
            return $user->student->id == $contact->student_id;
        }

        return false;
    }
}
