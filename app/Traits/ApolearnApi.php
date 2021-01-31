<?php


namespace App\Traits;


use App\Services\ApolearnService;

trait ApolearnApi
{

    /**
     * ApolearnApi constructor.
     */
    public function __construct()
    {
        if (! config('services.apolearn.sync_enabled')) {
            die();
        }

        $this->lms = new ApolearnService();
    }
}
