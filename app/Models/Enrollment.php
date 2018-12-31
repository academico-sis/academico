<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\SoftDeletes;

class Enrollment extends Model
{

    use SoftDeletes;

    protected $fillable = ['user_id', 'course_id'];


    public function student_data()
    {
        return $this->belongsTo('App\Models\Student', 'user_id')->with('self_data');
    }

    public function course_data()
    {
        return $this->belongsTo('\App\Models\Course', 'course_id');
    }
    
    public function pre_invoice()
    {
        return $this->hasMany('\App\Models\PreInvoice');
    }

    public function addToCart()
    {
        $product = Sale::firstOrNew([
            'user_id' => $this->student_data->id,
            'product_id' => $this->course_data->id,
            'product_type' => Course::class
        ]);

        $product->save();
        return $product->id;
    }



    /* Accessors */
    public function getStudentNameAttribute()
    {
        return $this->student_data->self_data['name'];
    }

    public function getStudentIdAttribute()
    {
        return $this->student_data['id'];
    }

    public function getStudentAgeAttribute()
    {
        return Carbon::createFromFormat('Y-m-d', $this->student_data['birthdate'])->age;
    }

    public function getStudentBirthdateAttribute()
    {
        return $this->student_data['birthdate'];
    }

    public function getStudentEmailAttribute()
    {
        return $this->student_data['email'];
    }

    public function comments()
    {
        return $this->morphMany('App\Models\Comment', 'commentable');
    }

    public function result()
    {
        return $this->hasOne('App\Models\Result')
            ->with('result_name');
    }

    public function getDateAttribute()
    {
        return Carbon::parse($this->created_at, 'UTC')->toFormattedDateString();
    }

    public function enrollment_status()
    {
        return $this->belongsTo('\App\Models\EnrollmentStatusType', 'status_id');
    }
}
