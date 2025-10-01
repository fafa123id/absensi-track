<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::post('/reset-passwd', [\App\Http\Controllers\Auth\NewPasswordController::class, 'reset']);
