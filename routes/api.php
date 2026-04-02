<?php

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;

// Mock Ecuasolutions endpoints for local testing only
if (app()->environment('local', 'testing')) {
    Route::get('/mock/ecuasolutions/ping', function () {
        return response()->json(['status' => 'online']);
    })->name('mock.ecuasolutions.ping');

    Route::post('/mock/ecuasolutions', function () {
        $data = request()->all();
        Log::info('[MockEcuasolutions] Received invoice', ['numtrans' => $data['numtrans'] ?? null]);

        return response()->json([
            'mensaje' => 'MOCK-'.str_pad($data['numtrans'] ?? rand(1000, 9999), 6, '0', STR_PAD_LEFT),
        ]);
    })->name('mock.ecuasolutions');
}
