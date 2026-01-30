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

    // Workspace routes
    Route::get('workspace/settings', [\App\Http\Controllers\WorkspaceController::class, 'settings'])->name('workspace.settings');
    Route::post('workspace/generate-invite', [\App\Http\Controllers\WorkspaceController::class, 'generateInviteCode'])->name('workspace.generate-invite');
    Route::get('workspace/join', [\App\Http\Controllers\WorkspaceController::class, 'joinForm'])->name('workspace.join');
    Route::post('workspace/join', [\App\Http\Controllers\WorkspaceController::class, 'join'])->name('workspace.join.submit');
    Route::post('workspace/switch/{teamId}', [\App\Http\Controllers\WorkspaceController::class, 'switchWorkspace'])->name('workspace.switch');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
