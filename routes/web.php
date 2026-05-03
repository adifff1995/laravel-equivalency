<?php

use App\Http\Controllers\Admin\RequestController as AdminRequestController;
use App\Http\Controllers\Academic\RequestController as AcademicRequestController;
use App\Http\Controllers\PublicRequestController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    if (auth()->check()) {
        return auth()->user()->isAdmin()
            ? redirect()->route('admin.requests.index')
            : redirect()->route('academic.requests.index');
    }
    return redirect()->route('requests.public.create');
});

Route::get('/submit', [PublicRequestController::class, 'create'])->name('requests.public.create');
Route::post('/submit', [PublicRequestController::class, 'store'])->name('requests.public.store');
Route::get('/submitted', [PublicRequestController::class, 'submitted'])->name('requests.public.submitted');

// Track a request by tracking code (no auth required)
Route::get('/track', [PublicRequestController::class, 'trackForm'])->name('requests.track');
Route::post('/track', [PublicRequestController::class, 'trackLookup'])->name('requests.track.lookup');

/*
|--------------------------------------------------------------------------
| Auth Routes (Breeze / UI style)
|--------------------------------------------------------------------------
*/

Auth::routes(['register' => false]); // No public registration

/*
|--------------------------------------------------------------------------
| Admin Routes
|--------------------------------------------------------------------------
*/

Route::prefix('admin')->name('admin.')->middleware(['auth'])->group(function () {

    Route::get('/dashboard', function () {
        return redirect()->route('admin.requests.index');
    })->name('dashboard');

    Route::resource('requests', AdminRequestController::class)
        ->except(['destroy']);

    Route::patch('requests/{request}/status', [AdminRequestController::class, 'changeStatus'])
        ->name('requests.change-status');
});

/*
|--------------------------------------------------------------------------
| Academic Routes
|--------------------------------------------------------------------------
*/

Route::prefix('academic')->name('academic.')->middleware(['auth'])->group(function () {

    Route::get('/dashboard', function () {
        return redirect()->route('academic.requests.index');
    })->name('dashboard');

    Route::get('requests', [AcademicRequestController::class, 'index'])
        ->name('requests.index');

    Route::get('requests/{request}', [AcademicRequestController::class, 'show'])
        ->name('requests.show');

    Route::patch('requests/{request}/decide', [AcademicRequestController::class, 'decide'])
        ->name('requests.decide');
});
