<?php

namespace App\Http\Controllers\Admin;

use Backpack\CRUD\app\Http\Controllers\CrudController;

// VALIDATION: change the requests to match your own file names if you need form validation
use App\Http\Requests\CommentRequest as UpdateRequest;
use App\Models\User;

/**
 * Class CommentCrudController
 * @package App\Http\Controllers\Admin
 * @property-read CrudPanel $crud
 */
class CommentCrudController extends CrudController
{

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
        $this->crud->setModel('App\Models\Comment');
        $this->crud->setRoute(config('backpack.base.route_prefix') . '/comment');
        $this->crud->setEntityNameStrings('comment', 'comments');
        $this->crud->denyAccess('create');
        /*
        |--------------------------------------------------------------------------
        | CrudPanel Configuration
        |--------------------------------------------------------------------------
        */


        $this->crud->setColumns([
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
                'label' => "Private", // Table column heading
                'type' => "boolean",
                'name' => 'private', // the column that contains the ID of that connected entity;
             ],
        ]);


        $this->crud->addFields([

             ['label' => "Comment", 'type' => "text", 'name' => 'body'],

             ['label' => "Private", 'type' => "checkbox", 'name' => 'private'],

        ]);


        $this->crud->addFilter([ // simple filter
            'type' => 'simple',
            'name' => 'private',
            'label'=> 'Private'
          ], 
          false, 
          function() { // if the filter is active
            $this->crud->addClause('where', 'private', true);
          } );


          $this->crud->addFilter([ // dropdown filter
            'name' => 'type',
            'type' => 'dropdown',
            'label'=> 'Type'
          ], [
            'App\Models\User' => 'Students',
            'App\Models\Enrollment' => 'Enrollments',
            'App\Models\PreInvoice' => 'PreInvoice',
            'App\Models\Result' => 'Result',

          ], function($value) { // if the filter is active
              $this->crud->addClause('where', 'commentable_type', '=', $value);
          },
          function() { // if the filter is not active
            $this->crud->addClause('where', 'commentable_type', '=', 'App\Models\User');
            $this->crud->request->request->add(['commentable_type' => 'App\Models\User']); // to make the filter look active

        });

        
        // add asterisk for fields that are required in CommentRequest
        $this->crud->setRequiredFields(UpdateRequest::class, 'edit');
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
