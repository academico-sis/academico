<?php

Route::permanentRedirect('/auth/login', '/');
Route::permanentRedirect('/dashboard', '/');

Route::get('register', 'Auth\RegisterController@showRegistrationForm')->name('backpack.auth.register');
Route::post('register', 'Auth\RegisterController@register');
Route::get('searchstudents', 'StudentController@search');

/* DASHBOARD ROUTES */
Route::group(
    ['middleware' => ['web', 'language']],
    function () {
        Route::get('/home', 'HomeController@index')->name('home')->middleware('forceupdate');
        Route::get('/', 'HomeController@index')->middleware('forceupdate');
        Route::get('/admin', 'HomeController@admin')->name('admin');
        Route::get('dashboard/teacher', 'HomeController@teacher')->name('teacherDashboard');
        Route::get('dashboard/teacher/{teacher}/hours', 'HRController@teacher')->name('teacherHours'); // todo protect

        Route::get('dashboard/student', 'HomeController@student')->name('studentDashboard')->middleware('forceupdate');

        Route::get('setup', 'SetupController@index')->name('setupHome');
        Route::post('/leads/reset-converted', 'LeadStatusController@reset_all_converted_leads')->name('resetAllConvertedLeads');
    });

/* ATTENDANCE-RELATED ROUTES */
Route::group(
    ['middleware' => ['web', 'language']],
    function () {
        // Attendance routes
        Route::get('attendance', 'AttendanceController@index')->name('monitorAttendance');
        Route::get('attendance/student/{student}', 'AttendanceController@showStudentAttendanceForCourse')->name('studentAttendance');
        Route::get('attendance/course/{course}', 'AttendanceController@showCourse')->name('monitorCourseAttendance');
        Route::get('attendance/event/{event}', 'AttendanceController@showEvent')->name('eventAttendance');
        Route::post('attendance', 'AttendanceController@store')->name('storeAttendance');
        Route::post('attendance/event/{event}/toggle', 'AttendanceController@toggleEventAttendanceStatus')->name('toggleEventAttendance');
        Route::post('attendance/course/{course}/toggle', 'AttendanceController@toggleCourseAttendanceStatus')->name('toggleCourseAttendance');
    });

/* ENROLLMENTS-RELATED ROUTES */
Route::group(
    ['middleware' => ['web', 'language']],
    function () {
        Route::post('student/enroll', 'EnrollmentController@store')->name('storeEnrollment'); // create a new enrollment
        Route::post('enrollment/{enrollment}/changeCourse', 'EnrollmentController@update')->name('changeCourse');
        Route::get('enrollment/{enrollment}/bill', 'EnrollmentController@bill'); // display the cart to checkout the enrollment
        Route::post('checkout', 'PreInvoiceController@store'); // checkout the cart. Now only one enrollment at a time but maybe several in the future.
        Route::get('invoice/{preInvoice}/edit', 'PreInvoiceController@edit')->name('edit-invoice-number'); // update the invoice number
        Route::patch('invoice/{preInvoice}', 'PreInvoiceController@update')->name('store-invoice-number'); // update the invoice number
        Route::get('accountingservice/status', 'PreInvoiceController@accountingServiceStatus');
    });

/* STUDENTS-RELATED ROUTES */
Route::group(
    ['middleware' => ['web', 'language']],
    function () {
        Route::get('phonenumber/student/{student}', 'StudentPhoneNumberController@get');
        Route::post('phonenumber/student/{student}', 'StudentPhoneNumberController@store');
        Route::delete('phonenumber/{phoneNumber}', 'StudentPhoneNumberController@destroy');
    }
);

/* CONTACTS-RELATED ROUTES */
Route::group(
    ['middleware' => ['web', 'language']],
    function () {
        Route::post('user/addcontact', 'ContactController@store')->name('addContact'); // save an additional contact for a student
        Route::get('contact/{contact}/edit', 'ContactController@edit');
        Route::patch('contact/{contact}', 'ContactController@update')->name('updateContact');
        Route::get('phonenumber/contact/{contact}', 'ContactPhoneNumberController@get');
        Route::post('phonenumber/contact/{contact}', 'ContactPhoneNumberController@store');
    }
);

// EVALUATION RELATED ROUTES
Route::group(
    ['middleware' => ['web', 'language']],
    function () {

        // Skills Evaluation
        Route::get('course/{course}/skillsevaluation', 'CourseSkillEvaluationController@index')->name('courseSkillsEvaluation');
        Route::get('course/{course}/skillsevaluation/{student}', 'CourseSkillEvaluationController@edit')->name('studentSkillsEvaluation');
        Route::post('skillsevaluation', 'CourseSkillEvaluationController@store')->name('storeSkillEvaluation');
        Route::post('resultcomment', 'CommentController@storeresult')->name('storeResultComment'); // todo protect
    });

Route::group(
    ['middleware' => ['web', 'permission:evaluation.edit', 'language']],
    function () {
        Route::get('course/{course}/skill', 'CourseSkillController@index')->name('course-skills');
        Route::get('course/{course}/getskills', 'CourseSkillController@get');
        Route::patch('course/{course}/setskills', 'CourseSkillController@set');

        Route::get('course/{course}/skills/export', 'CourseSkillController@export')->name('course-skills-export');
        Route::post('course/{course}/skills/import', 'CourseSkillController@import')->name('course-skills-import');

        Route::get('course/{course}/syllabus', 'CourseSkillController@exportCourseSyllabus')->name('exportCourseSyllabus');

        // todo review this entire module
        /* Course grades update */
        Route::get('course/{course}/grades', 'GradeController@edit');
        Route::post('grades', 'GradeController@store');
        Route::delete('grades', 'GradeController@destroy');
        Route::post('course/gradetype', 'GradeController@add_grade_type_to_course');
        Route::delete('course/{course}/gradetype/{gradetype}', 'GradeController@remove_grade_type_from_course');
    });

Route::post('store-result', 'ResultController@store')->name('storeResult');

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

    Route::patch('calendar/teacher', 'EventController@update_course_teacher');
        Route::patch('calendar/room', 'EventController@update_course_room');
    });

// Comments routes
Route::group(
    ['middleware' => ['web', 'permission:comments.edit', 'language']],
    function () {
        Route::delete('comment/{comment}', 'CommentController@destroy');
    }
);

Route::post('comment', 'CommentController@store')->name('storeComment');

/* SETUP ROUTES */
Route::group(
    ['middleware' => ['web', 'permission:enrollments.edit', 'language']],
    function () {
        Route::get('config/default-periods', 'ConfigController@get')->name('get-default-periods-screen');
        Route::post('config/default-periods', 'ConfigController@update')->name('set-default-periods');
    });

// calendars routes
Route::group(
    ['middleware' => ['web', 'language']],
    function () {
        Route::get('/calendar/room/{room}', 'RoomController@show')->name('roomCalendar');
        Route::get('/calendar/room', 'RoomController@index')->name('roomsCalendar');
        Route::get('/leave/teachers', 'TeacherLeaveController@leaves')->name('teachersLeaves');

        Route::get('/calendar/teacher/{teacher}', 'TeacherCalendarController@show')->name('teacherCalendar');
        Route::get('/calendar/teacher', 'TeacherCalendarController@index')->name('teachersCalendar');
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

// New COURSES module
Route::group(
    ['middleware' => ['web', 'language']],
    function () {
        Route::get('courselist', 'CourseController@index')->name('get-courses-list');
        Route::get('courselist/search', 'CourseController@search')->name('search-courses');
    }
);

// Registration Routes...
Route::group(
    [
        'namespace'  => '\App\Http\Controllers',
        'middleware' => ['web', 'loggedin', 'language'],
        'prefix'     => config('backpack.base.route_prefix'),
    ],
    function () {
        Route::post('edit-account-info', 'Auth\MyAccountController@postAccountInfoForm');
        Route::post('edit-student-info', 'Auth\MyAccountController@postStudentInfoForm');
        Route::post('edit-profession', 'Auth\MyAccountController@postAccountProfessionForm');
        Route::post('edit-phone', 'Auth\MyAccountController@postPhoneForm');
        Route::post('edit-photo', 'Auth\MyAccountController@postPhotoForm');
        Route::post('edit-contacts', 'Auth\MyAccountController@postContactsForm');
    }
);

Route::group(
    [
        'namespace'  => '\App\Http\Controllers',
        'middleware' => ['web', 'loggedin', 'language', 'forceupdate'],
        'prefix'     => config('backpack.base.route_prefix'),
    ],
    function () {
        // route numbers match the DB forceupdate field
        Route::get('edit/1', 'Auth\MyAccountController@getAccountInfoForm')->name('backpack.account.edit_info');
        Route::get('edit/2', 'Auth\MyAccountController@getStudentInfoForm')->name('backpack.student.info');
        Route::get('edit/3', 'Auth\MyAccountController@getPhoneForm')->name('backpack.account.phone');
        Route::get('edit/4', 'Auth\MyAccountController@getAccountProfessionForm')->name('backpack.account.profession');
        Route::get('edit/5', 'Auth\MyAccountController@getPhotoForm')->name('backpack.account.photo');
        Route::get('edit/6', 'Auth\MyAccountController@getContactsForm')->name('backpack.account.contacts');
    }
);
