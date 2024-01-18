<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\ResetPasswordController;

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


/**
 * Auth routes
 */
Route::prefix('auth')->name('auth.')->namespace('Auth')->group(function () {
    Route::post('/register', [RegisterController::class, 'registerWithEmailAndPassword']);
    Route::post('/login', [LoginController::class, 'login']);

    // Reset password
    Route::prefix('password')->name('reset-password.')->group(function () {
        Route::post('/send-link', [ResetPasswordController::class, 'sendResetPasswordLink']);
        Route::post('/verify-code', [ResetPasswordController::class, 'verifyCode']);
        Route::post('/reset-password', [ResetPasswordController::class, 'resetPassword']);
    });
});
