<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\PartnerRequest;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;

/**
 * Class PartnerCrudController
 * @package App\Http\Controllers\Admin
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class PartnerCrudController extends CrudController
{
    use \Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\CreateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\UpdateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\DeleteOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\ShowOperation;

    /**
     * Configure the CrudPanel object. Apply settings to all operations.
     *
     * @return void
     */
    public function setup()
    {
        CRUD::setModel(\App\Models\Partner::class);
        CRUD::setRoute(config('backpack.base.route_prefix') . '/partner');
        CRUD::setEntityNameStrings(__('partnership'), __('partnerships'));
    }

    /**
     * Define what happens when the List operation is loaded.
     *
     * @see  https://backpackforlaravel.com/docs/crud-operation-list-entries
     * @return void
     */
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

    /**
     * Define what happens when the Create operation is loaded.
     *
     * @see https://backpackforlaravel.com/docs/crud-operation-create
     * @return void
     */
    protected function setupCreateOperation()
    {
        CRUD::setValidation(PartnerRequest::class);
        
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

        CRUD::addField([   // Checkbox
            'name'  => 'auto_renewal',
            'label' => __('Tacit renewal'),
            'type'  => 'checkbox'
        ]);

        CRUD::addField([
            'name' => 'send_report_on',
            'label' => __('Send report on ... of the month'),
            'type' => 'number',
            'attributes' => ['step' => 1, 'min' => 1, 'max' => 28],
        ]);
    }

    /**
     * Define what happens when the Update operation is loaded.
     *
     * @see https://backpackforlaravel.com/docs/crud-operation-update
     * @return void
     */
    protected function setupUpdateOperation()
    {
        $this->setupCreateOperation();
    }
}
