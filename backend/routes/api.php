<?php

use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\ProfileController;
use Illuminate\Support\Facades\Route;

Route::apiResource('usuarios', UserController::class);

Route::apiResource('perfis', ProfileController::class);
