<?php

// custom user CRUD controller
Route::group(['prefix' => 'admin', 'middleware' => ['admin']], function () {
    CRUD::resource('user', '\App\Http\Controllers\Admin\UserCrudController');
});

// routes used to add additional contacts to a user
// these routes may be used by guests... -> todo find another protection.
//Route::get('users/{user}/addcontact', 'UserDataController@create');
Route::post('users/addcontact', 'UserDataController@store');


/* Routes protected by Backpack */
Route::group(
    ['middleware' => 'web'],
    function () {
        
    // dashboard and home routes
    Route::get('/home', 'HomeController@index')->name('home');
    Route::get('/', 'HomeController@index');

    // Course management routes
    Route::get('courses', 'CourseController@index');
    Route::get('courses/{course}', 'CourseController@show');

    Route::get('courses/create', 'CourseController@create');
    Route::post('courses', 'CourseController@store');
    
    Route::delete('courses', 'CourseController@destroy');


    /* Course Teacher update */
    Route::get('courses/{course}/teacher', 'CourseTeacherController@edit');
    Route::patch('courses/{course}/teacher', 'CourseTeacherController@update');

    /* Course Room update */
    Route::get('courses/{course}/room', 'CourseRoomController@edit');
    Route::patch('courses/{course}/room', 'CourseRoomController@update');

    /* Course Evaluation Type update */
    Route::get('courses/{course}/evaluation', 'CourseEvaluationController@edit');
    Route::patch('courses/{course}/evaluation', 'CourseEvaluationController@update');

    /* Course Skills update */
    Route::get('courses/{course}/skills', 'CourseSkillsController@edit');
    Route::patch('courses/{course}/skills', 'CourseSkillsController@update');

    /* Skills Evaluation update */
    Route::get('courses/{course}/skillsevaluation', 'CourseSkillsController@show');

    /* Course grades update */
    Route::get('courses/{course}/grades', 'GradeController@edit');
    Route::post('grades', 'GradeController@store');
    Route::delete('grades', 'GradeController@destroy');

    /* Course attendance overview  */
    Route::get('courses/{course}/attendance', 'AttendanceController@showCourse'); // showStudent method will exist
    Route::get('attendance/get', 'AttendanceController@get'); // queryString parameters
    Route::get('attendance/{event}', 'AttendanceController@showEvent'); // route model binding


    /* Course Times update */
    Route::get('courses/{course}/time/get', 'CourseTimeController@show');
    Route::get('courses/{course}/time', 'CourseTimeController@edit');
    Route::post('courses/{course}/time', 'CourseTimeController@store');
    Route::delete('coursetime/{id}', 'CourseTimeController@destroy');


    // Student management routes
    Route::get('students/get', 'StudentController@get');
    Route::resource('students', 'StudentController');

    // Enrollments routes
    Route::get('students/{student}/enroll/{period?}', 'EnrollmentController@create');
    Route::post('enrollments', 'EnrollmentController@store');
    Route::resource('enrollments', 'EnrollmentController');

    // Billing and Invoicing routes
    Route::get('enrollments/{enrollment}/bill', 'EnrollmentController@bill');

    Route::resource('carts', 'CartController');
    Route::post('carts/{student}/checkout', 'PreInvoiceController@store');

    // add an enrollment to the cart for checkout
    Route::post('products', 'ProductController@store');

    // remove something from the cart
    Route::delete('products', 'ProductController@destroy');

    // Preinvoices management
    Route::get('invoices', 'PreInvoiceController@index');
    Route::get('invoices/{preInvoice}', 'PreInvoiceController@show');
    Route::patch('invoices/{preInvoice}', 'PreInvoiceController@update');

    // course result management -- todo some of these routes will need to be visible by students
    Route::resource('results', 'ResultController');

});