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
  
    public static function get_default_period()
    {
        return Period::where('start', '<=', date('Y-m-d'))
        ->where('end', '>=', date('Y-m-d'))
        ->first();
    }

    public function enrollments()
    {
        return $this->hasManyThrough('App\Models\Enrollment', 'App\Models\Course');
    }
}
