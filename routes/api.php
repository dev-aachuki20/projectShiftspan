<?php

use App\Http\Controllers\Api\HomeController;
use App\Http\Controllers\Api\Auth\LoginController;
use App\Http\Controllers\Api\Auth\RegisterController;
use App\Http\Controllers\Api\NotificationController;
use App\Http\Controllers\Api\MessageController;
use App\Http\Controllers\Api\ShiftController;
use App\Http\Controllers\Api\UserController;
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
    | Route 		: http://localhost:8000/api/new_register
    | Parameter 	: Multiple
    | Method 		: Post
    |
    */
	Route::post('new_register',[RegisterController::class,'create']);


	/*
    |--------------------------------------------------------------------------
    | Open API Routes
    |--------------------------------------------------------------------------
    | Method        : Get
    |
    */
    Route::get('companies', [HomeController::class,'companyList']);


	/*
    |--------------------------------------------------------------------------
    | Open API Routes
    |--------------------------------------------------------------------------
    | Method        : Post
    | Validate company_number with company
    |
    */
    Route::post('company', [HomeController::class,'companyNumberExist']);

     /*
    |--------------------------------------------------------------------------
    |  Get Occupations API Routes
    |--------------------------------------------------------------------------
    |
    | Route         : http://localhost:8000/api/occupations
    | Method        : Get
    | Parameters    : company_id
    |
    */
    Route::get('occupations', [HomeController::class, 'occupationsList']);


});


/*
| Auth Common Api List
|--------------------------------------------------------------------------
| Base Route : http://localhost:8000/api/
|--------------------------------------------------------------------------
|
*/
Route::group(['namespace' => 'Api', 'middleware' => ['auth:sanctum', 'checkUserStatus', 'check.device']],function (){
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


    /*
    |--------------------------------------------------------------------------
    |  Available Shifts API Routes
    |--------------------------------------------------------------------------
    | 
    | Route         : http://localhost:8000/api/available-shifts
    | Header        : Content-Type:application/json
    |               : Authorization : Token
    | Method        : GET
    | 
    | Parameters    : Query parater meter for filter: location, occupation
    */
    Route::get('available-shifts', [ShiftController::class,'availableShifts']);

    /*
    |--------------------------------------------------------------------------
    |  Completed Shifts API Routes
    |--------------------------------------------------------------------------
    | 
    | Route         : http://localhost:8000/api/completed-shifts
    | Header        : Content-Type:application/json
    |               : Authorization : Token
    | Method        : GET
    | 
    */
    Route::get('completed-shifts', [ShiftController::class,'completedShifts']);


    /*
    |--------------------------------------------------------------------------
    |  Upcoming Shifts API Routes
    |--------------------------------------------------------------------------
    | 
    | Route         : http://localhost:8000/api/upcoming-shifts
    | Header        : Content-Type:application/json
    |               : Authorization : Token
    | Method        : GET
    | 
    */
    Route::get('upcoming-shifts', [ShiftController::class,'upcomingShifts']);

    /*
    |--------------------------------------------------------------------------
    |  Pick Shift API Routes
    |--------------------------------------------------------------------------
    | 
    | Route         : http://localhost:8000/api/pick-shift
    | Header        : Content-Type:application/json
    |               : Authorization : Token
    | Method        : POST
    | 
    | Parameters    : id
    */
    Route::post('pick-shift', [ShiftController::class,'pickShift']);


    /*
    |--------------------------------------------------------------------------
    |  Clock In Shift API Routes
    |--------------------------------------------------------------------------
    | 
    | Route         : http://localhost:8000/api/clock-in-shift
    | Header        : Content-Type:application/json
    |               : Authorization : Token
    | Method        : POST
    | 
    | Parameters    : id, latitude, longitude
    */
    Route::post('clock-in-shift', [ShiftController::class,'clockInShift']);

    /*
    |--------------------------------------------------------------------------
    |  Clock Out Shift API Routes
    |--------------------------------------------------------------------------
    | 
    | Route         : http://localhost:8000/api/clock-out-shift
    | Header        : Content-Type:application/json
    |               : Authorization : Token
    | Method        : POST
    | 
    | Parameters    : id, latitude, longitude
    */
    Route::post('clock-out-shift', [ShiftController::class,'clockOutShift']);

    /*
    |--------------------------------------------------------------------------
    |  Cancel Shift API Routes
    |--------------------------------------------------------------------------
    | 
    | Route         : http://localhost:8000/api/cancel-shift
    | Header        : Content-Type:application/json
    |               : Authorization : Token
    | Method        : POST
    | 
    | Parameters    : id, latitude, longitude
    */
    Route::post('cancel-shift', [ShiftController::class,'cancelShift']);


    /*
    |--------------------------------------------------------------------------
    |  Authorized Sign Shift API Routes
    |--------------------------------------------------------------------------
    | 
    | Route         : http://localhost:8000/api/authrized-sign-shift
    | Header        : Content-Type:application/json
    |               : Authorization : Token
    | Method        : POST
    | 
    | Parameters    : id, full_name, signature
    */
    Route::post('authrized-sign-shift', [ShiftController::class,'authorizedSign']);
    /*
    |--------------------------------------------------------------------------
    |  Authorized Sign Shift API Routes
    |--------------------------------------------------------------------------
    | 
    | Route         : http://localhost:8000/api/authrized-sign-shift
    | Header        : Content-Type:application/json
    |               : Authorization : Token
    | Method        : POST
    | 
    | Parameters    : id, full_name, signature
    */
    Route::get('calendar', [ShiftController::class,'shiftsForCurrentAndPreviousMonth']);

    /*
    |--------------------------------------------------------------------------
    |  Completed Notification API Routes
    |--------------------------------------------------------------------------
    | 
    | Route         : http://localhost:8000/api/announcements
    | Header        : Content-Type:application/json
    |               : Authorization : Token
    | Method        : GET
    | 
    */
    Route::get('announcements', [NotificationController::class,'getAnnouncements']);
    
    /*
    |--------------------------------------------------------------------------
    |  Completed Notification API Routes
    |--------------------------------------------------------------------------
    | 
    | Route         : http://localhost:8000/api/help-chats
    | Header        : Content-Type:application/json
    |               : Authorization : Token
    | Method        : GET
    | 
    */
    Route::get('help-chats', [NotificationController::class,'helpChats']);
    
    /*
    |--------------------------------------------------------------------------
    |  Completed Notification API Routes
    |--------------------------------------------------------------------------
    | 
    | Route         : http://localhost:8000/api/help-chat-store
    | Header        : Content-Type:application/json
    |               : Authorization : Token
    | Method        : POST
    | 
    */
    Route::post('help-chat-store', [NotificationController::class,'helpChatNotification']);


    /*
    |--------------------------------------------------------------------------
    |  Mark As Read Notification API Routes
    |--------------------------------------------------------------------------
    | 
    | Route         : http://localhost:8000/api/mark-as-read
    | Header        : Content-Type:application/json
    |               : Authorization : Token
    | Method        : PUT
    | 
    */
    Route::put('/notifications/{uuid}/mark-as-read', [NotificationController::class,'makeAsRead']);

    /*
    |--------------------------------------------------------------------------
    |  Delete Notification API Routes
    |--------------------------------------------------------------------------
    | 
    | Route         : http://localhost:8000/api/notifications/{uuid}
    | Header        : Content-Type:application/json
    |               : Authorization : Token
    | Method        : DELETE
    | 
    */
    Route::delete('/notifications/{uuid}', [NotificationController::class,'deleteNotifications']);

    /*
    |--------------------------------------------------------------------------
    |  Clear All Notification API Routes
    |--------------------------------------------------------------------------
    | 
    | Route         : http://localhost:8000/api/clear-notifications
    | Header        : Content-Type:application/json
    |               : Authorization : Token
    | Method        : DELETE
    | 
    */
    Route::delete('/clear-notifications', [NotificationController::class,'clearNotifications']);


    /*
    |--------------------------------------------------------------------------
    |  Get Group Subject API Routes
    |--------------------------------------------------------------------------
    | 
    | Route         : http://localhost:8000/api/get-group-subjects
    | Header        : Content-Type:application/json
    |               : Authorization : Token
    | Method        : GET
    | 
    */
    Route::get('/get-group-subjects', [MessageController::class,'getGroupSubjects']);


    /*
    |--------------------------------------------------------------------------
    |  Send Group Message Routes
    |--------------------------------------------------------------------------
    | 
    | Route         : http://localhost:8000/api/sendGroupMessage
    | Header        : Content-Type:application/json
    |               : Authorization : Token
    | Method        : Post
    | 
    */
    Route::post('/sendGroupMessage', [MessageController::class,'storeGroupMessage']);


    /*
    |--------------------------------------------------------------------------
    |  Send Group Message Routes
    |--------------------------------------------------------------------------
    | 
    | Route         : http://localhost:8000/api/getGroupList
    | Header        : Content-Type:application/json
    |               : Authorization : Token
    | Method        : GET
    | 
    */
    Route::get('/getGroupList', [MessageController::class,'getGroupList']);

    /*
    |--------------------------------------------------------------------------
    |  Send Group Message Routes
    |--------------------------------------------------------------------------
    | 
    | Route         : http://localhost:8000/api/get-all-messages
    | Header        : Content-Type:application/json
    |               : Authorization : Token
    | Method        : GET
    | 
    */
    Route::get('/get-all-messages/{groupId}', [MessageController::class,'getAllMessages']);

    /*
    |--------------------------------------------------------------------------
    |  Send Message Routes
    |--------------------------------------------------------------------------
    | 
    | Route         : http://localhost:8000/api/send-message
    | Header        : Content-Type:application/json
    |               : Authorization : Token
    | Method        : GET
    | 
    */
    Route::post('/send-message/{groupId}', [MessageController::class,'sendMessage']);


    Route::post('delete-account', [UserController::class,'deleteUserAccount']);
    
});
