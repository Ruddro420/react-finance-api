<?php

use App\Http\Controllers\Api\HomeContentController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

Route::prefix('v1')->group(function () {
    Route::get('homecontent', [HomeContentController::class, 'index']);
    Route::get('homecontent/{id}', [HomeContentController::class, 'show']);
    Route::post('homecontent', [HomeContentController::class, 'store']);
    Route::put('homecontent/{id}', [HomeContentController::class, 'update']);
    Route::delete('homecontent/{id}', [HomeContentController::class, 'destroy']);
});
