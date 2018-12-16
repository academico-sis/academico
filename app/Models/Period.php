<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Backpack\CRUD\CrudTrait;

class Period extends Model
{
    use CrudTrait;

    protected $table = 'periods';
    // protected $primaryKey = 'id';
    public $timestamps = false;
    // protected $guarded = ['id'];
    protected $fillable = ['name', 'year_id'];
    // protected $hidden = [];
    // protected $dates = [];
  
}
