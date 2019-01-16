<?php

/* Routes protected by Backpack */
Route::group(
    ['middleware' => ['web']],
    function () {
        
    // dashboard and home routes
    Route::get('/home', 'HomeController@index')->name('home');
    Route::get('/', 'HomeController@index');

    // add additional contacts to a user
    Route::post('user/addcontact', 'UserDataController@store');


    /* Course Skills update */
    Route::get('course/{course}/skills', 'CourseSkillController@edit')->middleware('permission:grades.edit');
    Route::patch('course/{course}/skills', 'CourseSkillController@update')->middleware('permission:grades.edit');

    /* Skills Evaluation update */
    Route::get('course/{course}/skillsevaluation', 'CourseSkillEvaluationController@index')->middleware('permission:grades.edit');
    Route::get('course/{course}/skillsevaluation/{student}', 'CourseSkillEvaluationController@edit')->middleware('permission:grades.edit');
    Route::post('skillsevaluation', 'CourseSkillEvaluationController@store')->middleware('permission:grades.edit');
    
    /* Course grades update */
    Route::get('course/{course}/grades', 'GradeController@edit')->middleware('permission:grades.edit');
    Route::post('grades', 'GradeController@store')->middleware('permission:grades.edit');
    Route::delete('grades', 'GradeController@destroy')->middleware('permission:grades.edit');

    /* Course attendance overview  */
    Route::get('attendance', 'AttendanceController@index')->middleware('permission:attendance.view'); // queryString parameters for period
    Route::get('attendance/course/{course}', 'AttendanceController@showCourse')->middleware('permission:attendance.view');
    Route::get('attendance/event/{event}', 'AttendanceController@showEvent')->middleware('permission:attendance.edit');;
    Route::post('attendance', 'AttendanceController@store')->middleware('permission:attendance.edit');;

    /* Course Times update */
    Route::get('coursetime/{course}/get', 'CourseTimeController@show')->middleware('permission:courses.edit');
    Route::get('coursetime/{course}/edit', 'CourseTimeController@edit')->middleware('permission:courses.edit');;
    Route::post('coursetime/{course}', 'CourseTimeController@store')->middleware('permission:courses.edit');;
    Route::delete('coursetime/{id}', 'CourseTimeController@destroy')->middleware('permission:courses.edit');;

    // Course Events routes
    Route::get('course/{course}/events/get', 'EventController@get_course_events')->middleware('permission:courses.edit');;

    // Comments routes
    Route::post('comment', 'CommentController@store')->middleware('permission:comments.edit');
    Route::delete('comment/{comment}', 'CommentController@destroy')->middleware('permission:comments.edit');

    // Enrollments routes
    Route::get('students/{student}/enroll/{period?}', 'EnrollmentController@create')->middleware('permission:enrollments.create');

    // todo make singular and move all methods to the same controller
    Route::post('enrollments', 'EnrollmentController@store')->middleware('permission:enrollments.create');
    Route::resource('enrollments', 'EnrollmentController')->middleware('permission:enrollments.view');

    // Billing and Invoicing routes
    Route::get('enrollments/{enrollment}/bill', 'EnrollmentController@bill')->middleware('permission:invoices.create');

    Route::get('cart/{id}', 'CartController@show')->middleware('permission:invoices.create');
    Route::delete('cart/{id}', 'CartController@destroy')->middleware('permission:invoices.create');

    Route::post('cart/{student}/checkout', 'PreInvoiceController@store')->middleware('permission:invoices.create');

    // add an enrollment to the cart for checkout
    Route::post('product', 'ProductController@store')->middleware('permission:invoices.create');

    // remove something from the cart
    Route::delete('product', 'ProductController@destroy')->middleware('permission:invoices.create');

    // Preinvoices management
    Route::patch('invoice/{preInvoice}', 'PreInvoiceController@update')->middleware('permission:invoices.create');

    // course result management -- todo some of these routes will need to be visible by students
    Route::resource('result', 'ResultController')->middleware('permission:results.view');

});