<?php

namespace App\Http\Controllers\Admin;

use App\Models\Course;

// VALIDATION: change the requests to match your own file names if you need form validation
use App\Models\Skills\Skill;
use Illuminate\Http\Request;
use App\Http\Requests\SkillRequest as StoreRequest;
use App\Http\Requests\SkillRequest as UpdateRequest;
use Backpack\CRUD\app\Http\Controllers\CrudController;

/**
 * Class SkillCrudController
 * @package App\Http\Controllers\Admin
 * @property-read CrudPanel $crud
 */
class CourseSkillCrudController extends CrudController
{

    public function __construct(Request $request)
    {
        parent::__construct();
        $this->middleware(['permission:evaluation.view']);
        $this->course = $request->query('course');
    }

    
    public function setup()
    {
        /*
        |--------------------------------------------------------------------------
        | CrudPanel Basic Information
        |--------------------------------------------------------------------------
        */
        $this->crud->setModel('App\Models\Skills\Skill');
        $this->crud->setRoute(config('backpack.base.route_prefix') . '/courseskill');
        $this->crud->setEntityNameStrings('courseskill', 'courseskills');


        $this->crud->denyAccess('show');
        $this->crud->denyAccess('update');
        $this->crud->denyAccess('delete');
        $this->crud->denyAccess('create');
        $this->crud->removeAllButtons();

        $permissions = backpack_user()->getAllPermissions();
        if($permissions->contains('name', 'courses.delete'))
        {
            $this->crud->addButtonFromView('top', 'attachSkills', 'attachSkills', 'end');
        }

        $course = $this->course;
        $this->crud->course = $course;
        $this->crud->query = $this->crud->query->whereHas('course', function ($query) use ($course) {
            $query->where('course_id', $course);
        });
        

        /*
        |--------------------------------------------------------------------------
        | CrudPanel Configuration
        |--------------------------------------------------------------------------
        */
        //$this->crud->setFromDb();
        $this->crud->setColumns([

            [ // skill type
                'label'     => 'Type', // Table column heading
                'type'      => 'select',
                'name'      => 'skill_type', // the method that defines the relationship in your Model
                'entity'    => 'skill_type', // the method that defines the relationship in your Model
                'attribute' => 'name', // foreign key attribute that is shown to user
                'model'     => 'skill_type', // foreign key model
             ],

            [
                'label' => "Name", // skill description
                'type' => "text",
                'name' => 'name'
            ],

            [ // skill level
               'label'     => 'Level', // Table column heading
               'type'      => 'select',
               'name'      => 'level', // the method that defines the relationship in your Model
               'entity'    => 'level', // the method that defines the relationship in your Model
               'attribute' => 'name', // foreign key attribute that is shown to user
               'model'     => 'level', // foreign key model
            ],

            [
                'label' => "Weight", // skill description
                'type' => "text",
                'name' => 'weight'
            ],

        ]);
    }

}
