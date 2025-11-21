<?php

use App\Http\Controllers\AboutPageController;
use App\Http\Controllers\Api\HomeContentController;
use App\Http\Controllers\BlogController;
use App\Http\Controllers\ContactPageController;
use App\Http\Controllers\HomePageController;
use App\Http\Controllers\ProductApController;
use App\Http\Controllers\ProductArController;
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

// Route::prefix('v1')->group(function () {
//     Route::get('homecontent', [HomeContentController::class, 'index']);
//     Route::get('homecontent/{id}', [HomeContentController::class, 'show']);
//     Route::post('homecontent', [HomeContentController::class, 'store']);
//     Route::put('homecontent/{id}', [HomeContentController::class, 'update']);
//     Route::delete('homecontent/{id}', [HomeContentController::class, 'destroy']);
// });

Route::apiResource('contact', ContactPageController::class)->only([
    'index', 'store', 'update', 'show'
]);



Route::apiResource('homecontent', HomePageController::class)->only([
    'index', 'store', 'update', 'show'
]);


Route::apiResource('productap', ProductApController::class);


Route::apiResource('productar', ProductArController::class);


Route::apiResource('blogs', BlogController::class);

Route::apiResource('about', AboutPageController::class);

Route::get('/dashboarddata', [DashboardController::class, 'getDashboardData']);
