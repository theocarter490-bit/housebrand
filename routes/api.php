<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\V1\AuthController;


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

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });

Route::controller(AuthController::class)->group(function () {
    Route::post('designerRegister', 'designerRegister');
    Route::post('userRegister', 'userRegister');
    Route::post('login', 'login');
    Route::post('registration', 'registration');
});

Route::prefix('user')->group(function () {
    Route::post('/forgot-password', [AuthController::class, 'sendPasswordResetToken']);
    Route::post('/reset-password-token', [AuthController::class, 'resetPassword']);
    // email with link
    Route::get('/resend-verify-email-link', [AuthController::class, 'sendVerificationMailLink'])->middleware('auth:sanctum');
    Route::get('/complete-verify-link', [AuthController::class, 'completeVerifyWithLink'])->middleware('auth:sanctum');
    // email with otp
    Route::get('/resend-verify-email-otp', [AuthController::class, 'sendVerificationMailOtp'])->middleware('auth:sanctum');
    Route::post('/complete-verify-otp', [AuthController::class, 'completeVerifyWithOTP'])->middleware('auth:sanctum', 'throttle:3,3600');

    Route::post('/verify-password', [AuthController::class, 'verifyPassword'])->middleware('auth:sanctum');
});
