<?php

use App\Http\Controllers\ApiControllers\auth_controllers\LoginController;
use App\Http\Controllers\ApiControllers\auth_controllers\SignupController;
use App\Http\Controllers\ApiControllers\task_category_controllers\TaskCategoryController;
use App\Http\Controllers\ApiControllers\task_controllers\TaskController;
use App\Http\Controllers\ApiControllers\user_controllers\userController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::post('login', [LoginController::class, 'userLogin']);
Route::post('register', [SignupController::class, 'signUp']);


Route::group(['middleware' => ['auth:sanctum']], function () {
    Route::get('tasks', [TaskController::class, 'getAllTasks']);
    Route::post('tasks', [TaskController::class, 'addTask']);
    Route::post('tasks/{id}', [TaskController::class, 'updateTask']);
    Route::get('/tasks/added-by-user', [TaskController::class, 'getTasksAddedByUser']);
    Route::get('/tasks/assigned-to-user', [TaskController::class, 'getTasksAssignedToUser']);

    Route::get('task-categories', [TaskCategoryController::class, 'getTaskCategories']);

    Route::get('users', [userController::class, 'getAllUsers']);
});
