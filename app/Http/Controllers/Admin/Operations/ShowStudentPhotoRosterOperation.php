<?php

namespace App\Http\Controllers\Admin\Operations;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Gate;
use App\Models\Course;


trait ShowStudentPhotoRosterOperation
{
    /**
     * Define which routes are needed for this operation.
     *
     * @param string $segment    Name of the current entity (singular). Used as first URL segment.
     * @param string $routeName  Prefix of the route name.
     * @param string $controller Name of the current CrudController.
     */
    protected function setupShowStudentPhotoRosterRoutes($segment, $routeName, $controller)
    {
        Route::get($segment.'/{id}/show/roster', [
            'as'        => $routeName.'.showstudentphotoroster',
            'uses'      => $controller.'@showstudentphotoroster',
            'operation' => 'showstudentphotoroster',
        ]);
    }

    /**
     * Add the default settings, buttons, etc that this operation needs.
     */
    protected function setupShowStudentPhotoRosterDefaults()
    {
        $this->crud->allowAccess('showstudentphotoroster');
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
     *
     * @return Response
     */
    public function showstudentphotoroster($course)
    {
        $course = Course::findOrFail($course);

        // The current user is not allowed to view the page
        // I used the same gate as the show function in the CourseCrudController
        // because i believe the authorization for that view is the same as this one
        if (Gate::forUser(backpack_user())->denies('view-course', $course)) {
            abort(403);
        }

        $enrollments = $course->enrollments()->with('student')->get();

        return view('courses/showRoster', compact('course', 'enrollments'));
    }
}
