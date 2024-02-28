<?php

use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\Backend\DashboardController;
use App\Http\Controllers\Backend\LocationController;
use App\Http\Controllers\Backend\OccupationController;
use App\Http\Controllers\Backend\SettingController;
use App\Http\Controllers\Backend\ShiftController;
use App\Http\Controllers\Backend\StaffController;
use App\Http\Controllers\Backend\UserController;
use App\Http\Controllers\Backend\SubAdminController;
use App\Http\Controllers\Backend\SubAdminDetailController;

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

        Route::resource('/client-admins',SubAdminController::class);
        Route::post('/client-admins/mass-destroy', [SubAdminController::class, 'massDestroy'])->name('client-admins.massDestroy');

        Route::get('settings', [SettingController::class, 'index'])->name('show.setting');
        Route::post('update-settings', [SettingController::class, 'update'])->name('update.setting');

        Route::get('settings/contact-details', [SettingController::class, 'showContactDetails'])->name('show.contact-detail');
        Route::post('settings/update-contact-details', [SettingController::class, 'updateContactDetails'])->name('update.contact-detail');

        Route::get('/dashboard',[DashboardController::class,'index'])->name('dashboard');
        Route::get('/logout',[LoginController::class,'logout'])->name('logout');

        Route::resource('/locations',LocationController::class);
        Route::post('/location/mass-destroy', [LocationController::class, 'massDestroy'])->name('locations.massDestroy');

        Route::resource('/occupations',OccupationController::class);
        Route::post('/occupations/massDestroy', [OccupationController::class, 'massDestroy'])->name('occupations.massDestroy');

        
        Route::resource('/staffs',StaffController::class);
        Route::post('/staffs/mass-destroy', [StaffController::class, 'massDestroy'])->name('staffs.massDestroy');
        Route::post('/staffs/update-status', [StaffController::class, 'updateStaffStatus'])->name('staffs.update.status');
        
        Route::resource('/client-details',SubAdminDetailController::class, ['parameters'=>['client-details'=>'subAdminDetail']]);
        Route::post('/client-details/mass-destroy', [SubAdminDetailController::class, 'massDestroy'])->name('client-details.massDestroy');
        
        Route::resource('/shifts',ShiftController::class);
        Route::post('/shifts/mass-destroy', [ShiftController::class, 'massDestroy'])->name('shifts.massDestroy');
        Route::post('/shifts/cancel/{id}', [ShiftController::class, 'CancelShift'])->name('shifts.cancel');
        Route::post('/shifts/rating/{id}', [ShiftController::class, 'RateShift'])->name('shifts.rating');
        Route::get('/shifts-get-sub-admin-details', [ShiftController::class, 'getSubAdminData'])->name('shifts.get-sub-admin-details');
    });
});


