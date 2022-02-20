<?php

namespace App\Http\Controllers\Admin;

use App\Events\UserCreated;
use App\Events\UserUpdated;
use App\Http\Requests\UserStoreCrudRequest as StoreRequest;
use App\Http\Requests\UserUpdateCrudRequest as UpdateRequest;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Http\Controllers\Operations\CreateOperation;
use Backpack\CRUD\app\Http\Controllers\Operations\DeleteOperation;
use Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
use Backpack\CRUD\app\Http\Controllers\Operations\UpdateOperation;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;
use Backpack\PermissionManager\app\Models\Role;

class UserCrudController extends CrudController
{
    use ListOperation;
    use CreateOperation { store as traitStore; }
    use UpdateOperation { update as traitUpdate; }
    use DeleteOperation;

    public function setup()
    {
        CRUD::setModel(config('backpack.permissionmanager.models.user'));
        CRUD::setEntityNameStrings(trans('backpack::permissionmanager.user'), trans('backpack::permissionmanager.users'));
        CRUD::setRoute(backpack_url('user'));
    }

    public function setupListOperation()
    {
        // Columns.
        CRUD::setColumns([
            [
                'label' => __('First Name'),
                'type' => 'text',
                'name' => 'firstname',
            ],
            [
                'label' => __('Last Name'),
                'type' => 'text',
                'name' => 'lastname',
            ],
            [
                'name' => 'email',
                'label' => trans('backpack::permissionmanager.email'),
                'type' => 'email',
            ],
            [ // n-n relationship (with pivot table)
                'label' => trans('backpack::permissionmanager.roles'),
                'type' => 'select_multiple',
                'name' => 'roles',
                'entity' => 'roles',
                'attribute' => 'name',
                'model' => config('permission.models.role'),
            ],

        ]);
    }

    protected function setupCreateOperation()
    {
        $this->crud->setValidation(StoreRequest::class);
        $this->addFields();
    }

    protected function setupUpdateOperation()
    {
        $this->crud->setValidation(UpdateRequest::class);
        $this->addFields();
    }

    /**
     * Store a newly created resource in the database.
     */
    public function store(StoreRequest $request)
    {
        $this->handlePasswordInput($request);
        $response = $this->traitStore();

        $user = $this->crud->getCurrentEntry();
        UserCreated::dispatch($user, $request->input('password'));

        return $response;
    }

    /**
     * Update the specified resource in the database.
     */
    public function update(UpdateRequest $request)
    {
        $this->handlePasswordInput($request);
        $user = $this->crud->getCurrentEntry();
        UserUpdated::dispatch($user, $request->input('password'));

        return $this->traitUpdate();
    }

    /**
     * Add the fields needed in the Create and Update operations.
     */
    protected function addFields()
    {
        $this->crud->addFields([
            [  // Select2
                'label' => trans('firstname'),
                'type' => 'text',
                'name' => 'firstname',
            ],
            [  // Select2
                'label' => trans('lastname'),
                'type' => 'text',
                'name' => 'lastname',
            ],
            [
                'name' => 'email',
                'label' => trans('backpack::permissionmanager.email'),
                'type' => 'email',
            ],
            [
                'name' => 'password',
                'label' => trans('password'),
                'type' => 'password',
            ],

            [
                'label' => 'Roles',
                'type' => 'checklist',
                'name' => 'roles',
                'entity' => 'roles',
                'attribute' => 'name',
                'model' => Role::class,
                'pivot' => true,
            ],
        ]);
    }

    protected function handlePasswordInput($request)
    {
        $crud_request = $this->crud->getRequest();

        // If a password was specified
        if ($request->input('password')) {
            // encrypt it before storing it
            $hashed_password = bcrypt($request->input('password'));

            $crud_request->request->set('password', $hashed_password);
            $crud_request->request->set('password_confirmation', $hashed_password);
        } else {
            // ignore the password inputs entirely
            $crud_request->request->remove('password');
            $crud_request->request->remove('password_confirmation');
        }

        $this->crud->setRequest($crud_request);
    }
}
