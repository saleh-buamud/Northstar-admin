<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request): ?\Illuminate\Contracts\Auth\Authenticatable {
    return $request->user();
})->middleware('auth:sanctum');