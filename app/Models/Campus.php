<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Backpack\CRUD\CrudTrait;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Translatable\HasTranslations;

class Campus extends Model
{
    use CrudTrait;
    use SoftDeletes;

    use HasTranslations;

    public $translatable = ['name'];

    protected $table = 'campuses';
    // protected $primaryKey = 'id';
    public $timestamps = false;
    // protected $guarded = ['id'];
    protected $fillable = ['name'];
    // protected $hidden = [];
    // protected $dates = [];

    /** in the current configuration, the campus with the ID of 1 represent the school itself
     * the campus model with the ID of 2 represents all external courses
     */
  }
