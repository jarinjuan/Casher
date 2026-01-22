<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::get('/analysis', function () {
    return view('analysis');
})->middleware(['auth', 'verified'])->name('analysis');


Route::middleware('auth')->group(function () {
    
    Route::resource('transactions', \App\Http\Controllers\TransactionController::class);
    Route::resource('categories', \App\Http\Controllers\CategoryController::class);
    Route::get('charts', [\App\Http\Controllers\ChartController::class, 'index'])->name('charts.index');
    Route::get('currency/convert', [\App\Http\Controllers\CurrencyController::class, 'convert'])->name('currency.convert');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
