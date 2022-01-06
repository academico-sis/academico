<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;

class EnrollmentStatusType extends Model
{
    use HasTranslations;

    public array $translatable = ['name'];

    public $timestamps = false;

    public function styling()
    {
        return match($this->id) {
            1 => 'warning',
            2 => 'info',
            default => 'danger',
        };
    }
}
