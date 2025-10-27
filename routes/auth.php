<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\ConfirmablePasswordController;
use App\Http\Controllers\Auth\EmailVerificationNotificationController;
use App\Http\Controllers\Auth\EmailVerificationPromptController;
use App\Http\Controllers\Auth\NewPasswordController;
use App\Http\Controllers\Auth\PasswordController;
use App\Http\Controllers\Auth\PasswordResetLinkController;
use App\Http\Controllers\Auth\RegisteredAdminController;
use App\Http\Controllers\Auth\RegisteredEmployeeController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\Auth\VerifyEmailController;
use App\Http\Controllers\QrLoginController;
use App\Http\Controllers\QrScannerController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::middleware(['guest','no-cache'])->group(function () {
    Route::get('register', [RegisteredUserController::class, 'create'])
        ->name('register');
    Route::get('register/admin', function () {
        return Inertia::render('Auth/RegisterAdmin');
    })->name('register.admin.get');
    Route::get('register/employee', function () {
        return Inertia::render('Auth/RegisterEmployee');
    })->name('register.employee.get');
    Route::post('register/admin', [RegisteredAdminController::class, 'store'])->name('register.admin.post');
    Route::post('register/employee', [RegisteredEmployeeController::class, 'store'])->name('register.employee.post');

    Route::get('login', [AuthenticatedSessionController::class, 'create'])
        ->name('login');

    Route::post('login', [AuthenticatedSessionController::class, 'store']);

    Route::get('forgot-password', [PasswordResetLinkController::class, 'create'])
        ->name('password.request');

    Route::post('forgot-password', [PasswordResetLinkController::class, 'store'])
        ->name('password.email');

    Route::get('reset-password/{token}', [NewPasswordController::class, 'create'])
        ->name('password.reset');

    Route::post('reset-password', [NewPasswordController::class, 'store'])
        ->name('password.store');
    Route::get('/login/qr-code/generate', function(){
        return Inertia::render('Auth/LoginWithQr');
    })->name('qr.generate');
    Route::get('/login/qr-code/{id}/status', [QrLoginController::class, 'status'])->name('qr.status');

});

Route::middleware(['auth','no-cache'])->group(function () {
    Route::post('/login/qr-code/scan', [QrLoginController::class, 'scan'])->name('qr.scan');
    Route::get('/login/scan-qr', QrScannerController::class)->name('qr.scanner');
    Route::get('verify-email', EmailVerificationPromptController::class)
        ->name('verification.notice');

    Route::get('verify-email/{id}/{hash}', VerifyEmailController::class)
        ->middleware(['signed', 'throttle:6,1'])
        ->name('verification.verify');

    Route::post('email/verification-notification', [EmailVerificationNotificationController::class, 'store'])
        ->middleware('throttle:6,1')
        ->name('verification.send');

    Route::get('confirm-password', [ConfirmablePasswordController::class, 'show'])
        ->name('password.confirm');

    Route::post('confirm-password', [ConfirmablePasswordController::class, 'store']);

    Route::put('password', [PasswordController::class, 'update'])->name('password.update');

    Route::post('logout', [AuthenticatedSessionController::class, 'destroy'])
        ->name('logout');
});
