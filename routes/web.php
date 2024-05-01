<?php

use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\Backend\DashboardController;
use App\Http\Controllers\Backend\LocationController;
use App\Http\Controllers\Backend\MessageController;
use App\Http\Controllers\Backend\NotificationController;
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

Route::get('/log-file', function () {
    $file = File::get(storage_path() . '/logs/laravel.log');
    return "<div style='white-space: pre;'>$file</div>";
})->name('logFile');

Route::get('/clear-log-file', function () {
    $file = File::put(storage_path() . '/logs/laravel.log', '');
    return $file;
})->name('clearlogFile');

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

Route::middleware(['auth','PreventBackHistory', 'userinactive'])->group(function () {
    Route::prefix('admin')->group(function (){
        Route::get('profile', [UserController::class, 'showProfile'])->name('show.profile');
        Route::post('profile', [UserController::class, 'updateProfile'])->name('update.profile');

        Route::get('change-password', [UserController::class, 'showChangePassword'])->name('show.change.password');
        Route::post('change-password', [UserController::class, 'updateChangePassword'])->name('update.change.password');

        Route::resource('/client-admins',SubAdminController::class);
        Route::post('/client-admins/mass-destroy', [SubAdminController::class, 'massDestroy'])->name('client-admins.massDestroy');
        Route::post('/update-client-admin-status', [SubAdminController::class, 'statusUpdate'])->name('client-admins.statusUpdate');

        Route::get('settings', [SettingController::class, 'index'])->name('show.setting');
        Route::post('update-settings', [SettingController::class, 'update'])->name('update.setting');

        Route::get('settings/contact-details', [SettingController::class, 'showContactDetails'])->name('show.contact-detail');
        Route::post('settings/update-contact-details', [SettingController::class, 'updateContactDetails'])->name('update.contact-detail');
        Route::post('settings/subject/store', [SettingController::class, 'storeSubject'])->name('settings.subject.store');
        Route::post('settings/subject/delete', [SettingController::class, 'deleteSubject'])->name('settings.subject.delete');

        Route::get('/dashboard',[DashboardController::class,'index'])->name('dashboard');
        Route::get('/logout',[LoginController::class,'logout'])->name('logout');

        /* For Header Notification */
        Route::get('notifications', [DashboardController::class, 'notification'])->name('getNotification');
        Route::get('mark-as-read', [DashboardController::class, 'readNotification'])->name('read.notification');
        Route::get('mark-as-read-all', [DashboardController::class, 'readAllNotification'])->name('readall.notification');
        Route::get('clear-notification', [DashboardController::class, 'clearNotifications'])->name('clear.notification');
        Route::post('delete-notification', [DashboardController::class, 'deleteNotifications'])->name('delete.notification');


        Route::resource('/locations',LocationController::class);
        Route::post('/location/mass-destroy', [LocationController::class, 'massDestroy'])->name('locations.massDestroy');

        Route::resource('/occupations',OccupationController::class);
        Route::post('/occupations/massDestroy', [OccupationController::class, 'massDestroy'])->name('occupations.massDestroy');


        Route::resource('/staffs',StaffController::class);
        Route::post('/staffs/mass-destroy', [StaffController::class, 'massDestroy'])->name('staffs.massDestroy');
        Route::post('/staffs/update-status', [StaffController::class, 'updateStaffStatus'])->name('staffs.update.status');
        Route::get('/notifications-create', [StaffController::class, 'createNotification'])->name('staffs.createNotification');
        Route::post('/notifications-store', [StaffController::class, 'notificationStore'])->name('staffs.notificationStore');

        // Route::resource('/client-details',SubAdminDetailController::class, ['parameters'=>['client-details'=>'subAdminDetail']]);
        // Route::post('/client-details/mass-destroy', [SubAdminDetailController::class, 'massDestroy'])->name('client-details.massDestroy');

        Route::resource('/listed-businesses', SubAdminDetailController::class, ['parameters' => ['listed-businesses' => 'subAdminDetail'], 'names' => [
            'index' => 'client-details.index',
            'create' => 'client-details.create',
            'store' => 'client-details.store',
            'show' => 'client-details.show',
            'edit' => 'client-details.edit',
            'update' => 'client-details.update',
            'destroy' => 'client-details.destroy',
        ]]);
        Route::post('/listed-businesses/mass-destroy', [SubAdminDetailController::class, 'massDestroy'])->name('client-details.massDestroy');

        Route::resource('/shifts',ShiftController::class);
        Route::post('/shifts/mass-destroy', [ShiftController::class, 'massDestroy'])->name('shifts.massDestroy');
        Route::post('/shifts/cancel/{id}', [ShiftController::class, 'CancelShift'])->name('shifts.cancel');
        Route::post('/shifts/rating/{id}', [ShiftController::class, 'RateShift'])->name('shifts.rating');
        Route::get('/shifts-get-sub-admin-details', [ShiftController::class, 'getSubAdminData'])->name('shifts.get-sub-admin-details');
        Route::get('/shifts-clockin-clockout', [ShiftController::class, 'clockInAndClockOut'])->name('shifts.clockInAndClockOut');

        Route::resource('/messages',MessageController::class);
        Route::get('/get-group-list', [MessageController::class, 'getGroupList'])->name('messages.getGroupList');
        Route::get('/showChatScreen', [MessageController::class, 'showChatScreen'])->name('messages.showChatScreen');

        Route::post('/messages/send/{groupId}', [MessageController::class, 'sendMessage'])->name('messages.send');

        Route::post('/messages/mass-destroy', [MessageController::class, 'massDestroy'])->name('messages.massDestroy');
    });
});


