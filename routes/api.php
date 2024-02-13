<?php

use App\Http\Controllers\Api\Auth\LoginController;
use App\Http\Controllers\Api\Auth\LogoutController;
use App\Http\Controllers\Api\OccupationController;
use App\Http\Controllers\Api\SettingController;
use App\Http\Controllers\Api\StaffController;
use App\Http\Controllers\Api\SubAdminController;
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

Route::controller(LoginController::class)->group(function(){
    Route::post('login', 'login');
    Route::post('create-account', 'registerUser');
    Route::post('forgot-password', 'forgotPassword');
    Route::post('password/verify-otp', 'verifyOtp');
    Route::post('password/reset-password', 'resetPassword');
});

Route::middleware(['auth:sanctum'])->group(function () {
    Route::post('/logout', [LogoutController::class, 'logout']);

    Route::get('/all-sub-admin', [SubAdminController::class, 'AllSubAdmins']);
    Route::get('/all-occupations', [OccupationController::class, 'AllOccupations']);

    Route::post('user/profile/{user}', [StaffController::class, 'updateProfile']);
    Route::get('/setting-doc', [SettingController::class, 'getPolicyDoc']);
});
