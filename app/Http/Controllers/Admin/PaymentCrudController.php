<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\PaymentRequest;
use App\Models\Payment;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Http\Controllers\Operations\DeleteOperation;
use Backpack\CRUD\app\Http\Controllers\Operations\UpdateOperation;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;

/**
 * Class PaymentCrudController.
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class PaymentCrudController extends CrudController
{
    use UpdateOperation;
    use DeleteOperation;

    /**
     * Configure the CrudPanel object. Apply settings to all operations.
     *
     * @return void
     */
    public function setup()
    {
        CRUD::setModel(Payment::class);
        CRUD::setRoute(config('backpack.base.route_prefix').'/payment');
        CRUD::setEntityNameStrings(__('payment'), __('payments'));
        $this->crud->replaceSaveActions(
            [
                'name' => __('Save and go back'),
                'visible' => function ($crud) {
                    return true;
                },
                'redirect' => function ($crud, $request, $itemId) {
                    $enrollment_id = Payment::find($itemId)->enrollment_id;

                    return route('enrollment.show', ['id' => $enrollment_id]);
                },
            ],
        );
    }

    /**
     * Define what happens when the Update operation is loaded.
     *
     * @see https://backpackforlaravel.com/docs/crud-operation-update
     * @return void
     */
    protected function setupUpdateOperation()
    {
        CRUD::setValidation(PaymentRequest::class);
        CRUD::field('value')->type('number');
        CRUD::field('invoice_id')->type('text');
        CRUD::field('comment')->type('text');
    }
}
