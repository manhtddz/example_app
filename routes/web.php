<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\TeamController;
use App\Http\Middleware\AuthenticationMiddleware;
use App\Http\Middleware\ClearSessionTempFileMiddleware;
use App\Http\Middleware\ClearTempFileMiddleware;
use App\Http\Middleware\TimeoutMiddleware;
use App\Http\Middleware\AccessAuthorizationMiddleware;
use Illuminate\Support\Facades\Route;

Route::get('/', [AuthController::class, 'index'])->name('auth.admin');
Route::post('login', [AuthController::class, 'login'])->name('auth.login');
Route::get('logout', [AuthController::class, 'logout'])->name('auth.logout');

// Group for each resource
foreach (['team' => TeamController::class, 'employee' => EmployeeController::class, 'project' => ProjectController::class, 'task' => TaskController::class] as $prefix => $controller) {
    Route::middleware([
        AuthenticationMiddleware::class,
        ClearTempFileMiddleware::class,
        TimeoutMiddleware::class
    ])->prefix($prefix)->group(function () use ($prefix, $controller) {

        // Routes with AccessAuthorizationMiddleware
        Route::middleware([AccessAuthorizationMiddleware::class])->group(function () use ($prefix, $controller) {
            Route::post('update/{id}', [$controller, 'update'])->name("$prefix.update");
            Route::post('create', [$controller, 'create'])->name("$prefix.create");
            Route::post('delete/{id}', [$controller, 'delete'])->name("$prefix.delete");

            if ($prefix === 'project' || $prefix === 'task') {
                Route::post('add-employees/{id}', [$controller, 'addEmployees'])->name("$prefix.addEmployees");
            }
        });
    });
}

// Template + confirm routes
foreach ([
    'team' => TeamController::class,
    'employee' => EmployeeController::class,
    'project' => ProjectController::class,
    'task' => TaskController::class
] as $prefix => $controller) {
    Route::middleware([
        AuthenticationMiddleware::class,
        ClearSessionTempFileMiddleware::class
    ])->prefix($prefix)->group(function () use ($prefix, $controller) {

        Route::get('', [$controller, 'index'])->name("$prefix.index");
        Route::get('show/{id}', [$controller, 'show'])->name("$prefix.show");

        // Routes that need AccessAuthorizationMiddleware
        Route::middleware([AccessAuthorizationMiddleware::class])->group(function () use ($prefix, $controller) {
            Route::get('edit/{id}', [$controller, 'edit'])->name("$prefix.edit");
            Route::post('updateConfirm/{id}', [$controller, 'updateConfirm'])->name("$prefix.updateConfirm");
            Route::get('updateConfirm/{id}', [$controller, 'showUpdateConfirm'])->name("$prefix.updateConfirm");

            Route::get('create', [$controller, 'getCreateForm'])->name("$prefix.create");
            Route::post('createConfirm', [$controller, 'createConfirm'])->name("$prefix.createConfirm");
            Route::get('createConfirm', [$controller, 'showCreateConfirm'])->name("$prefix.showCreateConfirm");

            if ($prefix === 'project' || $prefix === 'task') {
                Route::get('add-employees/{id}', [$controller, 'getAddEmployees'])->name("$prefix.addEmployees");
            }
        });

        // Special case for employee export
        if ($prefix === 'employee') {
            Route::middleware([AccessAuthorizationMiddleware::class])->post('export', [EmployeeController::class, 'export'])->name('employee.export');
        }
    });
}

// Optional routes kept separate for now
Route::middleware([AuthenticationMiddleware::class])->prefix('employee')->group(function () {
    Route::middleware([AccessAuthorizationMiddleware::class])->group(function () {
        Route::get('edit/{id}', [EmployeeController::class, 'edit'])->name('employee.edit');
        Route::get('create', [EmployeeController::class, 'getCreateForm'])->name('employee.create');
    });
});
