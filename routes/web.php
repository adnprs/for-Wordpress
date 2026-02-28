<?php

use App\Models\App;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\RoleSwitchController;
use App\Http\Controllers\WorkspaceController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect('/login');
});

Route::middleware(['auth', 'verified'])->prefix('dashboard')->name('dashboard.')->group(function () {
    Route::get('/workspace/{app}', function (App $app) {
        $tools = $app->type === App::TYPE_ANDROID
            ? ['Firebase Data Console', 'Content Queue Monitor', 'Push Notification Tools']
            : ['WordPress Post Manager', 'Category & Tag Setup', 'Media & SEO Toolkit'];

        return view('dashboard.workspace', [
            'selectedApp' => $app,
            'tools' => $tools,
            'activeModule' => 'workspace',
            'stats' => [
                'drafts_generated' => 0,
                'api_calls' => 0,
            ],
        ]);
    })->name('workspace');

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

Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/choose-role', [RoleSwitchController::class, 'showSelection'])->name('role.choose');
    Route::post('/choose-role', [RoleSwitchController::class, 'setRole'])->name('role.set');
});

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/workspace/{id}', [WorkspaceController::class, 'show'])->name('workspace.show');
    Route::post('/workspace/{id}/generate', [WorkspaceController::class, 'generate'])->name('workspace.generate');
    Route::post('/workspace/{id}/publish', [WorkspaceController::class, 'publish'])->name('workspace.publish');
});

Route::prefix('admin')->name('admin.')->middleware(['auth', 'verified', 'role:admin'])->group(function () {
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');

    Route::get('/apps', [AdminController::class, 'appsIndex'])->name('apps.index');
    Route::post('/apps', [AdminController::class, 'appsStore'])->name('apps.store');
    Route::put('/apps/{app}', [AdminController::class, 'appsUpdate'])->name('apps.update');
    Route::delete('/apps/{app}', [AdminController::class, 'appsDestroy'])->name('apps.destroy');

    Route::get('/prompts', [AdminController::class, 'promptsIndex'])->name('prompts.index');
    Route::post('/prompts', [AdminController::class, 'promptsStore'])->name('prompts.store');
    Route::put('/prompts/{prompt}', [AdminController::class, 'promptsUpdate'])->name('prompts.update');
    Route::delete('/prompts/{prompt}', [AdminController::class, 'promptsDestroy'])->name('prompts.destroy');

    Route::get('/users', [AdminController::class, 'usersIndex'])->name('users.index');
    Route::post('/users', [AdminController::class, 'usersStore'])->name('users.store');
    Route::put('/users/{user}', [AdminController::class, 'usersUpdate'])->name('users.update');
    Route::delete('/users/{user}', [AdminController::class, 'usersDestroy'])->name('users.destroy');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
