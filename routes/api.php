<?php

use App\Http\Controllers\Web_Api\BankTransferController;
use App\Http\Controllers\Web_Api\CouponController;
use App\Http\Controllers\Web_Api\PlanController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Web_Api\LandingPageController;
use App\Http\Controllers\Web_Api\PlanRequestController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

/*
|--------------------------------------------------------------------------
|                              WEB APIS
|--------------------------------------------------------------------------
*/

Route::resource('plans', PlanController::class);
Route::resource('landing_page_data', LandingPageController::class);
Route::resource('plan_request', PlanRequestController::class);
Route::resource('banktransfer', BankTransferController::class);
Route::post('banktransfer_request', [BankTransferController::class, 'banktransferstore']);
Route::get('/apply-coupon', [CouponController::class, 'applyCoupon']);