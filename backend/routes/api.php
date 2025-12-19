<?php

use App\Http\Controllers\Api\UserController;
use App\Models\Profile;
use Illuminate\Support\Facades\Route;

Route::apiResource('usuarios', UserController::class);

Route::get('perfis', function() {
    return Profile::all();
});
