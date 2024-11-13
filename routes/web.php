<?php

use App\Http\Controllers\ProfileController;
use App\Jobs\SampleJob;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\JobDashboardController;


Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::get('/test-background-job', function () {
    runBackgroundJob('App\Jobs\SampleJob', 'execute', ['Hello, Background Job!']);
    return 'Background job dispatched!';
});

Route::get('/dashboard', [JobDashboardController::class, 'index'])->middleware(['auth', 'verified'])->name('dashboard');
Route::post('/jobs/dispatch', [JobDashboardController::class, 'dispatchJob'])->middleware(['auth', 'verified'])->name('jobs.dispatch');
Route::post('/jobs/cancel/{id}', [JobDashboardController::class, 'cancelJob'])->middleware(['auth', 'verified'])->name('jobs.cancel');

require __DIR__.'/auth.php';
