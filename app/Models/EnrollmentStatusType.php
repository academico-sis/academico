<?php

namespace App\Models;

use App\Models\Concerns\HasFallbackTranslations;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EnrollmentStatusType extends Model
{
    use HasFactory, HasFallbackTranslations;

    public array $translatable = ['name'];

    public $timestamps = false;

    protected $fillable = ['color'];
}
