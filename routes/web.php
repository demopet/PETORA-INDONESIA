<?php

declare(strict_types=1);

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/.well-known/petora-health', function () {
    $status = 'ok';
    $checks = ['app' => true];

    try {
        DB::connection()->getPdo();
        $checks['database'] = true;
    } catch (Throwable) {
        $checks['database'] = false;
        $status = 'degraded';
    }

    return response()->json([
        'status' => $status,
        'checks' => $checks,
        'timestamp' => now()->toIso8601String(),
    ], $status === 'ok' ? 200 : 503);
})->name('health');
