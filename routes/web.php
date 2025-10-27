<?php

use App\Http\Controllers\Auth\QrIdentityController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DepartementController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\ProjectRoleController;
use App\Http\Controllers\QrLoginController;
use App\Http\Controllers\WebPushController;
use App\Http\Controllers\WifiController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
})->name('welcome');

Route::middleware(['auth', 'verified', 'admin', 'no-cache'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::post('/departements', [DepartementController::class, 'store'])->name('departements.store');
    Route::put('/departements/{id}', [DepartementController::class, 'update'])->name('departements.update');
    Route::delete('/departements/{id}', [DepartementController::class, 'destroy'])->name('departements.destroy');
    Route::get('/employees/create/{id}', [EmployeeController::class, 'create'])->name('employees.create');
    Route::post('/employees/store/{id}', [EmployeeController::class, 'store'])->name('employees.store');
    Route::delete('/employees/{id}', [EmployeeController::class, 'destroy'])->name('employees.destroy');
    Route::put('/employees/{id}', [EmployeeController::class, 'update'])->name('employees.update');
    Route::patch('/refresh-token/{id}', [DepartementController::class, 'regenerateToken'])->name('departements.refreshToken');
    Route::resource('projects', ProjectController::class)->except(['index', 'show']);
    Route::patch('/projects/{project}/refresh-token', [ProjectController::class, 'refreshToken'])->name('projects.refreshToken');

    // Role Proyek (Nested store, direct update/destroy)
    Route::post('/projects/{project}/roles', [ProjectRoleController::class, 'store'])->name('projects.roles.store');
    Route::put('/project-roles/{projectRole}', [ProjectRoleController::class, 'update'])->name('projects.roles.update');
    Route::delete('/project-roles/{projectRole}', [ProjectRoleController::class, 'destroy'])->name('projects.roles.destroy');

    Route::get('/wifi', [WifiController::class, 'getWifi'])->name('wifi.get');
    Route::post('/wifi', [WifiController::class, 'store'])->name('wifi.create');
    Route::put('/wifi/{id}', [WifiController::class, 'update'])->name('wifi.update');
    Route::delete('/wifi/{id}', [WifiController::class, 'destroy'])->name('wifi.destroy');
});

Route::middleware(['auth', 'no-cache'])->group(function () {
    Route::post('/push/subscribe', [WebPushController::class, 'subscribe'])->name('push.subscribe');
    Route::post('/push/unsubscribe', [WebPushController::class, 'unsubscribe'])->name('push.unsubscribe');
    Route::post('/push/test', [WebPushController::class, 'test'])->name('push.test');
    Route::middleware(['ip-company'])->group(function () {
        Route::get('/identity/absen', [QrIdentityController::class, 'indexAbsen'])->name('identity.absen.scan');
        Route::post('/identity/absen', [QrIdentityController::class, 'absen'])->name('identity.qr.absen');
        Route::get('/identity/show', [QrIdentityController::class, 'index'])->name('identity.qr.show');
        Route::get('/identity', [QrIdentityController::class, 'getQr'])->name('identity.qr.get');
    });

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::delete('/session/{sessionId}', [ProfileController::class, 'destroySession'])->name('profile.session.destroy');
});

Route::get('/identity/{id}', [App\Http\Controllers\Auth\QrIdentityController::class, 'show'])->name('identity.qr');
require __DIR__ . '/auth.php';
