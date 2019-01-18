<?php

/* Routes protected by Backpack */
Route::group(
    ['middleware' => ['web']],
    function () {
        
    // dashboard and home routes
    Route::get('/home', 'HomeController@index')->name('home');
    Route::get('/', 'HomeController@index');
    Route::get('/admin', 'HomeController@admin');

    // add additional contacts to a user
    Route::post('user/addcontact', 'UserDataController@store');
});


// EVALUATION RELATED ROUTES
Route::group(
    ['middleware' => ['web', 'permission:grades.edit']],
    function () {

    /* Course Skills update */
    Route::get('course/{course}/skills', 'CourseSkillController@edit');
    Route::patch('course/{course}/skills', 'CourseSkillController@update');

    /* Skills Evaluation update */
    Route::get('course/{course}/skillsevaluation', 'CourseSkillEvaluationController@index');
    Route::get('course/{course}/skillsevaluation/{student}', 'CourseSkillEvaluationController@edit');
    Route::post('skillsevaluation', 'CourseSkillEvaluationController@store');
    
    /* Course grades update */
    Route::get('course/{course}/grades', 'GradeController@edit');
    Route::post('grades', 'GradeController@store');
    Route::delete('grades', 'GradeController@destroy');
    Route::post('course/gradetype', 'GradeController@add_grade_type_to_course');
    Route::delete('course/{course}/gradetype/{gradetype}', 'GradeController@remove_grade_type_from_course');

});


// ATTENDANCE RELATED ROUTES
Route::group(
    ['middleware' => ['web', 'permission:attendance.view']],
    function () {

    /* Course attendance overview  */
    Route::get('attendance', 'AttendanceController@index'); // queryString parameters for period
    Route::get('attendance/course/{course}', 'AttendanceController@showCourse');
    Route::get('attendance/event/{event}', 'AttendanceController@showEvent');
    Route::post('attendance', 'AttendanceController@store');
});


// COURSE EDITION ROUTES
Route::group(
    ['middleware' => ['web', 'permission:courses.edit']],
    function () {

    /* Course Times update */
    Route::get('coursetime/{course}/get', 'CourseTimeController@show');
    Route::get('coursetime/{course}/edit', 'CourseTimeController@edit');
    Route::post('coursetime/{course}', 'CourseTimeController@store');
    Route::delete('coursetime/{id}', 'CourseTimeController@destroy');

    // Course Events routes
    Route::get('course/{course}/events/get', 'EventController@get_course_events');
});


// ENROLLMENTS RELATED ROUTES
Route::group(
    ['middleware' => ['web', 'permission:enrollments.view']],
    function () {

    // Comments routes
    Route::post('comment', 'CommentController@store');
    Route::delete('comment/{comment}', 'CommentController@destroy');

    // Enrollments routes
    Route::get('/student/{student}/enroll/{period?}', 'EnrollmentController@create');

    // todo make singular and move all methods to the same controller
    Route::post('student/enroll', 'EnrollmentController@store');

    // Billing and Invoicing routes
    Route::get('enrollments/{enrollment}/bill', 'EnrollmentController@bill');

    Route::get('cart/{id}', 'CartController@show');
    Route::delete('cart/{id}', 'CartController@destroy');

    Route::post('cart/{student}/checkout', 'PreInvoiceController@store');

    // add an enrollment to the cart for checkout
    Route::post('product', 'ProductController@store');

    // remove something from the cart
    Route::delete('product', 'ProductController@destroy');

    // Preinvoices management
    Route::patch('invoice/{preInvoice}', 'PreInvoiceController@update');

    // course result management -- todo some of these routes will need to be visible by students
    Route::resource('result', 'ResultController');

});

// TEACHER ROUTES
Route::group(
    ['middleware' => ['web', 'role:teacher']],
    function () {

        Route::get('teacher/dashboard', 'HomeController@teacher');



});