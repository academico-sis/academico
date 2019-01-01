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

});