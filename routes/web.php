<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\DashboardController;
use App\Models\Category;
use App\Models\User;
use Illuminate\Support\Facades\Route;

// Rota de Login
Route::controller(LoginController::class)->group(function () {
    Route::get('/', 'index')->name('login');
    Route::get('/login', 'index');
    Route::post('/login', 'login')->name('auth.login');
});

//Rotas Protegidas
Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::livewire('/categories', 'pages.categories')->middleware('can:viewAny,' . Category::class)->name('categories');
    Route::livewire('/caixa', 'pages.caixa')->name('caixa');
    Route::livewire('/products', 'pages.products')->name('products');
    Route::livewire('/users', 'pages.users')->middleware('can:viewAny,' . User::class)->name('users');
    Route::livewire('/sales', 'pages.sales')->name('sales');
    Route::livewire('/config', 'pages.config')->name('config');
});
