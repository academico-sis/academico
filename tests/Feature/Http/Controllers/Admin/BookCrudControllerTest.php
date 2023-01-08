<?php

namespace Tests\Feature\Http\Controllers\Admin;

use App\Models\Book;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * @see \App\Http\Controllers\Admin\BookCrudController
 */
class BookCrudControllerTest extends TestCase
{
    public $entityname;
    use RefreshDatabase;

    public $user;

    public $model;

    public $table;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed('TestSeeder');
        $this->user = factory(User::class)->create();
        $this->user->assignRole('admin');

        $this->model = Book::class;
        $this->table = 'books';
        $this->entityname = 'book';
    }

    /**
     * @test
     */
    public function create_is_permitted_for_authorized_users_only()
    {
        // unauthorized users should receive a 302
        $response = $this->get(route($this->entityname.'.create'));
        $response->assertStatus(302);

        // create model but do not persist to DB
        $entity = factory($this->model)->make();

        $response = $this->post(route($this->entityname.'.store'), $entity->toArray());
        $response->assertStatus(302);
        $this->assertDatabaseMissing($this->table, $entity->toArray());

        \Auth::guard(backpack_guard_name())->login($this->user);
        $response = $this->post(route($this->entityname.'.store'), $entity->toArray());

        $entity->price *= 100;
        $this->assertDatabaseHas($this->table, $entity->only(['name', 'price', 'product_code']));
    }

    /**
     * @test
     */
    public function destroy_is_permitted_for_authorized_users_only()
    {
        $entity = factory($this->model)->create();

        $response = $this->delete(route($this->entityname.'.destroy', ['id' => $entity->id]));
        $this->assertDatabaseHas($this->table, $entity->only(['name', 'product_code']));

        \Auth::guard(backpack_guard_name())->login($this->user);

        $response = $this->delete(route($this->entityname.'.destroy', ['id' => $entity->id]));

        $entity->price *= 100;
        $this->assertDatabaseMissing($this->table, $entity->only(['name', 'price', 'product_code']));
    }

    /**
     * @test
     */
    public function index_returns_an_ok_response()
    {
        factory($this->model)->create();
        \Auth::guard(backpack_guard_name())->login($this->user);
        $response = $this->get(route($this->entityname.'.index'));
        $response->assertOk();
    }

    public function search_returns_an_ok_response()
    {
        $this->markTestIncomplete('This test case was generated by Shift. When you are ready, remove this line and complete this test case.');

        $response = $this->post(route($this->entityname.'.search'), [
            // TODO: send request data
        ]);

        $response->assertOk();

        // TODO: perform additional assertions
    }

    /**
     * @test
     */
    public function store_returns_an_ok_response()
    {
        \Auth::guard(backpack_guard_name())->login($this->user);

        $this->post(route($this->entityname.'.store'), [
            'name' => 'My Book',
            'price' => 12,
        ]);

        $this->assertEquals(1, Book::where('name', 'My Book')->where('price', 1200)->count());
    }

    /**
     * @test
     */
    public function update_returns_an_ok_response()
    {
        $this->markTestIncomplete('This test case was generated by Shift. When you are ready, remove this line and complete this test case.');

        $response = $this->put(route($this->entityname.'.update', ['id' => $id]), [
            // TODO: send request data
        ]);

        $response->assertOk();

        // TODO: perform additional assertions
    }

    // test cases...
}
