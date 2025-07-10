<?php
declare(strict_types=1);

use App\Http\Controllers\TestClickHouseController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::prefix('clickhouse')->group(function () {
    Route::get('test-connection', [TestClickHouseController::class, 'testConnection']);
    Route::get('test-data', [TestClickHouseController::class, 'getTestData']);
});
