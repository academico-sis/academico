<?php

namespace App\Http\Controllers\Admin;

use App\Models\Partner;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Http\Controllers\Operations\CreateOperation;
use Backpack\CRUD\app\Http\Controllers\Operations\DeleteOperation;
use Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
use Backpack\CRUD\app\Http\Controllers\Operations\ShowOperation;
use Backpack\CRUD\app\Http\Controllers\Operations\UpdateOperation;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;
use Illuminate\Validation\Rule;

class PartnerCrudController extends CrudController
{
    use ListOperation;
    use CreateOperation;
    use UpdateOperation;
    use DeleteOperation;
    use ShowOperation;

    public function setup()
    {
        CRUD::setModel(Partner::class);
        CRUD::setRoute(config('backpack.base.route_prefix').'/partner');
        CRUD::setEntityNameStrings(__('partnership'), __('partnerships'));
    }

    protected function setupListOperation()
    {
        CRUD::addColumn([
            'name' => 'name',
            'label' => __('Name'),
            'type' => 'text',
        ]);

        CRUD::addColumn([
            'name' => 'started_on',
            'label' => __('Start Date'),
            'type' => 'date',
        ]);

        CRUD::addColumn([
            'name' => 'expired_on',
            'label' => __('End Date'),
            'type' => 'date',
        ]);

        CRUD::addColumn([
            'name' => 'send_report_on',
            'label' => __('Send report on ... of the month'),
            'type' => 'number',
        ]);

        CRUD::addColumn([
            'name' => 'auto_renewal',
            'label' => __('Tacit renewal'),
            'type' => 'check',
        ]);
    }

    protected function setupCreateOperation()
    {
        CRUD::setValidation([
            'name' => [
                'required',
                'min:1',
                'max:255',
                Rule::unique($this->crud->getModel()->getTable())->ignore($this->crud->getCurrentEntry()),
            ],
            'started_on' => 'required|date',
            'expired_on' => 'nullable|date',
            'last_alert_sent_at' => 'nullable|date',
            'send_report_on' => 'nullable|integer',
        ]);

        CRUD::addField([
            'name' => 'name',
            'label' => __('Name'),
            'type' => 'text',
        ]);

        CRUD::addField([
            'name' => 'started_on',
            'label' => __('Start Date'),
            'type' => 'date',
        ]);

        CRUD::addField([
            'name' => 'expired_on',
            'label' => __('End Date'),
            'type' => 'date',
        ]);

        CRUD::addField([
            'name' => 'auto_renewal',
            'label' => __('Tacit renewal'),
            'type' => 'checkbox',
        ]);

        CRUD::addField([
            'name' => 'send_report_on',
            'label' => __('Send report on ... of the month'),
            'type' => 'number',
            'attributes' => [
                'step' => 1,
                'min' => 1,
                'max' => 28,
            ],
        ]);
    }

    protected function setupUpdateOperation()
    {
        $this->setupCreateOperation();
    }
}
