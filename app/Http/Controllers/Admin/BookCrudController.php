<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\BookRequest;
use App\Models\Book;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Http\Controllers\Operations\CreateOperation;
use Backpack\CRUD\app\Http\Controllers\Operations\DeleteOperation;
use Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
use Backpack\CRUD\app\Http\Controllers\Operations\UpdateOperation;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;

class BookCrudController extends CrudController
{
    use ListOperation;
    use CreateOperation;
    use UpdateOperation;
    use DeleteOperation;

    private array $currency = [];

    public function __construct()
    {
        if (config('academico.currency_position') === 'before') {
            $this->currency = ['prefix' => config('academico.currency_symbol')];
        } else {
            $this->currency = ['suffix' => config('academico.currency_symbol')];
        }

        parent::__construct();
    }

    public function setup()
    {
        CRUD::setModel(Book::class);
        CRUD::setRoute(config('backpack.base.route_prefix') . '/book');
        CRUD::setEntityNameStrings(__('book'), __('books'));
    }

    public function setupListOperation()
    {
        CRUD::addColumns([
            [
                'name' => 'name',
                'label' => __('Name'),
            ],
            [
                'name' => 'price_with_currency',
                'label' => __('Price'),
            ],
            [
                'name' => 'product_code',
                'label' => __('Product Code'),
            ],
        ]);
    }

    public function setupCreateOperation()
    {
        CRUD::setValidation(BookRequest::class);

        CRUD::addFields([
            [
                'name' => 'name',
                'label' => __('Name'),
                'type' => 'text',
            ],

            array_merge([
                'name' => 'price',
                'label' => __('Price'),
                'type' => 'number',
                'attributes' => ['step' => '0.01'],
            ], $this->currency),

            [
                'name' => 'product_code',
                'label' => __('Product Code'),
                'type' => 'text',
            ],
        ]);
    }

    public function setupUpdateOperation()
    {
        $this->setupCreateOperation();
    }
}
