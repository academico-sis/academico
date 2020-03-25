<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\CommentRequest as UpdateRequest;
use App\Models\User;
// VALIDATION: change the requests to match your own file names if you need form validation
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;

/**
 * Class CommentCrudController
 * Monitor comments. This controller is NOT used to add comments, only to view, edit or delete them.
 * A comment may be attached to various models (polymorphism).
 *
 * @property-read CrudPanel $crud
 */
class CommentCrudController extends CrudController
{
    use \Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\UpdateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\DeleteOperation;

    public function __construct()
    {
        parent::__construct();
        $this->middleware(['permission:comments.edit']);
    }

    public function setup()
    {
        /*
        |--------------------------------------------------------------------------
        | CrudPanel Basic Information
        |--------------------------------------------------------------------------
        */
        CRUD::setModel(\App\Models\Comment::class);
        CRUD::setRoute(config('backpack.base.route_prefix').'/comment');
        CRUD::setEntityNameStrings('comment', 'comments');
        CRUD::setRequiredFields(UpdateRequest::class, 'edit');
    }

    /*
    |--------------------------------------------------------------------------
    | CrudPanel Configuration
    |--------------------------------------------------------------------------
    */

    public function setupListOperation()
    {
        CRUD::setColumns([
            [
                // Commentable entity
                'label'     => 'Commentable', // Table column heading
                'type'      => 'select',
                'name'      => 'commentable_id', // the column that contains the ID of that connected entity;
                'entity'    => 'commentable', // the method that defines the relationship in your Model
                'attribute' => 'name', // foreign key attribute that is shown to user
            ],

            [
                // Commentable entity
                'label' => 'Commentable', // Table column heading
                'type'  => 'text',
                'name'  => 'body', // the column that contains the ID of that connected entity;
            ],

            [
                // Commentable entity
                'label'     => 'Author', // Table column heading
                'type'      => 'select',
                'name'      => 'author_id', // the column that contains the ID of that connected entity;
                'entity'    => 'author', // the method that defines the relationship in your Model
                'attribute' => 'name', // foreign key attribute that is shown to user
            ],

            [
                // Commentable entity
                'label' => 'Action', // Table column heading
                'type'  => 'boolean',
                'name'  => 'action', // the column that contains the ID of that connected entity;
            ],
        ]);

        CRUD::addFilter(
            [ // simple filter
                'type' => 'simple',
                'name' => 'action',
                'label'=> 'Action',
            ],
            false,
            function () { // if the filter is active
                  CRUD::addClause('where', 'action', true);
              }
        );

        CRUD::addFilter(
            [ // dropdown filter
                'name' => 'type',
                'type' => 'dropdown',
                'label'=> 'Type',
            ],
            [
                \App\Models\Student::class    => 'Student',
                \App\Models\Enrollment::class => 'Enrollments',
                \App\Models\PreInvoice::class => 'PreInvoice',
                \App\Models\Result::class     => 'Result',

            ],
            function ($value) { // if the filter is active
                  CRUD::addClause('where', 'commentable_type', '=', $value);
              },
            function () { // if the filter is not active
                  CRUD::addClause('where', 'commentable_type', '=', \App\Models\Student::class);
                  $this->crud->request->request->add(['commentable_type' => \App\Models\Student::class]); // to make the filter look active
              }
        );
    }

    public function setupUpdateOperation()
    {
        CRUD::addFields([
            ['label' => 'Comment', 'type' => 'text', 'name' => 'body'],
            ['label' => 'Action', 'type' => 'checkbox', 'name' => 'action'],
        ]);
    }
}
