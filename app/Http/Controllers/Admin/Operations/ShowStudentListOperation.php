<?php

namespace App\Http\Controllers\Admin\Operations;

use App\Models\Course;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Route;

trait ShowStudentListOperation
{
    /**
     * Define which routes are needed for this operation.
     *
     * @param string $segment    Name of the current entity (singular). Used as first URL segment.
     * @param string $routeName  Prefix of the route name.
     * @param string $controller Name of the current CrudController.
     */
    protected function setupShowStudentListRoutes($segment, $routeName, $controller)
    {
        Route::get($segment.'/{id}/show', [
            'as' => $routeName.'.showstudentlist',
            'uses' => $controller.'@showstudentlist',
            'operation' => 'showstudentlist',
        ]);
    }

    /**
     * Add the default settings, buttons, etc that this operation needs.
     */
    protected function setupShowStudentListDefaults()
    {
        $this->crud->allowAccess(['showstudentphotoroster']);
        $this->crud->setOperationSetting('setFromDb', true);

        $this->crud->operation('showstudentphotoroster', function () {
            $this->crud->loadDefaultOperationSettingsFromConfig();
        });

        // $this->crud->operation('list', function () {
        //     $this->crud->addButton('line', 'showstudentphotoroster', 'view', 'crud::buttons.studentRoster', 'beginning');
        // });
    }

    /**
     * Show the view for performing the operation.
     */
    public function showstudentlist($id)
    {
        $course = Course::findOrFail($id);
        // The current is not allowed to view the page
        if (Gate::forUser(backpack_user())->denies('view-course', $course)) {
            abort(403);
        }

        return view('courses/show', [
            'course' => $course,
            'enrollments' => $course->enrollments()->with('student')->get(),
            'widget' => ['course' => $course],
        ]);
    }
}
