<?php

namespace App\Http\Controllers\Admin;

use App\Models\Member;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Http\Controllers\Operations\CreateOperation;
use Backpack\CRUD\app\Http\Controllers\Operations\DeleteOperation;
use Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
use Backpack\CRUD\app\Http\Controllers\Operations\ShowOperation;
use Backpack\CRUD\app\Http\Controllers\Operations\UpdateOperation;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;

class MemberCrudController extends CrudController
{
    use ListOperation;
    use CreateOperation;
    use UpdateOperation;
    use DeleteOperation;
    use ShowOperation;

    public function setup()
    {
        CRUD::setModel(Member::class);
        CRUD::setRoute(config('backpack.base.route_prefix').'/member');
        CRUD::setEntityNameStrings('member', 'members');
    }

    protected function setupListOperation()
    {
        CRUD::setFromDb();
    }

    protected function setupCreateOperation()
    {
        CRUD::setValidation([
            'firstname' => 'required|min:1|max:255',
            'lastname' => 'required|min:1|max:255',
            'email' => 'nullable|email',
        ]);

        CRUD::setFromDb();
    }

    protected function setupUpdateOperation()
    {
        $this->setupCreateOperation();
    }
}
