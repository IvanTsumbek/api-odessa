<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\LinkedinController;
use App\Http\Controllers\LinkedinPostController;
use App\Http\Controllers\LinkedinPostStoreController;

Route::get('/', [HomeController::class, 'home'])->name('home');
Route::get('/linkedin/redirect', [LinkedinController::class, 'redirect'])->name('redirect');
Route::get('/linkedin/callback', [LinkedinController::class, 'callback'])->name('callback');
Route::get('/linkedin/post', [LinkedinPostController::class, 'create'])
    ->name('linkedin.post')
    ->middleware('auth');
Route::post('/linkedin/post', [LinkedinPostStoreController::class, 'store'])
    ->name('linkedin.post.store')
    ->middleware('auth');





Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__ . '/auth.php';
