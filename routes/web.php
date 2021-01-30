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
        Route::get('dashboard/teacher/{teacher}/hours', 'HRController@teacher')->name('teacherHours');

        Route::get('dashboard/student', 'HomeController@student')->name('studentDashboard')->middleware('forceupdate');
    }
);

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
    }
);

/* ENROLLMENTS-RELATED ROUTES */
Route::group(
    ['middleware' => ['web', 'language']],
    function () {
        Route::post('student/enroll', 'EnrollmentController@store')->name('storeEnrollment'); // create a new enrollment
        Route::post('enrollment/{enrollment}/changeCourse', 'EnrollmentController@update')->name('changeCourse');
        Route::get('enrollment/{enrollment}/bill', 'EnrollmentController@bill'); // display the cart to checkout the enrollment
        Route::post('checkout', 'PaymentController@store');
        Route::post('enrollment/{enrollment}/price', 'EnrollmentController@savePrice');

        Route::post('enrollment/{enrollment}/markaspaid', 'EnrollmentController@markaspaid'); // display the cart to checkout the enrollment
    }
);

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
        Route::delete('contact/{contact}/delete', 'ContactController@destroy')->name('deleteContact');
        Route::patch('contact/{contact}', 'ContactController@update')->name('updateContact');
        Route::get('phonenumber/contact/{contact}', 'ContactPhoneNumberController@get');
        Route::post('phonenumber/contact/{contact}', 'ContactPhoneNumberController@store');
    }
);

// EVALUATION RELATED ROUTES
Route::group(
    ['middleware' => ['web', 'language']],
    function () {

        /* Course grades update */
        Route::get('course/{course}/grades', 'GradeController@edit')->name('editCourseGrades');
        Route::post('grades', 'GradeController@store');
        Route::post('grades/enrollment-total', 'GradeController@getEnrollmentTotal');

        // Skills Evaluation
        Route::get('course/{course}/skillsevaluation', 'CourseSkillEvaluationController@index')->name('courseSkillsEvaluation');
        Route::get('course/{course}/skillsevaluation/{student}', 'CourseSkillEvaluationController@edit')->name('studentSkillsEvaluation');
        Route::post('skillsevaluation', 'CourseSkillEvaluationController@store')->name('storeSkillEvaluation');

        Route::get('course/{course}/syllabus', 'CourseSkillController@exportCourseSyllabus')->name('exportCourseSyllabus');
    }
);

Route::post('store-result', 'ResultController@store')->name('storeResult');

// COURSE EDITION ROUTES
Route::group(
    ['middleware' => ['web', 'permission:courses.edit', 'language']],
    function () {
        // Course Events routes
        Route::get('course/{course}/events/get', 'EventController@getCourseEvents')->name('getCourseEvents'); // todo use route name
        Route::patch('calendar/teacher', 'EventController@update_course_teacher');
        Route::patch('calendar/room', 'EventController@update_course_room');
    }
);

// Comments routes
Route::group(
    ['middleware' => ['web', 'permission:comments.edit', 'language']],
    function () {
        Route::put('edit-comment/{comment}', 'CommentController@update');
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
    }
);

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
        Route::get('/report/internal', 'ReportController@internal')->name('homeReport');
        Route::get('/report/courses', 'ReportController@courses')->name('courseReport');
        Route::get('/report/rhythms', 'ReportController@rhythms')->name('rhythmReport');
        Route::get('/report/levels', 'ReportController@levels')->name('levelReport');
    }
);

Route::post('leadstatus', 'LeadStatusController@update')->name('postLeadStatus');

// New COURSES module
Route::group(
    ['middleware' => ['web', 'language']],
    function () {
        Route::get('courselist', 'CourseController@index')->name('get-courses-list');
        Route::get('courselist/search', 'CourseController@search')->name('search-courses');
        Route::get('course-user-view', 'CourseController@redirectToUserPreferredView')->name('course-view-find');
        Route::get('course-view-switch', 'CourseController@switchViews')->name('course-view-switch');
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
        Route::post('edit-account', 'Auth\MyAccountController@postAccountInfoForm')->name('backpack.account.info.store');
        Route::post('edit-student-info', 'Auth\MyAccountController@postStudentInfoForm');
        Route::post('edit-profession', 'Auth\MyAccountController@postAccountProfessionForm');
        Route::post('edit-phone', 'Auth\MyAccountController@postPhoneForm');
        Route::post('edit-photo', 'Auth\MyAccountController@postPhotoForm');
        Route::post('edit-contacts', 'Auth\MyAccountController@postContactsForm');
        Route::post('change-password', 'Auth\MyAccountController@postChangePasswordForm')->name('backpack.account.password');
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
        Route::permanentRedirect('/edit-account-info', '/edit/1')->name('backpack.account.info');
        Route::get('edit/1', 'Auth\MyAccountController@getAccountInfoForm')->name('backpack.account.edit_info');
        Route::get('edit/2', 'Auth\MyAccountController@getChangePasswordForm')->name('backpack.account.change_password');
        Route::get('edit/3', 'Auth\MyAccountController@getStudentInfoForm')->name('backpack.student.info');
        Route::get('edit/4', 'Auth\MyAccountController@getPhoneForm')->name('backpack.account.phone');
        Route::get('edit/5', 'Auth\MyAccountController@getAccountProfessionForm')->name('backpack.account.profession');
        Route::get('edit/6', 'Auth\MyAccountController@getPhotoForm')->name('backpack.account.photo');
        Route::get('edit/7', 'Auth\MyAccountController@getContactsForm')->name('backpack.account.contacts');
    }
);

Route::group([
    'middleware' => ['web', 'role:admin', 'language'],
], function () {
    Route::post('teacher/{id}/restore', 'TeacherController@restore');
    Route::post('teacher/{teacher}/delete', 'TeacherController@destroy');

    Route::post('level/{id}/restore', 'LevelController@restore');
    Route::post('level/{level}/delete', 'LevelController@destroy');

    Route::post('rhythm/{id}/restore', 'RhythmController@restore');
    Route::post('rhythm/{rhythm}/delete', 'RhythmController@destroy');

    Route::get('/student/create', 'StudentController@create')->name('student.create');
    Route::get('/student/{student}/edit', 'StudentController@edit')->name('student.edit');
    Route::put('/student/{student}', 'StudentController@update')->name('student.update');
    Route::post('/student', 'StudentController@store')->name('student.store');
});
