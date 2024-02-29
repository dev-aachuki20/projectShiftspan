<?php

use App\Http\Controllers\Api\HomeController;
use App\Http\Controllers\Api\Auth\LoginController;
use App\Http\Controllers\Api\Auth\RegisterController;
use App\Http\Controllers\Api\Auth\LogoutController;
use App\Http\Controllers\Api\SettingController;
use App\Http\Controllers\Api\UserController;
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

Route::group(['namespace' => 'Api'], function () {
    /*
    |--------------------------------------------------------------------------
    | Login API Routes
    |--------------------------------------------------------------------------
    |
    | Route 		: http://localhost:8000/api/login
    | Parameter 	: Email, Password
    | Method 		: Post
    |
    */
	Route::post('login',[LoginController::class,'login']);

    /*
    |--------------------------------------------------------------------------
    | Forgot Password API Routes
    |--------------------------------------------------------------------------
    |
    | Route 		: http://localhost:8000/api/forgot-password
    | Parameter 	: Email
    | Method 		: Post
    |
    */
	Route::post('forgot-password',[LoginController::class,'forgotPassword']);

    /*
    |--------------------------------------------------------------------------
    | Password Verify OTP API Routes
    |--------------------------------------------------------------------------
    |
    | Route 		: http://localhost:8000/api/password/verify-otp
    | Parameter 	: Email, otp
    | Method 		: Post
    |
    */
	Route::post('password/verify-otp',[LoginController::class,'verifyOtp']);


    /*
    |--------------------------------------------------------------------------
    | Reset Password API Routes
    |--------------------------------------------------------------------------
    |
    | Route 		: http://localhost:8000/api/password/reset-password
    | Parameter 	: Email
    | Method 		: Post
    |
    */
	Route::post('password/reset-password',[LoginController::class,'resetPassword']);


    /*
    |--------------------------------------------------------------------------
    | Register API Routes
    |--------------------------------------------------------------------------
    |
    | Route 		: http://localhost:8000/api/register
    | Parameter 	: Multiple
    | Method 		: Post
    |
    */
	Route::post('register',[RegisterController::class,'create']);


	/*
    |--------------------------------------------------------------------------
    | Open API Routes
    |--------------------------------------------------------------------------
    | Method        : Get
    |
    */
    Route::get('companies', [HomeController::class,'companyList']);

});


/*
| Auth Common Api List
|--------------------------------------------------------------------------
| Base Route : http://localhost:8000/api/
|--------------------------------------------------------------------------
|
*/
Route::group(['namespace' => 'Api', 'middleware' => ['auth:sanctum']],function (){
    /*
    |--------------------------------------------------------------------------
    |  Logout API Routes
    |--------------------------------------------------------------------------
    |
    | Route         : http://localhost:8000/api/logout
    | Header        : Content-Type:application/json
    |               : Authorization : Token
    | Method        : Get
    |
    */
    Route::post('logout', [HomeController::class,'logout']);

    /*
    |--------------------------------------------------------------------------
    |  Get Occupations API Routes
    |--------------------------------------------------------------------------
    |
    | Route         : http://localhost:8000/api/occupations
    | Header        : Content-Type:application/json
    |               : Authorization : Token
    | Method        : Get
    | Parameters    : company_id
    |
    */
    Route::get('occupations', [HomeController::class, 'occupationsList']);

    /*
    |--------------------------------------------------------------------------
    |  Get Locations API Routes
    |--------------------------------------------------------------------------
    |
    | Route         : http://localhost:8000/api/locations
    | Header        : Content-Type:application/json
    |               : Authorization : Token
    | Method        : Get
    | Parameters    : company_id
    |
    */
    Route::get('locations', [HomeController::class, 'locationsList']);


    /*
    |--------------------------------------------------------------------------
    |  Auth User Details API Routes
    |--------------------------------------------------------------------------
    |
    | Route         : http://localhost:8000/api/profile
    | Header        : Content-Type:application/json
    |               : Authorization : Token
    | Method        : Get
    |
    */
    Route::get('profile', [UserController::class,'profile']);


    /*
    |--------------------------------------------------------------------------
    |  Profile Update API Routes
    |--------------------------------------------------------------------------
    |
    | Route         : http://localhost:8000/api/profile
    | Header        : Content-Type:application/json
    |               : Authorization : Token
    | Method        : post
    |
    | Parameters    : name, address, phone, occupation_id
    |
    */
    Route::post('profile', [UserController::class,'updateProfile']);



    /*
    |--------------------------------------------------------------------------
    |  Setting API Routes
    |--------------------------------------------------------------------------
    |
    | Route         : http://localhost:8000/api/settings
    | Header        : Content-Type:application/json
    |               : Authorization : Token
    | Method        : GET    |
    |
    */
    Route::get('settings', [HomeController::class,'setting']);
});
