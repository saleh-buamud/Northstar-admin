<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\LocaleController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\SaleController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::middleware('guest')->group(function (): void {
    Route::get('/login', [AuthController::class, 'create'])->name('login');
    Route::post('/login', [AuthController::class, 'store'])->middleware('throttle:5,1');
    Route::get('/register', [AuthController::class, 'register'])->name('register');
    Route::post('/register', [AuthController::class, 'storeFirstAdmin'])->middleware('throttle:5,1');
});

Route::post('/logout', [AuthController::class, 'destroy'])->middleware('auth')->name('logout');
Route::post('/locale', [LocaleController::class, 'update'])->name('locale.update');

Route::middleware('auth')->group(function (): void {
    Route::get('/', DashboardController::class)->middleware('can:dashboard.view')->name('dashboard');
    Route::resource('products', ProductController::class)
        ->middlewareFor('index', 'can:products.view')->middlewareFor('show', 'can:products.view')
        ->middlewareFor('create', 'can:products.create')->middlewareFor('store', 'can:products.create')
        ->middlewareFor('edit', 'can:products.edit')->middlewareFor('update', 'can:products.edit')
        ->middlewareFor('destroy', 'can:products.delete');
    Route::resource('categories', CategoryController::class)->except(['show'])
        ->middlewareFor('index', 'can:products.view')
        ->middlewareFor('create', 'can:products.create')->middlewareFor('store', 'can:products.create')
        ->middlewareFor('edit', 'can:products.edit')->middlewareFor('update', 'can:products.edit')
        ->middlewareFor('destroy', 'can:products.delete');
    Route::resource('users', UserController::class)
        ->middlewareFor('index', 'can:users.view')->middlewareFor('show', 'can:users.view')
        ->middlewareFor('create', 'can:users.create')->middlewareFor('store', 'can:users.create')
        ->middlewareFor('edit', 'can:users.edit')->middlewareFor('update', 'can:users.edit')
        ->middlewareFor('destroy', 'can:users.delete');
    Route::resource('roles', RoleController::class)->except(['show'])
        ->middlewareFor('index', 'can:roles.manage')->middlewareFor('create', 'can:roles.manage')
        ->middlewareFor('store', 'can:roles.manage')->middlewareFor('edit', 'can:roles.manage')
        ->middlewareFor('update', 'can:roles.manage')->middlewareFor('destroy', 'can:roles.manage');
    Route::post('/permissions', [PermissionController::class, 'store'])->middleware('can:permissions.manage')->name('permissions.store');
    Route::delete('/permissions/{permission}', [PermissionController::class, 'destroy'])->middleware('can:permissions.manage')->name('permissions.destroy');
    Route::post('/sales', [SaleController::class, 'store'])->middleware('can:products.edit')->name('sales.store');
});

Route::redirect('/home', '/');
