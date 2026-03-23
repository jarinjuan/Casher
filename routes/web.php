<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/lang/{locale}', function ($locale) {
    if (in_array($locale, ['en', 'cs'])) {
        session()->put('locale', $locale);
    }
    return redirect()->back();
})->name('lang.switch');

Route::get('/dashboard', [\App\Http\Controllers\DashboardController::class, 'index'])->middleware(['auth', 'verified'])->name('dashboard');

Route::get('/analysis', function () {
    return view('analysis');
})->middleware(['auth', 'verified'])->name('analysis');


Route::middleware('auth')->group(function () {
    Route::get('dashboard/live-balance', [\App\Http\Controllers\DashboardController::class, 'liveBalance'])->name('dashboard.live-balance');
    
    Route::resource('transactions', \App\Http\Controllers\TransactionController::class);
    Route::resource('categories', \App\Http\Controllers\CategoryController::class);
    Route::get('charts', [\App\Http\Controllers\ChartController::class, 'index'])->name('charts.index');
    Route::get('investments', [\App\Http\Controllers\InvestmentController::class, 'index'])->name('investments.index');
    Route::get('investments/search', [\App\Http\Controllers\InvestmentController::class, 'search'])->name('investments.search');
    Route::get('investments/live-prices', [\App\Http\Controllers\InvestmentController::class, 'livePrices'])->name('investments.live-prices');
    Route::get('investments/{investment}/edit', [\App\Http\Controllers\InvestmentController::class, 'edit'])->name('investments.edit');
    Route::post('investments', [\App\Http\Controllers\InvestmentController::class, 'store'])->name('investments.store');
    Route::put('investments/{investment}', [\App\Http\Controllers\InvestmentController::class, 'update'])->name('investments.update');
    Route::delete('investments/{investment}', [\App\Http\Controllers\InvestmentController::class, 'destroy'])->name('investments.destroy');
    Route::post('investments/refresh', [\App\Http\Controllers\InvestmentController::class, 'refresh'])->name('investments.refresh');
    Route::get('currency/convert', [\App\Http\Controllers\CurrencyController::class, 'convert'])->name('currency.convert');

    // Data Import/Export routes
    Route::get('data', [\App\Http\Controllers\DataController::class, 'index'])->name('data.index');
    Route::post('data/export', [\App\Http\Controllers\DataController::class, 'export'])->name('data.export');
    Route::post('data/import', [\App\Http\Controllers\DataController::class, 'import'])->name('data.import');

    // Workspace routes
    Route::get('workspace/settings', [\App\Http\Controllers\WorkspaceController::class, 'settings'])->name('workspace.settings');
    Route::post('workspace/generate-invite', [\App\Http\Controllers\WorkspaceController::class, 'generateInviteCode'])->name('workspace.generate-invite');
    Route::put('workspace/currency', [\App\Http\Controllers\WorkspaceController::class, 'updateCurrency'])->name('workspace.update-currency');
    Route::get('workspace/join', [\App\Http\Controllers\WorkspaceController::class, 'joinForm'])->name('workspace.join');
    Route::post('workspace/join', [\App\Http\Controllers\WorkspaceController::class, 'join'])->name('workspace.join.submit');
    Route::post('workspace/switch/{teamId}', [\App\Http\Controllers\WorkspaceController::class, 'switchWorkspace'])->name('workspace.switch');
    Route::delete('workspace/{team}/members/{user}', [\App\Http\Controllers\WorkspaceController::class, 'removeMember'])->name('workspace.remove-member');
    Route::delete('workspace/{team}/leave', [\App\Http\Controllers\WorkspaceController::class, 'leaveWorkspace'])->name('workspace.leave');
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
