<?php

namespace Tests;

use App\Models\Period;
use App\Models\Room;
use App\Models\Teacher;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Support\Facades\View;
use JMac\Testing\Traits\AdditionalAssertions;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication, AdditionalAssertions;


    protected function setSharedVariables()
    {
        View::share('periods', Period::orderBy('id', 'desc')->get());
        View::share('current_period', Period::get_default_period());

        View::share('teachers', Teacher::all());

        View::share('rooms', Room::all());
    }
}
