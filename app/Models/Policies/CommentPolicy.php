<?php

namespace App\Models\Policies;

use App\Models\User;
use App\Models\Contact;
use Illuminate\Auth\Access\HandlesAuthorization;

class CommentPolicy
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
     * the user may add a comment for students who are enrolled in one of their courses,
     * or to any student if they have explicit permission to do so
     * 
     */
    public function store_student_comment(User $user, Student $student)
    {     
        return ($student->enrollments()->whereHas('course', function ($q) use ($user) {
            return $q->where('teacher_id', $user->teacher_id);
        })->count() > 0) || $user->can('comments.edit');
        
    }
}
