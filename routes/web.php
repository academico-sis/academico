<?php



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


    /* Course Skills update */
    Route::get('course/{course}/skills', 'CourseSkillController@edit');
    Route::patch('course/{course}/skills', 'CourseSkillController@update');

    /* Skills Evaluation update */
    Route::get('course/{course}/skillsevaluation/{student}', 'CourseSkillEvaluationController@edit');
    Route::post('skillsevaluation', 'CourseSkillEvaluationController@store');
    
    /* Course grades update */
    Route::get('course/{course}/grades', 'GradeController@edit');
    Route::post('grades', 'GradeController@store');
    Route::delete('grades', 'GradeController@destroy');



    /* Course attendance overview  */
    Route::get('attendance', 'AttendanceController@index'); // queryString parameters for period
    
    Route::get('attendance/course/{course}', 'AttendanceController@showCourse'); // route model binding

    Route::get('attendance/event/{event}', 'AttendanceController@showEvent'); // route model binding

    /* Course Times update */
    Route::get('coursetime/{course}/get', 'CourseTimeController@show');
    Route::get('coursetime/{course}/edit', 'CourseTimeController@edit');
    Route::post('coursetime/{course}', 'CourseTimeController@store');
    Route::delete('coursetime/{id}', 'CourseTimeController@destroy');

    // Course Events routes

    Route::get('course/{course}/events/get', 'EventController@get_course_events');

    // Comments routes

    Route::post('comment', 'CommentController@store');
    Route::delete('comment/{comment}', 'CommentController@destroy');

    // Enrollments routes
    Route::get('students/{student}/enroll/{period?}', 'EnrollmentController@create');
    Route::post('enrollments', 'EnrollmentController@store');
    Route::resource('enrollments', 'EnrollmentController');

    // Billing and Invoicing routes
    Route::get('enrollments/{enrollment}/bill', 'EnrollmentController@bill');

    Route::resource('cart', 'CartController');
    Route::post('cart/{student}/checkout', 'PreInvoiceController@store');

    // add an enrollment to the cart for checkout
    Route::post('product', 'ProductController@store');

    // remove something from the cart
    Route::delete('product', 'ProductController@destroy');

    // Preinvoices management
    Route::patch('invoice/{preInvoice}', 'PreInvoiceController@update');

    // course result management -- todo some of these routes will need to be visible by students
    //Route::resource('result', 'ResultController');

});