<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\TaxRequest;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;

/**
 * Class TaxCrudController
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class TaxCrudController extends CrudController
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
        CRUD::setModel(\App\Models\Tax::class);
        CRUD::setRoute(config('backpack.base.route_prefix').'/tax');
        CRUD::setEntityNameStrings('tax', 'taxes');
    }

    /**
     * Define what happens when the List operation is loaded.
     *
     * @see  https://backpackforlaravel.com/docs/crud-operation-list-entries
     * @return void
     */
    protected function setupListOperation()
    {
        CRUD::setColumns([
            [
                'name' => 'id',
                'label' => 'ID',
            ],
            [
                // Discount name
                'label' => __('Name'),
                'type' => 'text',
                'name' => 'name',
            ],
            [
                // Value
                'label' => __('Discount Value'),
                'type' => 'decimal',
                'name' => 'value',
                'suffix' => '%',
            ],
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
        CRUD::setValidation(TaxRequest::class);

        CRUD::addFields([
            [
                // Discount name
                'label' => __('Name'),
                'type' => 'text',
                'name' => 'name',
            ],
            [
                // Value
                'label' => __('Value (0-100%)'),
                'type' => 'number',
                'name' => 'value',
            ],
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
