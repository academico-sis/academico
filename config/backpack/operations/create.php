<?php

/**
 * Configurations for Backpack's CreateOperation.
 *
 * @see https://backpackforlaravel.com/docs/crud-operation-create
 */

return [
    // Define the size/looks of the content div for all CRUDs
    // To override per view use $this->crud->setCreateContentClass('class-string')
    'contentClass' => 'col-md-12 bold-labels',

    // When using tabbed forms (create & update), what kind of tabs would you like?
    'tabsType' => 'horizontal', //options: horizontal, vertical

    // How would you like the validation errors to be shown?
    'groupedErrors' => true,
    'inlineErrors' => true,

    // when the page loads, put the cursor on the first input?
    'autoFocusOnFirstField' => true,

    // Where do you want to redirect the user by default, save?
    // options: save_and_back, save_and_edit, save_and_new
    'defaultSaveAction' => 'save_and_back',

    // When the user chooses "save and back" or "save and new", show a bubble
    // for the fact that the default save action has been changed?
    'showSaveActionChange' => true, //options: true, false

    // Should we show a cancel button to the user?
    'showCancelButton' => true,

    // Should we warn a user before leaving the page with unsaved changes?
    'warnBeforeLeaving' => false,

/**
 * Before saving the entry, how would you like the request to be stripped?
 * - false - fall back to Backpack's default (ONLY save inputs that have fields)
 * - closure - process your own request (example removes all inputs that begin with underscode).
 *
 * @param  \Illuminate\Http\Request  $request
 * @return array
 */
    // 'strippedRequest' => (function ($request) {
    //     return $request->except('_token', '_method', '_http_referrer', '_current_tab', '_save_action');
    // }),
];
