<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\AttendanceType;
use App\Models\Course;
use App\Models\Event;
use App\Models\Student;
use App\Traits\PeriodSelection;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;
use Prologue\Alerts\Facades\Alert;

class AttendanceController extends Controller
{
    use PeriodSelection;

    public function __construct()
    {
        parent::__construct();
        $this->middleware('permission:attendance.view', ['except' => ['showCourse', 'showEvent', 'showStudentAttendanceForCourse', 'store']]);
    }

    /**
     * Monitor attendance for all students.
     */
    public function index(Request $request)
    {
        Log::info('Attendance dashboard viewed by '.backpack_user()->id);
        $selected_period = $this->selectPeriod($request);

        // student attendance overview
        $absences = (new Attendance())->get_absence_count_per_student($selected_period);

        // get all courses for period and preload relations
        $courses = $selected_period->courses()->whereHas('events')->whereHas('enrollments')->with('attendance')->with('events')->get();

        // loop through all courses and get the number of events with incomplete attendance
        foreach ($courses as $course) {
            $eventsWithMissingAttendanceCount = 0;
            $coursesdata[$course->id]['name'] = $course->name;
            $coursesdata[$course->id]['id'] = $course->id;
            $coursesdata[$course->id]['exempt_attendance'] = $course->exempt_attendance;
            $coursesdata[$course->id]['teachername'] = $course->course_teacher_name;

            foreach ($course->eventsWithExpectedAttendance as $event) {
                foreach ($course->enrollments as $enrollment) {
                    // if a student has no attendance record for the class (event)
                    $hasNotAttended = $course->attendance->where('student_id', $enrollment->student_id)
                    ->where('event_id', $event->id)
                    ->isEmpty();

                    // count one and break loop
                    if ($hasNotAttended) {
                        $eventsWithMissingAttendanceCount++;
                        break;
                    }
                }
            }

            $coursesdata[$course->id]['missing'] = $eventsWithMissingAttendanceCount;
        }

        // sort by number of events with missing attendance
        $courses = collect($coursesdata ?? [])->sortByDesc('missing')->toArray();
        $isadmin = backpack_user()->hasPermissionTo('courses.edit');

        return view('attendance.monitor', compact('absences', 'courses', 'selected_period', 'isadmin'));
    }

    /**
     * Store a newly created attendance record.
     */
    public function store(Request $request)
    {
        $event = Event::findOrFail($request->input('event_id'));

        // If the user is not allowed to perform this action
        if (Gate::forUser(backpack_user())->denies('edit-attendance', $event)) {
            abort(403);
        }

        $student = Student::findOrFail($request->input('student_id'));
        $attendance_type = AttendanceType::findOrFail($request->input('attendance_type_id'));

        $attendance = Attendance::firstOrNew([
            'student_id' => $student->id,
            'event_id' => $event->id,
        ]);

        $attendance->attendance_type_id = $attendance_type->id;

        $attendance->save();

        Log::info('Attendance recorded by '.backpack_user()->id);

        return $attendance;
    }

    /**
     * Show the attendance records for a course.
     */
    public function showCourse(Course $course)
    {

    // The current is not allowed to view the page
        if (Gate::forUser(backpack_user())->denies('view-course-attendance', $course)) {
            abort(403);
        }

        // get past events for the course
        $events = $course->events->filter(function ($value, $key) {
            return Carbon::parse($value->start) < Carbon::now();
        })->sortByDesc('start');

        // if the course has any past events
        if (($events->count() == 0) || ($course->enrollments()->count() == 0)) {
            Alert::add('error', 'This course has no events.')->flash();

            return redirect()->back();
        }

        $enrollments = $course->enrollments()->with('student')->get();

        foreach ($enrollments as $enrollment) {
            foreach ($events as $event) {
                if ($event->exempt_attendance == 1) {
                    $attendances[$enrollment->student->id][]['attendance'] = '';
                } else {
                    $attendances[$enrollment->student->id]['student'] = $enrollment->student->firstname.' '.$enrollment->student->lastname;
                    $attendances[$enrollment->student->id][]['attendance'] = $event->attendance->where('student_id', $enrollment->student->id)->first();
                }
            }
        }
        Log::info('Attendance for course viewed by '.backpack_user()->id);

        $isadmin = backpack_user()->hasPermissionTo('courses.edit');

        return view('attendance/course', compact('attendances', 'isadmin', 'course', 'events'));
    }

    public function showEvent(Event $event)
    {

    // The current is not allowed to view the page
        if (Gate::forUser(backpack_user())->denies('view-event-attendance', $event)) {
            abort(403);
        }

        // get students
        $enrollments = $event->enrollments()->with('student')->get();

        // get the attendance record for the event
        $attendance = $event->attendance;

        $attendances = [];

        $attendance_types = AttendanceType::all();

        // build a collection : for each student, display attendance

        foreach ($enrollments as $enrollment) {
            $attendances[$enrollment->student->id]['student'] = $enrollment->student->name;
            $attendances[$enrollment->student->id]['student_id'] = $enrollment->student->id;
            $attendances[$enrollment->student->id]['attendance'] = $attendance->where('student_id', $enrollment->student->id)->first() ?? '[attendance][attendance_type_id]';
        }
        Log::info('Attendance for event viewed by '.backpack_user()->id);

        return view('attendance/event', compact('attendances', 'event', 'attendance_types'));
    }

    public function showStudentAttendanceForCourse(Student $student, Request $request)
    {
        if ($request->query('course_id', null) == null) {
            $selectedCourse = $student->enrollments->last()->course;
        } else {
            $selectedCourse = Course::find($request->query('course_id', null));
        }

        // If the current is not allowed to view the page
        if (Gate::forUser(backpack_user())->denies('view-course-attendance', $selectedCourse)) {
            abort(403);
        }

        $studentEnrollments = $student->enrollments()->with('course')->get();
        $courseEventIds = $selectedCourse->events->pluck('id');

        $attendances = $student->attendance()->with('event')->get()->whereIn('event_id', $courseEventIds);
        $enrollment = $studentEnrollments->where('course_id', $selectedCourse->id)->first();
        $attendanceratio = $enrollment->attendance_ratio;

        return view('attendance.student', compact('student', 'selectedCourse', 'studentEnrollments', 'attendances', 'attendanceratio'));
    }

    public function toggleEventAttendanceStatus(Event $event, Request $request)
    {
        if (! backpack_user()->hasPermissionTo('courses.edit')) {
            abort(403);
        }
        $event->exempt_attendance = (int) $request->status;
        $event->save();

        return (int) $event->exempt_attendance;
    }

    public function toggleCourseAttendanceStatus(Course $course, Request $request)
    {
        if (! backpack_user()->hasPermissionTo('courses.edit')) {
            abort(403);
        }
        $course->exempt_attendance = (int) $request->status;
        $course->save();

        // apply the same change to all course events
        foreach ($course->events as $event) {
            $event->exempt_attendance = (int) $request->status;
            $event->save();
        }

        return (int) $course->exempt_attendance;
    }
}
