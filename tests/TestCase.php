<?php

namespace Tests;

use App\Models\Tenant;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        // Tests must not depend on a frontend build (public/build/manifest.json)
        $this->withoutVite();
    }

    /**
     * The migrations register the test database as the tenant of a single-tenant install, as on a real one.
     * Tests that refresh the database run with that tenant current.
     *
     * @return array<class-string, class-string>
     */
    protected function setUpTraits()
    {
        $uses = parent::setUpTraits();

        if (isset($uses[RefreshDatabase::class])) {
            Tenant::default()?->makeCurrent();
        }

        return $uses;
    }
}
