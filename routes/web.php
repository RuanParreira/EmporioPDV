<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\CategoriesController;
use App\Http\Controllers\DashboardController;
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
    Route::livewire('/categories', 'pages.categories')->name('categories');
});
