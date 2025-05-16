<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\SensorDataController;
use App\Http\Controllers\ReportController;
use Illuminate\Support\Facades\File;
/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('/sensor-data', [SensorDataController::class, 'getData']);
Route::get('/reports', [ReportController::class, 'getReports']);
Route::get('/reports/latest', [ReportController::class, 'getLatestReport']);
Route::get('/get-reports-json', function () {
    $path = storage_path('app/reports.json');
    if (!File::exists($path)) {
        return response()->json(['error' => 'File not found'], 404);
    }
    $content = File::get($path);
    return response($content)
        ->header('Content-Type', 'application/json')
        ->header('Access-Control-Allow-Origin', '*')
        ->header('Access-Control-Allow-Methods', 'GET');
});
