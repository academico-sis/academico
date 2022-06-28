<?php

namespace App\Http\Controllers\Admin;

use App\Models\Institution;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Http\Controllers\Operations\CreateOperation;
use Backpack\CRUD\app\Http\Controllers\Operations\InlineCreateOperation;
use Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
use Backpack\CRUD\app\Http\Controllers\Operations\ShowOperation;
use Backpack\CRUD\app\Http\Controllers\Operations\UpdateOperation;

class InstitutionCrudController extends CrudController
{
    use ListOperation;
    use CreateOperation;
    use InlineCreateOperation;
    use UpdateOperation;
    use ShowOperation { show as traitShow; }

    public function setup()
    {
        $this->crud->setModel(Institution::class);
        $this->crud->setRoute(config('backpack.base.route_prefix').'/institution');
        $this->crud->setEntityNameStrings('institution', 'institutions');
    }

    protected function setupListOperation()
    {
        $this->crud->addColumn([
            'name' => 'name',
            'type' => 'text',
            'label' => __('Name'),
        ]);
    }

    protected function setupCreateOperation()
    {
        $this->crud->setValidation([
            'name' => 'required|min:1|max:255',
        ]);

        $this->crud->addField([
            'name' => 'name',
            'type' => 'text',
            'label' => __('Name'),
        ]);
    }

    protected function setupUpdateOperation()
    {
        $this->setupCreateOperation();
    }

    public function show($id)
    {
        return redirect()->route('student.index', ['institution_id' => $id]);
    }
}
