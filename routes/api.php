<?php

use App\Http\Controllers\SettingsController;
use App\Http\Controllers\Web_Api\BankTransferController;
use App\Http\Controllers\Web_Api\CouponController;
use App\Http\Controllers\Web_Api\EmailverificationController;
use App\Http\Controllers\Web_Api\PlanController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Web_Api\LandingPageController;
use App\Http\Controllers\Web_Api\PlanRequestController;
use App\Http\Controllers\Web_Api\RegisterdCompanyController;
use App\Models\Company;

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

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

// Handle preflight OPTIONS request
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

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
Route::get('get_settings_for_api', [SettingsController::class, 'get_settings_for_api']);
Route::resource('email_verification', EmailverificationController::class);
Route::resource('register_company', RegisterdCompanyController::class);
Route::post('register_company_resend_email', [RegisterdCompanyController::class, 'register_company_resend_email']);
Route::post('verify_token', [RegisterdCompanyController::class, 'verify_token']);
Route::post('update_company_setup', [RegisterdCompanyController::class, 'update_company_setup']);


/*
|--------------------------------------------------------------------------
|                              APP APIS
|--------------------------------------------------------------------------
*/

Route::get('/check-company', function(Request $request) {
    $companyName = $request->input('company_name'); 
    // dd($companyName);
    $company = Company::whereRaw('lower(company_name) = ?', strtolower($companyName))->first();

    if ($company) {
        return response()->json(['status' => 200, 'message' => "Company found", 'url' => $company->url, 'company_logo' =>  $company->url . '/storage/uploads/logo/2_dark_logo.png'], 200);
    } else {
        return response()->json(['status' => 404, 'message' => "This Company name is not registered with us.", 'url' => null], 404);
    }
});
