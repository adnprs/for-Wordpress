<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::middleware(['auth', 'verified'])->prefix('dashboard')->name('dashboard.')->group(function () {
    Route::get('/seo-generator', function () {
        return view('dashboard.modules.seo-generator', [
            'activeModule' => 'seo-generator',
            'stats' => [
                'drafts_generated' => 0,
                'api_calls' => 0,
            ],
        ]);
    })->name('seo-generator');

    Route::get('/pdf-upload', function () {
        return view('dashboard.modules.pdf-upload', [
            'activeModule' => 'pdf-upload',
            'stats' => [
                'drafts_generated' => 0,
                'api_calls' => 0,
            ],
        ]);
    })->name('pdf-upload');

    Route::get('/jobshorts-firebase', function () {
        return view('dashboard.modules.jobshorts-firebase', [
            'activeModule' => 'jobshorts-firebase',
            'stats' => [
                'drafts_generated' => 0,
                'api_calls' => 0,
            ],
        ]);
    })->name('jobshorts-firebase');

    Route::get('/app-settings', function () {
        return view('dashboard.modules.app-settings', [
            'activeModule' => 'app-settings',
            'stats' => [
                'drafts_generated' => 0,
                'api_calls' => 0,
            ],
        ]);
    })->name('app-settings');
});

Route::get('/dashboard', function () {
    return redirect()->route('dashboard.seo-generator');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
