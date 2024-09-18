<?php

use App\Http\Controllers\ApiControllers\auth_controllers\LoginController;
use App\Http\Controllers\ApiControllers\auth_controllers\LogoutController;
use App\Http\Controllers\ApiControllers\auth_controllers\SignupController;
use App\Http\Controllers\ApiControllers\comment_controllers\CommentController;
use App\Http\Controllers\ApiControllers\fcm_controllers\FcmController;
use App\Http\Controllers\ApiControllers\manager_employees_controllers\ManagerEmployeeController;
use App\Http\Controllers\ApiControllers\task_category_controllers\TaskCategoryController;
use App\Http\Controllers\ApiControllers\task_controllers\TaskAssignmentController;
use App\Http\Controllers\ApiControllers\task_controllers\TaskController;
use App\Http\Controllers\ApiControllers\task_submission_controllers\TaskSubmissionController;
use App\Http\Controllers\ApiControllers\user_controllers\userController;
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


use Kreait\Firebase\Factory;

Route::get('/firebase-check', function () {
    $factory = (new Factory)->withServiceAccount(config('firebase.projects.app.credentials'));
    $auth = $factory->createAuth();

    return 'Firebase service is working!';
});

Route::post('/login', [LoginController::class, 'userLogin']);
Route::post('register', [SignupController::class, 'signUp']);

Route::get('users', [userController::class, 'getAllUsers']);


// FCM
Route::post('/storeFcmUserToken', [FcmController::class, 'storeFcmUserToken']);
Route::post('/deleteFcmUserToken', [FcmController::class, 'deleteFcmUserToken']);
Route::post('/updateFcmUserToken', [FcmController::class, 'updateFcmUserToken']);


Route::group(['middleware' => ['auth:sanctum']], function () {
    Route::post('logout', [LogoutController::class, 'logout']);

    // tasks
    Route::get('tasks', [TaskController::class, 'getAllTasks']);
    // Route::get('tasks/{id}', [TaskController::class, 'getTask']);
    Route::get('tasks/{id}/submissions-and-comments', [TaskController::class, 'getTaskWithSubmissionsAndComments']);
    Route::post('tasks', [TaskController::class, 'addTask']);
    Route::post('tasks/{id}', [TaskController::class, 'updateTask']);
    Route::get('/tasks/added-by-user', [TaskAssignmentController::class, 'getTasksAddedByUser']);
    Route::get('/tasks/assigned-to-user', [TaskAssignmentController::class, 'getTasksAssignedToUser']);
    Route::get('/tasks/user-not-submitted-tasks', [TaskAssignmentController::class, 'getUserNotSubmittedTasks']);

    // tasks submissions
    Route::post('task-submissions', [TaskSubmissionController::class, 'addTaskSubmission']);
    Route::get('task-submissions/today', [TaskSubmissionController::class, 'getTodaysSubmissions']);
    Route::get('task-submissions/{id}', [TaskSubmissionController::class, 'getTaskSubmissionWithTaskAndComments']);
    Route::get('task-submissions/{id}/versions', [TaskSubmissionController::class, 'getTaskSubmissionVersions']);
    Route::get('user-submissions', [TaskSubmissionController::class, 'getUserSubmissions']);
    Route::get('task-submissions/{id}/task-and-comments', [TaskSubmissionController::class, 'getTaskSubmissionWithTaskAndComments']);

    // manager and employees
    Route::get('users/employees', [ManagerEmployeeController::class, 'getManagerEmployees']);


    // user profile
    Route::get('users/profile/{user_id}', [userController::class, 'getUserProfileById']);
    Route::post('users/profile/image', [userController::class, 'updateProfileImage']);
    Route::get('users/{id}', [userController::class, 'getUserById']);

    // task categories
    Route::get('task-categories', [TaskCategoryController::class, 'getTaskCategories']);

    // comments
    Route::post('comments', [CommentController::class, 'addTaskSubmissionComment']);
    Route::get('task-submissions/{id}/comments', [CommentController::class, 'getSubmissionComments']);
    Route::get('task-submissions/{id}/comments/count', [CommentController::class, 'getSubmissionCommentCount']);

    Route::group(['prefix' => 'users'], function () {
        Route::group(['prefix' => 'roles'], function () {
            Route::get('get_roles', [App\Http\Controllers\ApiControllers\roles_and_permissions\RolesController::class, 'get_roles']);
            Route::post('create', [App\Http\Controllers\ApiControllers\roles_and_permissions\RolesController::class, 'create']);
        });
        //        Mohamad Maraqa
        Route::group(['prefix' => 'permissions'], function () {
            Route::get('get_permission', [App\Http\Controllers\ApiControllers\roles_and_permissions\PermissionController::class, 'get_permission']);
        });
    });
});
