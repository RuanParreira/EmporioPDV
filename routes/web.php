<?php

use App\Http\Controllers\DashboardController;
use App\Models\Category;
use App\Models\Enterprise;
use App\Models\Sale;
use App\Models\User;
use Illuminate\Support\Facades\Route;

// Rota de Login
Route::livewire('/', 'auth.login')->name('login');

//Rotas Protegidas
Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::livewire('/categories', 'pages.categories')->middleware('can:viewAny,' . Category::class)->name('categories');
    Route::livewire('/caixa', 'pages.caixa')->name('caixa');
    Route::livewire('/products', 'pages.products')->name('products');
    Route::livewire('/users', 'pages.users')->middleware('can:viewAny,' . User::class)->name('users');
    Route::livewire('/sales', 'pages.sales')->name('sales');
    Route::livewire('/config', 'pages.config')->middleware('can:manageConfig,' . Enterprise::class)->name('config');
    Route::get('/imprimir-recibo/{sale}', function (Sale $sale) {
        $sale->load('items');
        return view('print.receipt', compact('sale'));
    })->name('print.receipt')->middleware('can:view,sale')->name('imprimir-recibo');
});
