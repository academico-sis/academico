<?php

namespace App\Http\Controllers\Admin;

use Backpack\CRUD\app\Http\Controllers\CrudController;

// VALIDATION: change the requests to match your own file names if you need form validation
use App\Http\Requests\CartRequest as StoreRequest;
use App\Http\Requests\CartRequest as UpdateRequest;
use Backpack\CRUD\CrudPanel;

/**
 * Class CartCrudController
 * @package App\Http\Controllers\Admin
 * @property-read CrudPanel $crud
 */
class CartCrudController extends CrudController
{
    public function setup()
    {
        /*
        |--------------------------------------------------------------------------
        | CrudPanel Basic Information
        |--------------------------------------------------------------------------
        */
        $this->crud->setModel('App\Models\Cart');
        $this->crud->setRoute(config('backpack.base.route_prefix') . '/cart');
        $this->crud->setEntityNameStrings('cart', 'carts');

        $this->crud->denyAccess('update');
        $this->crud->denyAccess('create');
        $this->crud->allowAccess('show');


        /*
        |--------------------------------------------------------------------------
        | CrudPanel Configuration
        |--------------------------------------------------------------------------
        */
        $this->crud->setColumns([

            [
                'name' => 'id',
                'label' => "ID",
            ],

            [
            // STUDENT NAME
            'label' => __("Student"), // Table column heading
            'type' => "select",
            'entity' => 'student', // the method that defines the relationship in your Model
            'attribute' => "name", // foreign key attribute that is shown to user
            'searchLogic' => function ($query, $column, $searchTerm) {
                $query->orWhereHas('student', function ($q) use ($column, $searchTerm) {
                    $q->WhereHas('user', function ($q) use ($column, $searchTerm) {
                        $q->where('firstname', 'like', '%'.$searchTerm.'%')
                        ->orWhere('lastname', 'like', '%'.$searchTerm.'%');
                    });
                });
            }
            ],
        ]);

        // add asterisk for fields that are required in CartRequest
        $this->crud->setRequiredFields(StoreRequest::class, 'create');
        $this->crud->setRequiredFields(UpdateRequest::class, 'edit');
    }

    public function store(StoreRequest $request)
    {
        // your additional operations before save here
        $redirect_location = parent::storeCrud($request);
        // your additional operations after save here
        // use $this->data['entry'] or $this->crud->entry
        return $redirect_location;
    }

    public function update(UpdateRequest $request)
    {
        // your additional operations before save here
        $redirect_location = parent::updateCrud($request);
        // your additional operations after save here
        // use $this->data['entry'] or $this->crud->entry
        return $redirect_location;
    }
}
