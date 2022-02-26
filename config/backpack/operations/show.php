<?php

/**
 * Configurations for Backpack's ShowOperation.
 *
 * @see https://backpackforlaravel.com/docs/crud-operation-show
 */

return [
    // Define the size/looks of the content div for all CRUDs
    // To override per Controller use $this->crud->setShowContentClass('class-string')
    'contentClass' => 'col-md-8',

    // Automatically add all columns from the db table?
    'setFromDb'  => true,

    // Automatically add created_at and updated_at columns, if model has timestamps?
    'timestamps' => false,

    // If model has SoftDeletes, allow the admin to access the Show page for
    // soft deleted items & add a deleted_at column to ShowOperation?
    'softDeletes' => false,

];
