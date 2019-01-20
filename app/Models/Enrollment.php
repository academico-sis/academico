<?php

namespace App\Models;

use Carbon\Carbon;
use Backpack\CRUD\CrudTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Enrollment extends Model
{
    use CrudTrait;
    use SoftDeletes;

    protected $fillable = ['user_id', 'course_id', 'parent_id', 'status_id'];


    /**
     * return all pending enrollments without the child enrollments
     */
    public function scopePending($query)
    {
        return $query
            ->where('status_id', 1)
            ->where('parent_id', null)
            ->with('student_data')
            ->with('course_data')
            ->get();
    }

    
    /** FUNCTIONS */
    public function addToCart()
    {
        $product = Cart::firstOrNew([
            'user_id' => $this->student_data->id,
            'product_id' => $this->id,
            'product_type' => Enrollment::class
        ]);

        $product->save();
        return $product->id;
    }


    /** RELATIONS */
    public function student_data()
    {
        return $this->belongsTo('App\Models\User', 'user_id');
    }

    public function course_data()
    {
        return $this->belongsTo('App\Models\Course', 'course_id');
    }
    
    public function pre_invoice()
    {
        return $this->belongsToMany('App\Models\PreInvoice', 'enrollment_pre_invoice', 'enrollment_id', 'pre_invoice_id');
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

    public function enrollment_status()
    {
        return $this->belongsTo('App\Models\EnrollmentStatusType', 'status_id');
    }


    /* Accessors */
    public function getGradesAttribute()
    {
        return Grade::where('course_id', $this->course_data->id)
            ->where('user_id', $this->student_data->id)
            ->with('grade_type')
            ->get();
    }

    public function getSkillsAttribute()
    {
        return SkillEvaluation::where('user_id', $this->student_data->id)
            ->where('course_id', $this->course_data->id)
            ->with('skill')->with('skill_scale')
            ->get();
    }

    public function getStudentNameAttribute()
    {
        return $this->student_data['name'];
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

    public function getDateAttribute()
    {
        return Carbon::parse($this->created_at, 'UTC')->toFormattedDateString();
    }

    public function getChildrenCountAttribute()
    {
        return Enrollment::where('parent_id', $this->id)->count();
    }

    public function getChildrenAttribute()
    {
        return Enrollment::where('parent_id', $this->id)->with('course_data')->get();
    }

    public function getStatusAttribute()
    {
        return $this->status_id;
    }
}
