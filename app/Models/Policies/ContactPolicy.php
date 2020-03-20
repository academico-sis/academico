<?php

namespace App\Models\Policies;

use App\Models\Contact;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class ContactPolicy
{
    use HandlesAuthorization;

    /**
     * Create a new policy instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * the user may update this information if they are the student whom the contact is related to,
     * or if they have explicit permission to update any contact (students).
     */
    public function update(User $user, Contact $contact)
    {
        return $user->student->id ?? null == $contact->student_id || $user->can('student.edit');
    }
}
