<?php

namespace App\Models;

use App\User;
use Backpack\Base\app\Notifications\ResetPasswordNotification as ResetPasswordNotification;
use Tightenco\Parental\HasParentModel;
use Backpack\CRUD\CrudTrait; // <------------------------------- this one
use Spatie\Permission\Traits\HasRoles;// <---------------------- and this one
use Illuminate\Database\Eloquent\Builder;

class BackpackUser extends User
{
    use HasParentModel;
    use CrudTrait; // <----- this
    use HasRoles; // <------ and this
    protected $guard_name = 'web';
    protected $table = 'users';


/*     protected static function boot()
    {
        parent::boot();

        // Restrict the results from this class to users who have the teacher role.
        static::addGlobalScope(function (Builder $builder) {
            $builder->role('admin');
            //User::role('writer')->get();
        });
    }
 */

    /**
     * Send the password reset notification.
     *
     * @param string $token
     *
     * @return void
     */
    public function sendPasswordResetNotification($token)
    {
        $this->notify(new ResetPasswordNotification($token));
    }

    /**
     * Get the e-mail address where password reset links are sent.
     *
     * @return string
     */
    public function getEmailForPasswordReset()
    {
        return $this->email;
    }
}
