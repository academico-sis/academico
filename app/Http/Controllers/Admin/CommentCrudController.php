<?php

namespace App\Http\Controllers\Admin;

use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;

// VALIDATION: change the requests to match your own file names if you need form validation
use App\Http\Requests\CommentRequest as UpdateRequest;
use App\Models\User;

/**
 * Class CommentCrudController
 * Monitor comments. This controller is NOT used to add comments, only to view, edit or delete them.
 * A comment may be attached to various models (polymorphism).
 * @package App\Http\Controllers\Admin
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
        CRUD::setModel('App\Models\Comment');
        CRUD::setRoute(config('backpack.base.route_prefix') . '/comment');
        CRUD::setEntityNameStrings('comment', 'comments');

        /*
        |--------------------------------------------------------------------------
        | CrudPanel Configuration
        |--------------------------------------------------------------------------
        */


        CRUD::setColumns([
            [
                // Commentable entity
                'label' => "Commentable", // Table column heading
                'type' => "select",
                'name' => 'commentable_id', // the column that contains the ID of that connected entity;
                'entity' => 'commentable', // the method that defines the relationship in your Model
                'attribute' => "name", // foreign key attribute that is shown to user
             ],

             [
                // Commentable entity
                'label' => "Commentable", // Table column heading
                'type' => "text",
                'name' => 'body', // the column that contains the ID of that connected entity;
             ],

             [
                // Commentable entity
                'label' => "Author", // Table column heading
                'type' => "select",
                'name' => 'author_id', // the column that contains the ID of that connected entity;
                'entity' => 'author', // the method that defines the relationship in your Model
                'attribute' => "name", // foreign key attribute that is shown to user
             ],

             [
                // Commentable entity
                'label' => "Action", // Table column heading
                'type' => "boolean",
                'name' => 'action', // the column that contains the ID of that connected entity;
             ],
        ]);


        CRUD::addFields([

             ['label' => "Comment", 'type' => "text", 'name' => 'body'],

             ['label' => "Action", 'type' => "checkbox", 'name' => 'action'],

        ]);


        CRUD::addFilter([ // simple filter
            'type' => 'simple',
            'name' => 'action',
            'label'=> 'Action'
          ], 
          false, 
          function() { // if the filter is active
            CRUD::addClause('where', 'action', true);
          } );


          CRUD::addFilter([ // dropdown filter
            'name' => 'type',
            'type' => 'dropdown',
            'label'=> 'Type'
          ], [
            'App\Models\Student' => 'Student',
            'App\Models\Enrollment' => 'Enrollments',
            'App\Models\PreInvoice' => 'PreInvoice',
            'App\Models\Result' => 'Result',

          ], function($value) { // if the filter is active
              CRUD::addClause('where', 'commentable_type', '=', $value);
          },
          function() { // if the filter is not active
            CRUD::addClause('where', 'commentable_type', '=', 'App\Models\Student');
            $this->crud->request->request->add(['commentable_type' => 'App\Models\Student']); // to make the filter look active

        });

        
        // add asterisk for fields that are required in CommentRequest
        CRUD::setRequiredFields(UpdateRequest::class, 'edit');
    }


    protected function setupUpdateOperation()
    {
        CRUD::setValidation(UpdateRequest::class);
    }

}
