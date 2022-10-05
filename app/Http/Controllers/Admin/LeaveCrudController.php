<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\LeaveRequest;
use App\Models\Leave;
use App\Models\LeaveType;
use App\Models\Teacher;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Http\Controllers\Operations\CreateOperation;
use Backpack\CRUD\app\Http\Controllers\Operations\DeleteOperation;
use Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
use Backpack\CRUD\app\Http\Controllers\Operations\UpdateOperation;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;
use Carbon\Carbon;

class LeaveCrudController extends CrudController
{
    use ListOperation;
    use CreateOperation;
    use UpdateOperation;
    use DeleteOperation;

    public function setup()
    {
        CRUD::setModel(Leave::class);
        CRUD::setRoute(config('backpack.base.route_prefix').'/leave');
        CRUD::setEntityNameStrings(__('leave'), __('leaves'));
    }

    protected function setupListOperation()
    {
        CRUD::setColumns([
            [
                'label' => __('Teacher'),
                'type' => 'select',
                'name' => 'teacher_id',
                'entity' => 'teacher',
                'attribute' => 'name',
                'model' => Teacher::class,
            ],

            [
                'label' => __('Type'),
                'type' => 'select',
                'name' => 'leave_type_id',
                'entity' => 'leaveType',
                'attribute' => 'name',
                'model' => LeaveType::class,
            ],

            [
                'name' => 'date',
                'label' => __('Date'),
                'type' => 'date',
            ],
        ]);

        CRUD::addFilter(
            [
                'name' => 'teacher_id',
                'type' => 'select2',
                'label' => __('Teacher'),
            ],
            fn () => Teacher::all()->pluck('name', 'id')->toArray(),
            function ($value) {
                CRUD::addClause('where', 'teacher_id', $value);
            },
            function () { // if the filter is NOT active (the GET parameter "checkbox" does not exit)
            }
        );

        CRUD::addFilter(
            [
                'type' => 'date_range',
                'name' => 'from_to',
                'label' => __('Date range'),
            ],
            false,
            function ($value) { // if the filter is active, apply these constraints
                $dates = json_decode($value, null, 512, JSON_THROW_ON_ERROR);
                CRUD::addClause('where', 'date', '>=', $dates->from);
                CRUD::addClause('where', 'date', '<=', $dates->to.' 23:59:59');
            }
        );
    }

    protected function setupCreateOperation()
    {
        CRUD::setValidation(LeaveRequest::class);

        CRUD::addFields([
            [
                'label' => __('Teacher'),
                'type' => 'select_multiple',
                'name' => 'teacher_id',
                'entity' => 'teacher',
                'attribute' => 'name',
                'model' => Teacher::class,
            ],

            [
                'label' => __('Type'),
                'type' => 'select',
                'name' => 'leave_type_id',
                'entity' => 'leaveType',
                'attribute' => 'name',
                'model' => LeaveType::class,
            ],

            [
                'name' => ['start_date', 'end_date'],
                'label' => __('Date Range'),
                'type' => 'date_range',
                'default' => [Carbon::now()->format('Y-m-d 00:00'), Carbon::now()->addDays(2)->format('Y-m-d 00:00')],
            ],
        ]);
    }

    public function store()
    {
        $request = $this->crud->getRequest();

        foreach ($request->teacher_id as $teacher_id) {
            $start = Carbon::parse($request->start_date);
            $end = Carbon::parse($request->end_date);

            while ($start <= $end) {
                Leave::create([
                    'teacher_id' => $teacher_id,
                    'date' => $start,
                    'leave_type_id' => $request->leave_type_id,
                ]);

                $start->addDay();
            }
        }

        return redirect()->to('/leave/teachers');
    }

    protected function setupUpdateOperation()
    {
        CRUD::setValidation(LeaveRequest::class);

        CRUD::addFields([
            [
                'label' => 'Type',
                'type' => 'select',
                'name' => 'leave_type_id',
                'entity' => 'leaveType',
                'attribute' => 'name',
                'model' => LeaveType::class,
            ],

            [
                'name' => 'date',
                'label' => 'Event Date',
                'type' => 'date',
            ],
        ]);
    }
}
