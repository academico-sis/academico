<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Backpack\CRUD\CrudTrait;

class EvaluationType extends Model
{
    public $timestamps = false;
    use CrudTrait;
    protected $guarded = ['id'];

    public function courses()
    {
        return $this->belongsToMany('\App\Models\Course');
    }
}
