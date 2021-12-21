<?php

namespace App\Models\Policies;

use App\Models\Student;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class StudentPolicy
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
     * Determine if the given post can be updated by the user.
     */
    public function show(User $user, Student $student)
    {
        // if the student is enrolled in any class by the user

        return $student->enrollments()->whereHas('course', fn ($q) => $q->where('teacher_id', $user->id))->count() > 0;
    }
}
