<?php

use App\Http\Controllers\ApiControllers\auth_controllers\LoginController;
use App\Http\Controllers\ApiControllers\auth_controllers\LogoutController;
use App\Http\Controllers\ApiControllers\comment_controllers\CommentController;
use App\Http\Controllers\ApiControllers\department_controllers\DepartmentControllers;
use App\Http\Controllers\ApiControllers\fcm_controllers\FcmController;
use App\Http\Controllers\ApiControllers\fcm_controllers\NotificationController;
use App\Http\Controllers\ApiControllers\interested_parties_controllers\InterestedPartiesController;
use App\Http\Controllers\ApiControllers\manager_employees_controllers\ManagerEmployeeController;
use App\Http\Controllers\ApiControllers\permissions_roles_controllers\MobilePermissionController;
use App\Http\Controllers\ApiControllers\permissions_roles_controllers\RoleController;
use App\Http\Controllers\ApiControllers\permissions_roles_controllers\UserRoleAndPermissionController;
use App\Http\Controllers\ApiControllers\submission_evaluation_controllers\SubmissionEvaluationController;
use App\Http\Controllers\ApiControllers\task_category_controllers\TaskCategoryController;
use App\Http\Controllers\ApiControllers\task_controllers\TaskAssignmentController;
use App\Http\Controllers\ApiControllers\task_controllers\TaskController;
use App\Http\Controllers\ApiControllers\task_submission_controllers\TaskSubmissionController;
use App\Http\Controllers\ApiControllers\user_controllers\userController;
use App\Http\Controllers\ApiControllers\user_controllers\UsersManagementController;
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

Route::post('/login', [LoginController::class, 'userLogin']);

// FCM
Route::post('/storeFcmUserToken', [FcmController::class, 'storeFcmUserToken']);
Route::post('/deleteFcmUserToken', [FcmController::class, 'deleteFcmUserToken']);
Route::post('/updateFcmUserToken', [FcmController::class, 'updateFcmUserToken']);

Route::group(['middleware' => ['auth:sanctum', 'checkActive']], function () {
    Route::post('logout', [LogoutController::class, 'logout']);

    Route::prefix('tasks')->group(function () {
        Route::get('/', [TaskController::class, 'getAllTasks']);
        Route::get('{id}/submissions-and-comments', [TaskController::class, 'getTaskWithSubmissionsAndComments']);
        Route::post('/', [TaskController::class, 'addTask']);
        Route::post('{id}', [TaskController::class, 'updateTask']);
        Route::get('added-by-user', [TaskAssignmentController::class, 'getTasksAddedByUser']);
        Route::get('assigned-to-user', [TaskAssignmentController::class, 'getTasksAssignedToUser']);
        Route::get('user-not-submitted-tasks', [TaskAssignmentController::class, 'getUserNotSubmittedTasks']);
        Route::get('{id}', [TaskController::class, 'getTaskById']);
    });

    Route::prefix('task-submissions')->group(function () {
        Route::post('/', [TaskSubmissionController::class, 'addTaskSubmission']);
        Route::get('today', [TaskSubmissionController::class, 'getTodaysSubmissions']);
        Route::get('{id}', [TaskSubmissionController::class, 'getTaskSubmission']);
        Route::get('{id}/versions', [TaskSubmissionController::class, 'getTaskSubmissionVersions']);
        Route::get('{id}/task-and-comments', [TaskSubmissionController::class, 'getTaskSubmissionWithTaskAndComments']);
        Route::post('evaluate', [SubmissionEvaluationController::class, 'evaluate']);
        Route::post('update-status', [TaskSubmissionController::class, 'updateSubmissionStatus']);
    });
    Route::get('user-submissions', [TaskSubmissionController::class, 'getUserSubmissions']);

    // manager and employees
    Route::get('users/employees', [ManagerEmployeeController::class, 'getManagerEmployees']);
    Route::get('users/employees/{manager_id}', [ManagerEmployeeController::class, 'getManagerEmployeesById']);
    Route::post('users/employees/with-task-assignees', [ManagerEmployeeController::class, 'getManagerEmployeesWithTaskAssignees']);
    Route::get('users/managers', [ManagerEmployeeController::class, 'getManagers']);
    Route::post('users/employees/add-edit', [ManagerEmployeeController::class, 'addEditManagerEmployees']);
    Route::post('users/managers/assign', [ManagerEmployeeController::class, 'assignEmployeeForManagers']);
    Route::post('users/managers/delete', [ManagerEmployeeController::class, 'deleteManager']);
    Route::get('users/{user_id}/managers-and-employees', [ManagerEmployeeController::class, 'getManagersAndEmployeesOfUser']);


    // user
    Route::get('users', [userController::class, 'getAllUsers']);
    Route::get('users/{id}', [userController::class, 'getUserById']);
    Route::post('users', [UsersManagementController::class, 'addUser']);
    Route::post('users/{id}', [UsersManagementController::class, 'updateUser']);

    // user profile
    Route::get('users/profile/{user_id}', [userController::class, 'getUserProfileById']);
    Route::post('users/profile/image', [userController::class, 'updateProfileImage']);
    Route::post('users/profile/change-password', [userController::class, 'changePassword']);

    // task categories
    Route::get('task-categories', [TaskCategoryController::class, 'getTaskCategories']);

    // departments
    Route::get('departments', [DepartmentControllers::class, 'getDepartments']);

    // comments
    Route::post('comments', [CommentController::class, 'addTaskSubmissionComment']);
    Route::get('task-submissions/{id}/comments', [CommentController::class, 'getSubmissionComments']);
    Route::get('task-submissions/{id}/comments/count', [CommentController::class, 'getSubmissionCommentCount']);

    Route::group(['prefix' => 'interested-parties'], function () {
        Route::get('/', [InterestedPartiesController::class, 'getInterestedParties']);
        Route::post('/', [InterestedPartiesController::class, 'handleInterestedParties']);
        Route::post('articles', [InterestedPartiesController::class, 'getArticlesOfInterest']);
    });


    Route::group(['prefix' => 'notifications'], function () {
        Route::get('/', [NotificationController::class, 'getUserNotifications']);
        Route::get('unread-count', [NotificationController::class, 'unreadNotificationsCount']);
        Route::get('read/{notification_id}', [NotificationController::class, 'readNotification']);
        Route::get('read-all', [NotificationController::class, 'readAll']);
    });


    // Route::middleware(['role:admin'])->group(function () {
    // Admin-only routes

    Route::prefix('roles')->group(function () {
        Route::get('/', [RoleController::class, 'index']);
        Route::post('/', [RoleController::class, 'store']);
        Route::put('{id}', [RoleController::class, 'update']);
        Route::delete('{id}', [RoleController::class, 'destroy']);
        Route::post('{id}/permissions', [RoleController::class, 'assignPermissions']);
        Route::get('/roles-with-permissions', [RoleController::class, 'getAllRolesWithPermissions']);
    });

    Route::prefix('permissions')->group(function () {
        Route::get('/', [MobilePermissionController::class, 'index']);
        Route::post('/', [MobilePermissionController::class, 'store']);
        Route::get('{id}', [MobilePermissionController::class, 'show']);
        Route::put('{id}', [MobilePermissionController::class, 'update']);
        Route::delete('{id}', [MobilePermissionController::class, 'destroy']);
    });

    Route::prefix('users')->group(function () {
        // Route::get('{id}/roles-permissions', [UserRoleAndPermissionController::class, 'getRolesPermissions']);
        Route::get('{id}/roles-and-permissions', [UserRoleAndPermissionController::class, 'getRolesAndPermissions']);
        Route::post('{id}/roles', [UserRoleAndPermissionController::class, 'assignRoles']);
        Route::post('{id}/permissions', [UserRoleAndPermissionController::class, 'assignPermissions']);
        // Route::post('{id}/remove-role', [UserRoleAndPermissionController::class, 'removeRole']);
        // Route::post('{id}/remove-permission', [UserRoleAndPermissionController::class, 'removePermission']);
    });
    // });



    //        Mohamad Maraqa
    Route::group(['prefix' => 'users'], function () {
        Route::group(['prefix' => 'roles'], function () {
            Route::get('get_roles', [App\Http\Controllers\ApiControllers\roles_and_permissions\RolesController::class, 'get_roles']);
            Route::post('create', [App\Http\Controllers\ApiControllers\roles_and_permissions\RolesController::class, 'create']);
        });
        Route::group(['prefix' => 'permissions'], function () {
            Route::get('get_permission', [App\Http\Controllers\ApiControllers\roles_and_permissions\PermissionController::class, 'get_permission']);
        });
    });
});
