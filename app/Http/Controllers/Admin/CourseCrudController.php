<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Admin\Operations\ShowStudentListOperation;
use App\Http\Controllers\Admin\Operations\ShowStudentPhotoRosterOperation;
use App\Http\Requests\CourseRequest;
use App\Models\Book;
use App\Models\Course;
use App\Models\EvaluationType;
use App\Models\Level;
use App\Models\Period;
use App\Models\Rhythm;
use App\Models\Room;
use App\Models\Teacher;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Http\Controllers\Operations\CreateOperation;
use Backpack\CRUD\app\Http\Controllers\Operations\DeleteOperation;
use Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
use Backpack\CRUD\app\Http\Controllers\Operations\UpdateOperation;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;
use App\Traits\CurrencyFormatTrait;
use App\Http\Requests\CourseCreateRequest;

class CourseCrudController extends CrudController
{
    use ListOperation;
    use CreateOperation { store as traitStore; }
    use UpdateOperation;
    use DeleteOperation;
    use ShowStudentPhotoRosterOperation;
    use ShowStudentListOperation;

    protected array $currency = [];

    public function __construct()
    {
        parent::__construct();
        $this->middleware('permission:courses.view', ['except' => ['showstudentlist', 'showstudentphotoroster']]);
        $this->middleware('permission:courses.edit', ['only' => ['update', 'create', 'store', 'destroy']]);

        if (config('academico.currency_position') === 'before') {
            $this->currency = ['prefix' => config('academico.currency_symbol')];
        } else {
            $this->currency = ['suffix' => config('academico.currency_symbol')];
        }
    }

    public function setup()
    {
        CRUD::setModel(Course::class);
        CRUD::setRoute(config('backpack.base.route_prefix').'/course');
        CRUD::setEntityNameStrings(__('course'), __('courses'));
        CRUD::addClause('internal');
        $permissions = backpack_user()->getAllPermissions();

        if (! $permissions->contains('name', 'courses.edit')) {
            CRUD::denyAccess(['update', 'create']);
        }

        if ($permissions->contains('name', 'courses.view')) {
            CRUD::addButtonFromView('line', 'showEnrollments', 'showEnrollmentsForCourse');
        }

        CRUD::addButtonFromView('line', 'children_badge', 'children_badge', 'beginning');

        if (! $permissions->contains('name', 'courses.delete')) {
            CRUD::denyAccess(['delete']);
        }

        if (backpack_user()->hasRole('admin')) {
            CRUD::addButtonFromView('line', 'evaluation', 'evaluation', 'end');
        }

        CRUD::addButtonFromView('top', 'courses-view-switcher', 'courses-view-switcher', 'end');
    }

    protected function setupListOperation()
    {
        CRUD::setColumns([
            [
                'label' => __('Rhythm'),
                'type' => 'select',
                'name' => 'rhythm_id',
                'entity' => 'rhythm',
                'attribute' => 'name',
                'model' => Rhythm::class,
                'searchLogic' => false,
            ],

            [
                'label' => __('Level'),
                'type' => 'select',
                'name' => 'level_id',
                'entity' => 'level',
                'attribute' => 'name',
                'model' => Level::class,
                'searchLogic' => false,
            ],

            [
                'name' => 'name',
                'label' => __('Name'),
            ],

            [
                'name' => 'volume',
                'label' => __('Presential volume'),
                'suffix' => 'h',
                'type' => 'number',
                'searchLogic' => false,
            ],

            [
                'name' => 'remote_volume',
                'label' => __('Remote volume'),
                'suffix' => 'h',
                'type' => 'number',
                'searchLogic' => false,
            ],

            [
                'label' => __('Teacher'),
                'type' => 'select',
                'name' => 'teacher_id',
                'entity' => 'teacher',
                'attribute' => 'name',
                'model' => Teacher::class,
                'searchLogic' => false,
            ],

            [
                'label' => __('Room'),
                'type' => 'select',
                'name' => 'room_id',
                'entity' => 'room',
                'attribute' => 'name',
                'model' => Room::class,
                'searchLogic' => false,
            ],

            [
                'name' => 'times',
                'label' => __('Schedule'),
                'type' => 'model_function',
                'function_name' => 'getCourseTimesAttribute',
                'limit' => 150,
                'searchLogic' => false,
            ],

            [
                'name' => 'enrollments',
                'label' => __('Enrollments'),
                'type' => 'model_function',
                'function_name' => 'getCourseEnrollmentsCountAttribute',
                'searchLogic' => false,
            ],

            [
                'name' => 'start_date',
                'label' => __('Start Date'),
                'type' => 'date',
                'searchLogic' => false,
            ],

            [
                'name' => 'end_date',
                'label' => __('End Date'),
                'type' => 'date',
                'searchLogic' => false,
            ],

            [
                'name'  => 'marked',
                'label' => __('Evaluation ready'),
                'type'  => 'check',
            ],
        ]);
    }

    protected function setupCreateOperation()
    {
        CRUD::setValidation(CourseCreateRequest::class);

        $this->addRhythmField();
        $this->addLevelField();
        $this->addNameField();
        $this->addPriceFields();

        $this->addCourseInfoFields();

        if (config('lms.sync_to') == 'apolearn') {
            CRUD::addField([
                'name' => 'sync_to_lms',
                'label' => __('Sync to LMS'),
                'type' => 'checkbox',
                'tab' => __('Course info'),
            ]);
        }

        $this->addCourseResourceFields();
        $this->addCoursePedagogyFields();

        CRUD::addField([
            'type' => 'hidden',
            'name' => 'campus_id',
            'value' => 1,
        ]);

        $this->addCourseScheduleFields();
        $this->addCourseScheduleFieldsForRealCourses();
    }

    protected function setupUpdateOperation()
    {
        if (config('academico.currency_position') === 'before') {
            $this->currency = ['prefix' => config('academico.currency_symbol')];
        } else {
            $this->currency = ['suffix' => config('academico.currency_symbol')];
        }

        if ($this->crud->getCurrentEntry()->children->count() > 0) {
            CRUD::addField([
                'name' => 'custom-ajax-button',
                'type' => 'view',
                'view' => 'courses/parent-course-alert',
            ]);
        }

        if ($this->crud->getCurrentEntry()->parent_course_id !== null) {
            CRUD::addField([
                'name' => 'custom-ajax-button',
                'type' => 'view',
                'view' => 'courses/child-course-alert',
            ]);
        }

        $this->addRhythmField();

        // unless the course has children, show the level field
        if ($this->crud->getCurrentEntry()->children->count() == 0) {
            $this->addLevelField();
        }

        $this->addNameField();
        $this->addPriceFields();
        $this->addCourseInfoFields();

        // unless the course has children, show the resources tab
        if ($this->crud->getCurrentEntry()->children->count() == 0) {
            $this->addCourseResourceFields();
        }

        $this->addCoursePedagogyFields();

        CRUD::addFields([
            [
                'label' => __('Evaluation ready'),
                'name' => 'marked',
                'tab' => __('Pedagogy'),
            ],
        ]);

        $this->addCourseScheduleFields();

        // unless the course has children, show the coursetimes tab
        if ($this->crud->getCurrentEntry()->children->count() == 0) {
            $this->addCourseScheduleFieldsForRealCourses();
        }

        CRUD::setValidation();
    }

    protected function addRhythmField() {
        CRUD::addField([
            'label' => __('Rhythm'),
            'type' => 'select',
            'name' => 'rhythm_id',
            'entity' => 'rhythm',
            'attribute' => 'name',
            'model' => Rhythm::class,
            'tab' => __('Course info'),
        ]);
    }

    protected function addLevelField()
    {
        CRUD::addField([
            'label' => __('Level'),
            'type' => 'select',
            'name' => 'level_id',
            'entity' => 'level',
            'attribute' => 'name',
            'model' => Level::class,
            'tab' => __('Course info'),
        ]);
    }

    protected function addNameField()
    {
        CRUD::addField([
            'name' => 'name',
            'label' => __('Name'),
            'tab' => __('Course info'),
        ]);
    }

    private function addPriceFields()
    {
        CRUD::addField(array_merge([
                'name' => 'price',
                'label' => __('Price'),
                'tab' => __('Course info'),
                'type' => 'number',
            ], $this->currency));

        if (config('invoicing.price_categories_enabled')) {
            CRUD::addFields([
                array_merge([
                    'name' => 'price_b',
                    'label' => __('Price B'),
                    'tab' => __('Course info'),
                    'type' => 'number',
                ], $this->currency),

                array_merge([
                    'name' => 'price_c',
                    'label' => __('PriceC'),
                    'tab' => __('Course info'),
                    'type' => 'number',
                ], $this->currency),
            ]);
        }
    }

    private function addCourseInfoFields()
    {
        CRUD::addFields([
            [
                'name' => 'volume',
                'label' => __('Presential volume'),
                'suffix' => 'h',
                'tab' => __('Course info'),
            ],

            [
                'name' => 'remote_volume',
                'label' => __('Remote volume'),
                'suffix' => 'h',
                'tab' => __('Course info'),
            ],

            [
                'name' => 'spots',
                'label' => __('Spots'),
                'tab' => __('Course info'),
            ],

            [
                'name' => 'exempt_attendance',
                'label' => __('Exempt Attendance'),
                'type' => 'checkbox',
                'tab' => __('Course info'),
            ],
        ]);
    }

    protected function addCourseResourceFields()
    {
        CRUD::addFields([
            [
                'label' => __('Teacher'),
                'type' => 'select',
                'name' => 'teacher_id',
                'entity' => 'teacher',
                'attribute' => 'name',
                'model' => Teacher::class,
                'tab' => __('Resources'),
            ],

            [
                'label' => __('Room'),
                'type' => 'select',
                'name' => 'room_id',
                'entity' => 'room',
                'attribute' => 'name',
                'model' => Room::class,
                'tab' => __('Resources'),
            ],
        ]);
    }

    private function addCoursePedagogyFields()
    {
        CRUD::addFields([
            [
                'label' => __('Books'),
                'type' => 'select_multiple',
                'name' => 'books',
                'entity' => 'books',
                'attribute' => 'name',
                'model' => Book::class,
                'pivot' => true,
                'tab' => __('Pedagogy'),
            ],

            [
                'label' => __('Evaluation method'),
                'type' => 'select',
                'name' => 'evaluationType',
                'entity' => 'evaluationType',
                'attribute' => 'name',
                'model' => EvaluationType::class,
                'tab' => __('Pedagogy'),
            ],
        ]);
    }

    protected function addCourseScheduleFields()
    {
        $defaultPeriod = Period::get_default_period();

        CRUD::addFields([
            [
                'label' => __('Period'),
                'type' => 'select',
                'name' => 'period_id',
                'entity' => 'period',
                'attribute' => 'name',
                'model' => Period::class,
                'default' => $defaultPeriod->id,
                'tab' => __('Schedule'),
            ],

            [
                'name' => 'start_date',
                'label' => __('Start Date'),
                'type' => 'date',
                'default' => $defaultPeriod->start,
                'tab' => __('Schedule'),
            ],

            [
                'name' => 'end_date',
                'label' => __('End Date'),
                'type' => 'date',
                'default' => $defaultPeriod->end,
                'tab' => __('Schedule'),
            ],
        ]);
    }

    protected function addCourseScheduleFieldsForRealCourses()
    {

    }

    protected function createSublevels($course, $sublevels, $courseTimes, $teacherId, $roomId): void
    {
        foreach ($sublevels as $sublevel) {
            // create the subcourse and link it to the parent
            $childCourse = Course::create([
                'campus_id' => $course->campus_id,
                'rhythm_id' => $course->rhythm_id,
                'level_id' => $sublevel['level_id'],
                'volume' => $sublevel['volume'] ?? null,
                'remote_volume' => $sublevel['remote_volume'] ?? null,
                'name' => $sublevel['name'],
                'price' => $sublevel['price'],
                'start_date' => $sublevel['start_date'],
                'end_date' => $sublevel['end_date'],
                'room_id' => $roomId,
                'teacher_id' => $teacherId,
                'parent_course_id' => $course->id,
                'exempt_attendance' => $course->exempt_attendance,
                'period_id' => $course->period_id,
                'spots' => $course->spots,
            ]);

            $childCourse->saveCourseTimes($courseTimes);
            $childCourse->books()->attach($course->books);
        }
    }


    public function store()
    {
        $teacherId = $this->crud->getRequest()->input('teacher_id');
        $roomId = $this->crud->getRequest()->input('room_id');

        $courseTimes = collect($this->crud->getRequest()->input('times'));

        $sublevels = collect($this->crud->getRequest()->input('children'));

        // if subcourses were added
        if ($sublevels->count() > 0) {
            // do not persist level on parent
            $this->crud->getRequest()->request->remove('level_id');

            // do not persist resources on parent but on children
            $this->crud->getRequest()->request->remove('teacher_id');
            $this->crud->getRequest()->request->remove('room_id');

            // do not persist course times on parent but on children
            $this->crud->getRequest()->request->remove('times');
            $this->crud->getRequest()->request->remove('children');
        }

        $response = $this->traitStore();
        $course = $this->crud->getCurrentEntry();

        if ($sublevels->count() > 0) {
            // create sublevels and apply coursetimes to them
            $this->createSublevels($course, $sublevels, $courseTimes, $teacherId, $roomId);
        } else {
            // otherwise, apply course times to the parent.
            $course->saveCourseTimes($courseTimes);
        }

        return $response;
    }
}
