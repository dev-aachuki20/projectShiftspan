<?php

use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\Backend\DashboardController;
use App\Http\Controllers\Backend\LocationController;
use App\Http\Controllers\Backend\OccupationController;
use App\Http\Controllers\Backend\ShiftController;
use App\Http\Controllers\Backend\UserController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// Authentication Routes
Route::group(['middleware' => 'guest'], function () {
    Route::controller(LoginController::class)->group(function(){

        Route::get('/', function () {
            return redirect()->route('login');
        });

        Route::get('/admin/login', 'index')->name('login');
        Route::post('/admin/login','login')->name('authenticate');
    });

    // Route::controller(ForgotPasswordController::class)->group(function(){
    //     Route::get('/forgot-password', 'index')->name('forgot.password');
    //     Route::post('/forgot-pass-mail','sendResetLinkEmail')->name('password_mail_link');
    // });

    // Route::controller(ResetPasswordController::class)->group(function(){
    //     Route::get('reset-password', 'showform')->name('resetPassword');
    //     Route::post('/reset-password','resetpass')->name('reset-new-password');
    // });
});

Route::middleware(['auth','PreventBackHistory'])->group(function () {
    Route::prefix('admin')->group(function (){
        Route::get('profile', [UserController::class, 'showProfile'])->name('show.profile');
        Route::post('profile', [UserController::class, 'updateProfile'])->name('update.profile');

        Route::get('change-password', [UserController::class, 'showChangePassword'])->name('show.change.password');
        Route::post('change-password', [UserController::class, 'updateChangePassword'])->name('update.change.password');

        Route::get('/dashboard',[DashboardController::class,'index'])->name('dashboard');
        Route::get('/logout',[LoginController::class,'logout'])->name('logout');

        Route::resource('/locations',LocationController::class);
        Route::post('/location/mass-destroy', [LocationController::class, 'massDestroy'])->name('locations.massDestroy');

        Route::resource('/occupations',OccupationController::class);
        Route::post('/multiple-occupations-delete', [OccupationController::class, 'deleteMultipleOccupation'])->name('getMultipleOccupationToDelete');

        Route::resource('/shifts',ShiftController::class);
    });
});


