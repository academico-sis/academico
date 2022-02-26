<?php

use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\Auth;
use App\Http\Controllers\Auth\MyAccountController;
use App\Http\Controllers\BookController;
use App\Http\Controllers\Calendar\RoomCalendarController;
use App\Http\Controllers\Calendar\TeacherCalendarController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\ConfigController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\ContactPhoneNumberController;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\CourseSkillController;
use App\Http\Controllers\CourseSkillEvaluationController;
use App\Http\Controllers\EnrollmentController;
use App\Http\Controllers\EnrollmentScholarshipController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\GradeController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\HRController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\LeadStatusController;
use App\Http\Controllers\LevelController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\ResultController;
use App\Http\Controllers\RhythmController;
use App\Http\Controllers\ScheduledPaymentController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\StudentPhoneNumberController;
use App\Http\Controllers\TeacherController;
use App\Http\Controllers\TeacherLeaveController;
use Illuminate\Support\Facades\Route;

Route::permanentRedirect('/auth/login', '/');
Route::permanentRedirect('/dashboard', '/');

Route::get('register', [Auth\RegisterController::class, 'showRegistrationForm'])->name('backpack.auth.register');
Route::post('register', [Auth\RegisterController::class, 'register']);
Route::get('searchstudents', [StudentController::class, 'search']);

/* DASHBOARD ROUTES */
Route::middleware('web', 'language')->group(function () {
    Route::get('/home', [HomeController::class, 'index'])->name('home')->middleware('forceupdate');
    Route::get('/', [HomeController::class, 'index'])->middleware('forceupdate');
    Route::get('/admin', [HomeController::class, 'admin'])->name('admin');
    Route::get('dashboard/teacher', [HomeController::class, 'teacher'])->name('teacherDashboard');

    Route::get('dashboard/student', [HomeController::class, 'student'])->name('studentDashboard')->middleware('forceupdate');
}
);

/* ATTENDANCE-RELATED ROUTES */
Route::middleware('web', 'language')->group(function () {
    // Attendance routes
    Route::get('attendance', [AttendanceController::class, 'index'])->name('monitorAttendance');
    Route::get('attendance/student/{student}', [AttendanceController::class, 'showStudentAttendanceForCourse'])->name('studentAttendance');
    Route::get('attendance/course/{course}', [AttendanceController::class, 'showCourse'])->name('monitorCourseAttendance');
    Route::get('attendance/event/{event}', [AttendanceController::class, 'showEvent'])->name('eventAttendance');
    Route::post('attendance', [AttendanceController::class, 'store'])->name('storeAttendance');
    Route::post('attendance/event/{event}/toggle', [AttendanceController::class, 'toggleEventAttendanceStatus'])->name('toggleEventAttendance');
    Route::post('attendance/course/{course}/toggle', [AttendanceController::class, 'toggleCourseAttendanceStatus'])->name('toggleCourseAttendance');

    Route::get('course/{course}/show', [CourseController::class, 'show']);
}
);

/* ENROLLMENTS-RELATED ROUTES */
Route::middleware('web', 'language')->group(function () {
    Route::post('student/enroll', [EnrollmentController::class, 'store'])->name('storeEnrollment'); // create a new enrollment
    Route::post('enrollment/{enrollment}/changeCourse', [EnrollmentController::class, 'update'])->name('changeCourse');
    Route::get('enrollment/{enrollment}/bill', [EnrollmentController::class, 'bill']);
    Route::get('scheduledpayment/{scheduledPayment}/bill', [ScheduledPaymentController::class, 'bill'])->name('checkout-scheduled-payment');

    Route::get('enrollment/{enrollment}/export', [EnrollmentController::class, 'exportToWord'])->name('generate-enrollment-sheet');

    Route::get('enrollment/{enrollment}/export-result', [ResultController::class, 'exportResult'])->name('enrollment-export-result');
    Route::get('enrollment/{enrollment}/export-certificate', [ResultController::class, 'exportCertificate'])->name('enrollment-export-certificate');

    Route::post('checkout', [InvoiceController::class, 'store']);
    Route::post('enrollment/{enrollment}/price', [EnrollmentController::class, 'savePrice']);

    Route::get('invoice/{invoice}/pdf', [InvoiceController::class, 'download'])->name('export-invoice');

    Route::post('invoice/{invoice}/payments', [InvoiceController::class, 'savePayments'])->name('invoice-save-payments');

    Route::put('enrollment/{enrollment}/price', [EnrollmentController::class, 'savePrice']);
    Route::post('enrollment/{enrollment}/markaspaid', [EnrollmentController::class, 'markaspaid']);
    Route::post('enrollment/{enrollment}/markasunpaid', [EnrollmentController::class, 'markasunpaid']);
    Route::get('enrollment/{enrollment}/scheduled-payments', [ScheduledPaymentController::class, 'create'])->name('enrollment-scheduled-payments');
    Route::post('enrollment/{enrollment}/scheduled-payments', [ScheduledPaymentController::class, 'store'])->name('enrollment-save-scheduled-payments');
    Route::get('accountingservice/status', [InvoiceController::class, 'accountingServiceStatus']);
    Route::post('enrollment/{enrollment}/scholarships/add', [EnrollmentScholarshipController::class, 'store'])->name('add-scholarship'); // update the invoice number
        Route::post('enrollment/{enrollment}/scholarships/remove', [EnrollmentScholarshipController::class, 'destroy'])->name('remove-scholarship'); // update the invoice number

        Route::post('getEnrollmentBalance', [EnrollmentController::class, 'getBalance'])->middleware('role:admin');
}
);

/* STUDENTS-RELATED ROUTES */
Route::middleware('web', 'language')->group(function () {
    Route::get('phonenumber/student/{student}', [StudentPhoneNumberController::class, 'get']);
    Route::post('phonenumber/student/{student}', [StudentPhoneNumberController::class, 'store']);
    Route::delete('phonenumber/{phoneNumber}', [StudentPhoneNumberController::class, 'destroy']);

    Route::post('user/addbook', [BookController::class, 'store'])->name('addBook');
    Route::get('bookstudent', [BookController::class, 'exportCode']);
    Route::put('bookstudent', [BookController::class, 'update']);
    Route::delete('bookstudent', [BookController::class, 'destroy']);
}
);

/* CONTACTS-RELATED ROUTES */
Route::middleware('web', 'language')->group(function () {
    Route::post('user/addcontact', [ContactController::class, 'store'])->name('addContact'); // save an additional contact for a student
    Route::get('contact/{contact}/edit', [ContactController::class, 'edit']);
    Route::delete('contact/{contact}/delete', [ContactController::class, 'destroy'])->name('deleteContact');
    Route::patch('contact/{contact}', [ContactController::class, 'update'])->name('updateContact');
    Route::get('phonenumber/contact/{contact}', [ContactPhoneNumberController::class, 'get']);
    Route::post('phonenumber/contact/{contact}', [ContactPhoneNumberController::class, 'store']);
}
);

// EVALUATION RELATED ROUTES
Route::middleware('web', 'language')->group(function () {

        /* Course grades update */
    Route::get('course/{course}/grades', [GradeController::class, 'edit'])->name('editCourseGrades');
    Route::post('grades', [GradeController::class, 'store']);
    Route::post('grades/enrollment-total', [GradeController::class, 'getEnrollmentTotal']);

    // Skills Evaluation
    Route::get('course/{course}/skillsevaluation', [CourseSkillEvaluationController::class, 'index'])->name('courseSkillsEvaluation');
    Route::get('enrollment/{enrollment}/skillsevaluation', [CourseSkillEvaluationController::class, 'edit'])->name('studentSkillsEvaluation');
    Route::post('skillsevaluation', [CourseSkillEvaluationController::class, 'store'])->name('storeSkillEvaluation');

    Route::get('course/{course}/syllabus', [CourseSkillController::class, 'exportCourseSyllabus'])->name('exportCourseSyllabus');
}
);

Route::post('store-result', [ResultController::class, 'store'])->name('storeResult');
Route::get('enrollment/{enrollment}/export-result', [ResultController::class, 'exportResult'])->name('enrollment-export-result');
Route::get('enrollment/{enrollment}/export-certificate', [ResultController::class, 'exportCertificate'])->name('enrollment-export-certificate');
Route::get('course/{course}/export-course-results', [ResultController::class, 'exportCourseResults'])->name('course-export-results');

// COURSE EDITION ROUTES
Route::middleware('web', 'permission:courses.edit', 'language')->group(function () {
    // Course Events routes
        Route::get('course/{course}/events/get', [EventController::class, 'getCourseEvents'])->name('getCourseEvents'); // todo use route name
        Route::patch('calendar/teacher', [EventController::class, 'update_course_teacher']);
    Route::patch('calendar/room', [EventController::class, 'update_course_room']);
}
);

// Comments routes
Route::middleware('web', 'permission:comments.edit', 'language')->group(function () {
    Route::put('edit-comment/{comment}', [CommentController::class, 'update']);
    Route::delete('comment/{comment}', [CommentController::class, 'destroy']);
}
);

Route::post('comment', [CommentController::class, 'store'])->name('storeComment');

/* SETUP ROUTES */
Route::middleware('web', 'permission:enrollments.edit', 'language')->group(function () {
    Route::get('config/default-periods', [ConfigController::class, 'get'])->name('get-default-periods-screen');
    Route::post('config/default-periods', [ConfigController::class, 'update'])->name('set-default-periods');
}
);

// calendars routes
Route::middleware('web', 'language')->group(function () {
    Route::get('/calendar/room/{room}', [RoomCalendarController::class, 'show'])->name('roomCalendar');
    Route::get('/calendar/room', [RoomCalendarController::class, 'index'])->name('roomsCalendar');
    Route::get('/leave/teachers', [TeacherLeaveController::class, 'leaves'])->name('teachersLeaves');

    Route::get('/calendar/teacher/{teacher}', [TeacherCalendarController::class, 'show'])->name('teacherCalendar');
    Route::get('/calendar/teacher', [TeacherCalendarController::class, 'index'])->name('teachersCalendar');
}
);

// HR routes
Route::middleware('web', 'permission:hr.view', 'language')->group(function () {
    Route::get('/hr', [HRController::class, 'index'])->name('hrDashboard');
}
);

// Reports routes
Route::middleware('web', 'permission:reports.view', 'language')->group(function () {
    Route::get('/report', [ReportController::class, 'index'])->name('allReports');

    Route::get('/report/internal', [ReportController::class, 'internal'])->name('homeReport');
    Route::get('/report/gender', [ReportController::class, 'genderReport'])->name('genderReport');
    Route::get('/report/external', [ReportController::class, 'external'])->name('externalReport');
    Route::get('/report/external2', [ReportController::class, 'external2'])->name('externalReport2');
    Route::get('/report/external3', [ReportController::class, 'external3'])->name('externalReport3');
    Route::get('/report/partner/{partner}', [ReportController::class, 'partner'])->name('partnerReport');

    Route::get('/report/courses', [ReportController::class, 'courses'])->name('courseReport');
    Route::get('/report/rhythms', [ReportController::class, 'rhythms'])->name('rhythmReport');
    Route::get('/report/levels', [ReportController::class, 'levels'])->name('levelReport');
}
);

Route::post('leadstatus', [LeadStatusController::class, 'update'])->name('postLeadStatus');

// New COURSES module
Route::middleware('web', 'language')->group(function () {
    Route::get('courselist', [CourseController::class, 'index'])->name('get-courses-list');
    Route::get('courselist/search', [CourseController::class, 'search'])->name('search-courses');
    Route::get('course-user-view', [CourseController::class, 'redirectToUserPreferredView'])->name('course-view-find');
    Route::get('course-view-switch', [CourseController::class, 'switchViews'])->name('course-view-switch');
}
);

// Registration Routes...
Route::namespace('\App\Http\Controllers')->middleware('web', 'loggedin', 'language')->prefix(config('backpack.base.route_prefix'))->group(function () {
    Route::post('edit-account', [MyAccountController::class, 'postAccountInfoForm'])->name('backpack.account.info.store');
    Route::post('edit-student-info', [MyAccountController::class, 'postStudentInfoForm']);
    Route::post('edit-profession', [MyAccountController::class, 'postAccountProfessionForm']);
    Route::post('edit-phone', [MyAccountController::class, 'postPhoneForm']);
    Route::post('edit-photo', [MyAccountController::class, 'postPhotoForm']);
    Route::post('edit-contacts', [MyAccountController::class, 'postContactsForm']);
    Route::post('change-password', [MyAccountController::class, 'postChangePasswordForm'])->name('backpack.account.password');
}
);

Route::namespace('\App\Http\Controllers')->middleware('web', 'loggedin', 'language', 'forceupdate')->prefix(config('backpack.base.route_prefix'))->group(function () {
    // route numbers match the DB forceupdate field
    Route::permanentRedirect('/edit-account-info', '/edit/1')->name('backpack.account.info');
    Route::get('edit/1', [MyAccountController::class, 'getAccountInfoForm'])->name('backpack.account.edit_info');
    Route::get('edit/2', [MyAccountController::class, 'getChangePasswordForm'])->name('backpack.account.change_password');
    Route::get('edit/3', [MyAccountController::class, 'getStudentInfoForm'])->name('backpack.student.info');
    Route::get('edit/4', [MyAccountController::class, 'getPhoneForm'])->name('backpack.account.phone');
    Route::get('edit/5', [MyAccountController::class, 'getAccountProfessionForm'])->name('backpack.account.profession');
    Route::get('edit/6', [MyAccountController::class, 'getPhotoForm'])->name('backpack.account.photo');
    Route::get('edit/7', [MyAccountController::class, 'getContactsForm'])->name('backpack.account.contacts');
}
);

Route::middleware('web', 'role:admin', 'language')->group(function () {
    Route::post('teacher/{id}/restore', [TeacherController::class, 'restore']);
    Route::post('teacher/{teacher}/delete', [TeacherController::class, 'destroy']);

    Route::post('level/{id}/restore', [LevelController::class, 'restore']);
    Route::post('level/{level}/delete', [LevelController::class, 'destroy']);

    Route::post('rhythm/{id}/restore', [RhythmController::class, 'restore']);
    Route::post('rhythm/{rhythm}/delete', [RhythmController::class, 'destroy']);

    Route::get('createinvoice', [InvoiceController::class, 'create'])->name('invoice.create');
});
