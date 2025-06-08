<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\ReportController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Models\Transaction;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    // Profile
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Categories (soft delete, restore, force delete)
    Route::get('/categories/trashed', [CategoryController::class, 'trashed'])->name('categories.trashed');
    Route::post('/categories/{id}/restore', [CategoryController::class, 'restore'])->name('categories.restore');
    Route::delete('/categories/{id}/force-delete', [CategoryController::class, 'forceDelete'])->name('categories.forceDelete');
    Route::delete('/categories/{id}/force-delete-with-transactions', [CategoryController::class, 'forceDeleteWithTransactions'])->name('categories.forceDeleteWithTransactions');

    // Resource routes
    Route::resource('categories', CategoryController::class);
    Route::resource('transactions', TransactionController::class);

    // Reports
    Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');

    // API endpoint for modal transaction listing
    Route::get('/api/categories/{id}/transactions', function ($id) {
        return Transaction::where('category_id', $id)
            ->where('user_id', Auth::id())
            ->select('id', 'amount', 'currency', 'description', 'date')
            ->orderBy('date', 'desc')
            ->get();
    });
});

require __DIR__.'/auth.php';
