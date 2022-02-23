<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\LeadType;
use App\Models\Period;
use App\Models\Student;
use App\Models\Teacher;
use App\Services\StatService;
use App\Traits\PeriodSelection;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class HomeController extends Controller
{
    use PeriodSelection;

    public function __construct()
    {
        parent::__construct();

        $this->middleware(backpack_middleware());
    }

    /**
     * redirect the user according to their role.
     */
    public function index()
    {
        if (backpack_user()->hasRole(['admin', 'secretary'])) {
            return redirect()->route('admin');
        } elseif (backpack_user()->isTeacher()) {
            return redirect()->route('teacherDashboard');
        } elseif (backpack_user()->isStudent()) {
            return redirect()->route('studentDashboard');
        } else {
            // this should never happen
            Log::warning(backpack_user()->id.' accessed the generic dashboard (no role identified)');

            abort(403, 'User with no role');
        }
    }

    public function teacher(Request $request)
    {
        if (! backpack_user()->isTeacher()) {
            abort(403);
        }

        $period = $this->selectPeriod($request);

        $teacher = Teacher::where('id', backpack_user()->id)->first();
        Log::info($teacher->name.' accessed the student dashboard');

        $remoteVolume = $teacher->courses()->whereNull('parent_course_id')->where('period_id', $period->id)->sum('remote_volume');
        $presentialVolume = $teacher->courses()->whereNull('parent_course_id')->where('period_id', $period->id)->sum('volume');

        return view('teacher.dashboard', [
            'teacher' => $teacher,
            'courses' => $teacher->period_courses($period),
            'pending_attendance' => $teacher->events_with_pending_attendance($period),
            'selected_period' => $period,
            'remoteVolume' => $remoteVolume,
            'volume' => $presentialVolume,
            'totalVolume' => $remoteVolume + $presentialVolume,
        ]);
    }

    public function student()
    {
        if (! backpack_user()->isStudent()) {
            abort(403);
        }

        $student = Student::where('id', backpack_user()->id)->first();
        Log::info($student->name.' accessed the student dashboard');

        return view('student.dashboard', [
            'student' => $student,
            'enrollments' => $student->enrollments()->real(),
        ]);
    }

    public function admin()
    {
        $currentPeriod = Period::get_default_period();
        $enrollmentsPeriod = Period::get_enrollments_period();

        if (! backpack_user()->hasRole(['admin', 'secretary'])) {
            abort(403);
        }

        Log::info(backpack_user()->firstname.' '.backpack_user()->lastname.' accessed the admin dashboard');

        // todo optimize this !!
        $events = Event::where('start', '>', Carbon::now()->subDays(15))->where('end', '<', Carbon::now()->addDays(15))->orderBy('id', 'desc')
            ->get()
            ->map(fn ($event) => [
                'title' => $event['name'],
                'resourceId' => $event['teacher_id'],
                'start' => $event['start'],
                'end' => $event['end'],
                'backgroundColor' => $event['color'],
                'borderColor' => $event['color'],
            ])
            ->toArray();

        $teachers = Teacher::with('user')->get()->toArray();

        $teachers = array_map(fn ($teacher) => [
            'id' => $teacher['id'],
            'title' => $teacher['user']['firstname'],
        ], $teachers);

        $stats = new StatService(external: false, partner: null, reference: $currentPeriod);

        return view('admin.dashboard', [
            'pending_enrollment_count' => $stats->pendingEnrollmentsCount(),
            'paid_enrollment_count' => $stats->paidEnrollmentsCount(),
            'students_count' => $stats->studentsCount(),
            'currentPeriod' => $currentPeriod,
            'enrollmentsPeriod' => $enrollmentsPeriod,
            'total_enrollment_count' => $stats->enrollmentsCount(),
            'resources' => $teachers,
            'events' => $events,
            'pending_attendance' => $currentPeriod->courses_with_pending_attendance,
            // todo: optimize
            'unassigned_events' => Event::unassigned()->count(),
            'pending_leads' => LeadType::find(4)->students()->count(),
        ]);
    }
}
