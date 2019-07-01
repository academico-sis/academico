<?php

Route::permanentRedirect('/auth/login', '/');

// save an additional contact for a student
Route::post('user/addcontact', 'ContactController@store')->name('addContact');



/* All routes should be protected by Backpack */
Route::group(
    ['middleware' => ['web', 'language']],
    function () {

    Route::get('searchstudents', 'Admin\StudentCrudController@dataAjax');
        
    // dashboard and home routes
    Route::get('/home', 'HomeController@index')->name('home')->middleware('forceupdate');
    Route::get('/', 'HomeController@index')->name('home')->middleware('forceupdate');
    Route::get('/admin', 'HomeController@admin')->name('admin');
    Route::get('dashboard/teacher', 'HomeController@teacher')->name('teacherDashboard'); // todo protect
    Route::get('dashboard/teacher/{teacher}/hours', 'HRController@teacher')->name('teacherHours'); // todo protect

    Route::get('dashboard/student', 'HomeController@student')->name('studentDashboard')->middleware('forceupdate'); // todo protect

    Route::get('student/{student}/phonenumbers', 'PhoneNumberController@get');
    Route::get('contact/{contact}/phonenumbers', 'ContactController@getPhoneNumber');

    Route::get('contact/{contact}/edit', 'ContactController@edit');
    Route::patch('contact/{contact}', 'ContactController@update')->name('updateContact');

    Route::delete('phonenumber/{phoneNumber}', 'PhoneNumberController@destroy');
    Route::post('phonenumber', 'PhoneNumberController@store');
    Route::post('contactphonenumber', 'ContactController@storePhoneNumber');

    // moodle token login
    Route::get('moodlelogin', 'MoodleController@moodlelogin')->name('moodleLogin');
    //Route::resource('result', 'ResultController');

    Route::get('apitoken', 'ApiTokenController@index');
    Route::post('apitoken', 'ApiTokenController@store');

    // creates a new enrollment
    Route::post('student/enroll', 'EnrollmentController@store')->name('storeEnrollment');

    // Attendance routes
    Route::get('attendance', 'AttendanceController@index')->name('monitorAttendance');
    Route::get('attendance/student/{student}', 'AttendanceController@student')->name('studentAttendance');
    Route::get('attendance/course/{course}', 'AttendanceController@showCourse')->name('monitorCourseAttendance');
    Route::get('attendance/event/{event}', 'AttendanceController@showEvent')->name('eventAttendance');
    Route::post('attendance', 'AttendanceController@store')->name('storeAttendance');

    Route::get('attendance/event/{event}/exempt', 'AttendanceController@exemptEvent')->name('exemptEventAttendance');
    Route::get('attendance/course/{course}/exempt', 'AttendanceController@exemptCourse')->name('exemptCourseAttendance');


    // Skills Evaluation
    Route::get('course/{course}/skillsevaluation', 'CourseSkillEvaluationController@index')->name('courseSkillsEvaluation');
    Route::get('course/{course}/skillsevaluation/{student}', 'CourseSkillEvaluationController@edit')->name('studentSkillsEvaluation');
    Route::post('skillsevaluation', 'CourseSkillEvaluationController@store')->name('storeSkillEvaluation');
    Route::post('resultcomment', 'CommentController@storeresult')->name('storeResultComment'); // todo protect

    Route::get('setup', 'SetupController@index')->name('setupHome');
    Route::post('/leads/reset-converted', 'LeadStatusController@reset_all_converted_leads')->name('resetAllConvertedLeads');
});



// EVALUATION RELATED ROUTES
Route::group(
    ['middleware' => ['web', 'permission:evaluation.edit', 'language']],
    function () {

   
    Route::get('course/{course}/skill', 'CourseSkillController@index')->name('course-skills');
    Route::get('course/{course}/getskills', 'CourseSkillController@get');
    Route::patch('course/{course}/setskills', 'CourseSkillController@set');

    Route::get('course/{course}/skills/export', 'CourseSkillController@export')->name('course-skills-export');
    Route::post('course/{course}/skills/import', 'CourseSkillController@import')->name('course-skills-import');;

    Route::get('course/{course}/syllabus', 'CourseSkillController@exportCourseSyllabus')->name('exportCourseSyllabus');

    // todo review this entire module
    /* Course grades update */
    Route::get('course/{course}/grades', 'GradeController@edit');
    Route::post('grades', 'GradeController@store');
    Route::delete('grades', 'GradeController@destroy');
    Route::post('course/gradetype', 'GradeController@add_grade_type_to_course');
    Route::delete('course/{course}/gradetype/{gradetype}', 'GradeController@remove_grade_type_from_course');

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
        Route::delete('comment/{comment}', 'CommentController@destroy');
    }
);

// Enrollments, Billing and Invoicing routes
Route::group(
    ['middleware' => ['web', 'permission:enrollments.create', 'language']],
    function () {
    
    Route::get('enrollments/{enrollment}/bill', 'EnrollmentController@bill'); // temporary
    Route::post('preinvoice', 'EnrollmentController@quickInvoice')->name('quickInvoice'); // temporary

    Route::post('checkout', 'PreInvoiceController@store');
 
/*     Route::get('cart/{id}', 'CartController@show'); // todo
 */    Route::delete('cart', 'CartController@destroy');
/* 

    // add an enrollment to the cart for checkout
    Route::post('product', 'ProductController@store'); // todo

    // remove something from the cart
    Route::delete('product', 'ProductController@destroy'); // todo

    // Preinvoices management
    Route::patch('invoice/{preInvoice}', 'PreInvoiceController@update'); // todo */
});


// calendars routes
Route::group(
    ['middleware' => ['web', 'language']],
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
        Route::get('/report', 'ReportController@index')->name('allReports');

        Route::get('/report/internal', 'ReportController@internal')->name('homeReport');
        Route::get('/report/external', 'ReportController@external')->name('externalReport');

        Route::get('/report/courses', 'ReportController@courses')->name('courseReport');
        Route::get('/report/rhythms', 'ReportController@rhythms')->name('rhythmReport');
    }
);

Route::post('leadstatus', 'LeadStatusController@update')->name('postLeadStatus');

