<?php

Route::permanentRedirect('/auth/login', '/');

// save an additional contact for a student
Route::post('user/addcontact', 'ContactController@store')->name('addContact');

Route::get('searchstudents', 'Admin\StudentCrudController@dataAjax');


/* All routes should be protected by Backpack */
Route::group(
    ['middleware' => ['web', 'language']],
    function () {
        
    // dashboard and home routes
    Route::get('/home', 'HomeController@index')->name('home');
    Route::get('/', 'HomeController@index')->name('home');
    Route::get('/admin', 'HomeController@admin')->name('admin');
    Route::get('dashboard/teacher', 'HomeController@teacher')->name('teacherDashboard'); // todo protect
    Route::get('dashboard/teacher/{teacher}/hours', 'HRController@teacher')->name('teacherHours'); // todo protect

    Route::get('dashboard/student', 'HomeController@student')->name('studentDashboard'); // todo protect


    // moodle token login
    Route::get('moodlelogin', 'MoodleController@moodlelogin')->name('moodleLogin');
    //Route::resource('result', 'ResultController');

});



// EVALUATION RELATED ROUTES
Route::group(
    ['middleware' => ['web', 'permission:evaluation.edit', 'language']],
    function () {

    /* Skills Evaluation update */
    Route::get('course/{course}/skillsevaluation', 'CourseSkillEvaluationController@index')->name('courseSkillsEvaluation');
    Route::get('course/{course}/skillsevaluation/{student}', 'CourseSkillEvaluationController@edit')->name('studentSkillsEvaluation');
    Route::post('skillsevaluation', 'CourseSkillEvaluationController@store')->name('storeSkillEvaluation');

    // todo review this entire module
    /* Course grades update */
    Route::get('course/{course}/grades', 'GradeController@edit');
    Route::post('grades', 'GradeController@store');
    Route::delete('grades', 'GradeController@destroy');
    Route::post('course/gradetype', 'GradeController@add_grade_type_to_course');
    Route::delete('course/{course}/gradetype/{gradetype}', 'GradeController@remove_grade_type_from_course');

});


// ATTENDANCE RELATED ROUTES
Route::group(
    ['middleware' => ['web', 'permission:attendance.view', 'language']],
    function () {

    /* Course attendance overview  */
    Route::get('attendance', 'AttendanceController@index')->name('monitorAttendance');
    Route::get('attendance/course/{course}', 'AttendanceController@showCourse')->name('monitorCourseAttendance');
    Route::get('attendance/event/{event}', 'AttendanceController@showEvent')->name('eventAttendance');
    Route::post('attendance', 'AttendanceController@store')->name('storeAttendance');
});


// COURSE EDITION ROUTES
Route::group(
    ['middleware' => ['web', 'permission:courses.edit', 'language']],
    function () {

    /* Course Times update */
    /* todo use route names in Vue Components*/
    Route::get('coursetime/{course}/get', 'CourseTimeController@get');
    Route::get('coursetime/{course}/edit', 'CourseTimeController@edit');
    Route::post('coursetime/{course}', 'CourseTimeController@store');
    Route::delete('coursetime/{id}', 'CourseTimeController@destroy');

    // Course Events routes
    Route::get('course/{course}/events/get', 'EventController@getCourseEvents')->name('getCourseEvents'); // todo use route name
    Route::patch('course/{course}/events/updateRoom', 'EventController@syncEventsRoom')->name('syncEventsRoom'); // resync events with course data
    Route::patch('course/{course}/events/updateTeacher', 'EventController@syncEventsTeacher')->name('syncEventsTeacher');  // resync events with course data

    Route::patch('calendar/teacher', 'EventController@update_course_teacher'); // todo use previous route if possible
    Route::patch('calendar/room', 'EventController@update_course_room'); // todo use previous route if possible
});


// Comments routes
Route::group(
    ['middleware' => ['web', 'permission:comments.edit', 'language']],
    function () {
        Route::post('comment', 'CommentController@store')->name('storeComment');
        Route::post('resultcomment', 'CommentController@storeresult');
        Route::delete('comment/{comment}', 'CommentController@destroy');
    }
);

// Enrollments, Billing and Invoicing routes
Route::group(
    ['middleware' => ['web', 'permission:enrollments.create', 'language']],
    function () {
        
    // creates a new enrollment
    Route::post('student/enroll', 'EnrollmentController@store')->name('storeEnrollment');
    
    Route::get('enrollments/{enrollment}/bill', 'EnrollmentController@quickBill'); // temporary
    Route::post('preinvoice', 'EnrollmentController@quickInvoice')->name('quickInvoice'); // temporary
/* 
    Route::get('cart/{id}', 'CartController@show'); // todo
    Route::delete('cart/{id}', 'CartController@destroy'); // todo

    Route::post('cart/{student}/checkout', 'PreInvoiceController@store'); // todo

    // add an enrollment to the cart for checkout
    Route::post('product', 'ProductController@store'); // todo

    // remove something from the cart
    Route::delete('product', 'ProductController@destroy'); // todo

    // Preinvoices management
    Route::patch('invoice/{preInvoice}', 'PreInvoiceController@update'); // todo */
});


// calendars routes
Route::group(
    ['middleware' => ['web', 'permission:calendars.view', 'language']],
    function () {
        Route::get('/calendar/room/{room}', 'RoomController@show')->name('roomCalendar');
        Route::get('/calendar/room', 'RoomController@index')->name('roomsCalendar');

        Route::get('/calendar/teacher/{teacher}', 'TeacherController@show')->name('teacherCalendar');
        Route::get('/calendar/teacher', 'TeacherController@index')->name('teachersCalendar');
    }
);

// HR routes
Route::group(
    ['middleware' => ['web', 'permission:hr.view', 'language']],
    function () {
        Route::get('/hr', 'HRController@index')->name('hrDashboard');
    }
);


// Reports routes
Route::group(
    ['middleware' => ['web', 'permission:reports.view', 'language']],
    function () {
        Route::get('/report', 'ReportController@index')->name('homeReport');
        Route::get('/report/courses', 'ReportController@courses')->name('courseReport');
        Route::get('/report/rhythms', 'ReportController@rhythms')->name('rhythmReport');
    }
);