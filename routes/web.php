<?php

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

Route::group(['middleware' => 'auth'], function () {
    Route::group(['prefix' => 'users'], function () {
        Route::get('/index', [App\Http\Controllers\UsersController::class , 'index'])->name('users.index');
        Route::post('/list_users_ajax', [App\Http\Controllers\UsersController::class , 'list_users_ajax'])->name('users.list_users_ajax');
        Route::get('/add', [App\Http\Controllers\UsersController::class , 'add'])->name('users.add');
        Route::post('/create', [App\Http\Controllers\UsersController::class , 'create'])->name('users.create');
        Route::get('/edit/{id}', [App\Http\Controllers\UsersController::class , 'edit'])->name('users.edit');
        Route::post('/update', [App\Http\Controllers\UsersController::class , 'update'])->name('users.update');
    });
    Route::group(['prefix' => 'permissions'], function () {
        Route::get('/index', [App\Http\Controllers\PermissionController::class , 'index'])->name('permissions.index');
        Route::get('/add', [App\Http\Controllers\PermissionController::class , 'add'])->name('permissions.add');
        Route::post('/create', [App\Http\Controllers\PermissionController::class , 'create'])->name('permissions.create');
        Route::get('/edit/{id}', [App\Http\Controllers\PermissionController::class , 'edit'])->name('permissions.edit');
        Route::post('/update', [App\Http\Controllers\PermissionController::class , 'update'])->name('permissions.update');
    });
    Route::group(['prefix' => 'tasks_category'], function () {
        Route::get('/index', [App\Http\Controllers\TaskCategoryController::class , 'index'])->name('tasks_category.index');
        Route::get('/add', [App\Http\Controllers\TaskCategoryController::class , 'add'])->name('tasks_category.add');
        Route::post('/create', [App\Http\Controllers\TaskCategoryController::class , 'create'])->name('tasks_category.create');
        Route::get('/edit/{id}', [App\Http\Controllers\TaskCategoryController::class , 'edit'])->name('tasks_category.edit');
        Route::post('/update', [App\Http\Controllers\TaskCategoryController::class , 'update'])->name('tasks_category.update');
    });
    Route::group(['prefix' => 'tasks'], function () {
        Route::get('/index', [App\Http\Controllers\TasksController::class , 'index'])->name('tasks.index');
        Route::post('/list_tasks_ajax', [App\Http\Controllers\TasksController::class , 'list_tasks_ajax'])->name('tasks.list_tasks_ajax');
        Route::get('/add', [App\Http\Controllers\TasksController::class , 'add'])->name('tasks.add');
        Route::post('/create', [App\Http\Controllers\TasksController::class , 'create'])->name('tasks.create');
        Route::get('/edit/{id}', [App\Http\Controllers\TasksController::class , 'edit'])->name('tasks.edit');
        Route::post('/update', [App\Http\Controllers\TasksController::class , 'update'])->name('tasks.update');
        Route::group(['prefix' => 'task_submission'], function () {
            Route::get('/index/{task_id}', [App\Http\Controllers\TasksController::class , 'task_submission_index'])->name('tasks.task_submission.task_submission_index');
            Route::group(['prefix' => 'comments'], function () {
                Route::post('/get_comment_ajax', [App\Http\Controllers\TasksController::class , 'get_comment_ajax'])->name('tasks.task_submission.comments.get_comment_ajax');
                Route::post('/add_comment_ajax', [App\Http\Controllers\TasksController::class , 'add_comment_ajax'])->name('tasks.task_submission.comments.add_comment_ajax');
            });
        });
    });
    Route::post('/logout', [App\Http\Controllers\Auth\LoginController::class, 'logout'])->name('logout');
    Route::post('/api_get', function (Request $request) {
        $user_ip = $request->ip();

        $location = Http::post('http://ip-api.com/batch', [
            [
                'query' => $user_ip
            ]
        ]);

        return $location;
    });

    Route::get('generate',function(){
        return Artisan::call('storage:link');
    });
});
