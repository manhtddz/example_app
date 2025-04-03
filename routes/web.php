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
use App\Models\Employee;
use App\Models\Team;
use Illuminate\Support\Facades\Route;

Route::get('/', [AuthController::class, 'index'])->name('auth.admin')->middleware(
);
Route::post('login', [AuthController::class, 'login'])->name('auth.login');
Route::get('logout', [AuthController::class, 'logout'])->name('auth.logout');
//emp crud route
Route::middleware([
    AuthenticationMiddleware::class,
    ClearTempFileMiddleware::class,
    TimeoutMiddleware::class
])->prefix("team")->group(function () {
    Route::post('update/{id}', [TeamController::class, 'update'])->name('team.update');
    Route::post('create', [TeamController::class, 'create'])->name('team.create');
    Route::post('delete/{id}', [TeamController::class, 'delete'])->name('team.delete');
});

//team crud route
Route::middleware([
    AuthenticationMiddleware::class,
    ClearTempFileMiddleware::class,
    TimeoutMiddleware::class
])->prefix("employee")->group(function () {
    Route::post('update/{id}', [EmployeeController::class, 'update'])->name('employee.update');
    Route::post('create', [EmployeeController::class, 'create'])->name('employee.create');
    Route::post('delete/{id}', [EmployeeController::class, 'delete'])->name('employee.delete');
});

//project crud route
Route::middleware([
    AuthenticationMiddleware::class,
    ClearTempFileMiddleware::class,
    TimeoutMiddleware::class
])->prefix("project")->group(function () {
    Route::post('update/{id}', [ProjectController::class, 'update'])->name('project.update');
    Route::post('create', [ProjectController::class, 'create'])->name('project.create');
    Route::post('delete/{id}', [ProjectController::class, 'delete'])->name('project.delete');
    Route::post('/add-employees/{id}', [ProjectController::class, 'addEmployees'])
        ->name('project.addEmployees');

});

//task crud route
Route::middleware([
    AuthenticationMiddleware::class,
    ClearTempFileMiddleware::class,
    TimeoutMiddleware::class
])->prefix("task")->group(function () {
    Route::post('update/{id}', [TaskController::class, 'update'])->name('task.update');
    Route::post('create/{projectId?}', [TaskController::class, 'create'])->name('task.create');
    // Route::post('create/{projectId}', [TaskController::class, 'createAndRedirectToProjectDetails'])->name('task.createToProject');
    Route::post('delete/{id}', [TaskController::class, 'delete'])->name('task.delete');
    Route::post('/add-employees/{id}', [TaskController::class, 'addEmployees'])
        ->name('task.addEmployees');
});

//team get template
Route::middleware([
    AuthenticationMiddleware::class,
    ClearSessionTempFileMiddleware::class
])->prefix("team")->group(function () {

    Route::get('', [TeamController::class, 'index'])->name('team.index');

    Route::get('edit/{id}', [TeamController::class, 'edit'])->name('team.edit');
    Route::get('show/{id}', [TeamController::class, 'show'])->name('team.show');
    Route::post('updateConfirm/{id}', [TeamController::class, 'updateConfirm'])->name('team.updateConfirm');
    Route::get('updateConfirm/{id}', [TeamController::class, 'showUpdateConfirm'])->name('team.updateConfirm');

    Route::get('create', [TeamController::class, 'getCreateForm'])->name('team.create');
    Route::post('createConfirm', [TeamController::class, 'createConfirm'])->name('team.createConfirm');
    Route::get('createConfirm', [TeamController::class, 'showCreateConfirm'])->name('team.showCreateConfirm');
});

//employee get template
Route::middleware([
    AuthenticationMiddleware::class,
    ClearSessionTempFileMiddleware::class
])->prefix("employee")->group(function () {

    Route::get('', [EmployeeController::class, 'index'])->name('employee.index');

    Route::get('show/{id}', [EmployeeController::class, 'show'])->name('employee.show');
    Route::post('updateConfirm/{id}', [EmployeeController::class, 'updateConfirm'])->name('employee.updateConfirm');
    Route::get('updateConfirm/{id}', [EmployeeController::class, 'showUpdateConfirm'])->name('employee.updateConfirm');

    Route::post('createConfirm', [EmployeeController::class, 'createConfirm'])->name('employee.createConfirm');
    Route::get('createConfirm', [EmployeeController::class, 'showCreateConfirm'])->name('employee.showCreateConfirm');
    Route::post('export', [EmployeeController::class, 'export'])->name('employee.export');
});

//project get template
Route::middleware([
    AuthenticationMiddleware::class,
    ClearSessionTempFileMiddleware::class
])->prefix("project")->group(function () {

    Route::get('', [ProjectController::class, 'index'])->name('project.index');

    Route::get('edit/{id}', [ProjectController::class, 'edit'])->name('project.edit');
    Route::get('show/{id}', [ProjectController::class, 'show'])->name('project.show');
    Route::get('add-employees/{id}', [ProjectController::class, 'getAddEmployees'])->name('project.addEmployees');
    // Route::get('add-task', [ProjectController::class, 'getAddTaskForm'])->name('project.getAddTaskForm');
    Route::post('updateConfirm/{id}', [ProjectController::class, 'updateConfirm'])->name('project.updateConfirm');
    Route::get('updateConfirm/{id}', [ProjectController::class, 'showUpdateConfirm'])->name('project.updateConfirm');

    Route::get('create', [ProjectController::class, 'getCreateForm'])->name('project.create');
    Route::post('createConfirm', [ProjectController::class, 'createConfirm'])->name('project.createConfirm');
    Route::get('createConfirm', [ProjectController::class, 'showCreateConfirm'])->name('project.showCreateConfirm');
});

//task get template
Route::middleware([
    AuthenticationMiddleware::class,
    ClearSessionTempFileMiddleware::class
])->prefix("task")->group(function () {
    Route::get('', [TaskController::class, 'index'])->name('task.index');

    Route::get('edit/{id}', [TaskController::class, 'edit'])->name('task.edit');
    Route::get('show/{id}', [TaskController::class, 'show'])->name('task.show');
    Route::get('add-employees/{id}', [TaskController::class, 'getAddEmployees'])->name('task.addEmployees');
    Route::post('updateConfirm/{id}', [TaskController::class, 'updateConfirm'])->name('task.updateConfirm');
    Route::get('updateConfirm/{id}', [TaskController::class, 'showUpdateConfirm'])->name('task.updateConfirm');

    Route::get('create/{projectId?}', [TaskController::class, 'getCreateForm'])->name('task.create');
    Route::post('createConfirm{projectId?}', [TaskController::class, 'createConfirm'])->name('task.createConfirm');
    Route::get('createConfirm', [TaskController::class, 'showCreateConfirm'])->name('task.showCreateConfirm');
});

//employee get create and update template
Route::middleware([
    AuthenticationMiddleware::class,
])->prefix("employee")->group(function () {
    Route::get('edit/{id}', [EmployeeController::class, 'edit'])->name('employee.edit');
    Route::get('create', [EmployeeController::class, 'getCreateForm'])->name('employee.create');
});

// Route::get('emp/get', function () {
//     return response()->json(
//         // Team::addSelect([
//         //     'emp_count' => Employee::select('COUNT(*)')
//         //         ->whereColumn('team_id', 'm_teams.id')
//         //         ->where('salary', '>', 1)
//         // ->orderByDesc('id')
//         // ->limit(1)
//         // ])->get()
//         Team::whereIn(
//             'id',
//             Employee::select('team_id')
//                 ->where('salary', '>', 1)
//         )->get()
//     );
// });