<?php

use App\Http\Controllers\QrLoginController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::post('/reset-passwd', [\App\Http\Controllers\Auth\NewPasswordController::class, 'reset']);
Route::get('/qr-code/generate', [QrLoginController::class, 'generate'])->name('qr.make');
