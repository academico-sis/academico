<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\CommentRequest;
use App\Models\Comment;
use App\Models\Enrollment;
use App\Models\Invoice;
use App\Models\Result;
use App\Models\Student;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Http\Controllers\Operations\DeleteOperation;
use Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
use Backpack\CRUD\app\Http\Controllers\Operations\UpdateOperation;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;

/**
 * Class CommentCrudController
 * Monitor comments. This controller is NOT used to add comments, only to view, edit or delete them.
 * A comment may be attached to various models (polymorphism).
 */
class CommentCrudController extends CrudController
{
    use ListOperation;
    use UpdateOperation;
    use DeleteOperation;

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
        CRUD::setModel(Comment::class);
        CRUD::setRoute(config('backpack.base.route_prefix').'/comment');
        CRUD::setEntityNameStrings(__('comment'), __('comments'));
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
                'label' => 'Commentable',
                'type' => 'select',
                'name' => 'commentable_id',
                'entity' => 'commentable',
                'attribute' => 'name',
            ],

            [
                // Commentable entity
                'label' => 'Commentable',
                'type' => 'text',
                'name' => 'body',
            ],

            [
                // Commentable entity
                'label' => 'Author',
                'type' => 'select',
                'name' => 'author_id',
                'entity' => 'author',
                'attribute' => 'name',
            ],

            [
                // Commentable entity
                'label' => 'Action',
                'type' => 'boolean',
                'name' => 'action',
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
                Student::class => 'Student',
                Enrollment::class => 'Enrollments',
                Invoice::class => 'Invoice',
                Result::class => 'Result',

            ],
            function ($value) { // if the filter is active
                CRUD::addClause('where', 'commentable_type', '=', $value);
            },
            function () { // if the filter is not active
                  CRUD::addClause('where', 'commentable_type', '=', Student::class);
                  $this->crud->getRequest()->request->add(['commentable_type' => Student::class]); // to make the filter look active
              }
        );
    }

    public function setupUpdateOperation()
    {
        CRUD::addFields([
            ['label' => 'Comment', 'type' => 'text', 'name' => 'body'],
            ['label' => 'Action', 'type' => 'checkbox', 'name' => 'action'],
        ]);

        CRUD::setRequiredFields(CommentRequest::class);
    }
}
