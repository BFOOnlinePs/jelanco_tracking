<?php

namespace App\Http\Controllers\ApiControllers\user_controllers;

use App\Http\Controllers\Controller;
use App\Models\TaskSubmissionsModel;
use App\Models\User;
use App\Services\FileUploadService;
use App\Services\SubmissionService;
use App\Services\UserService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class userController extends Controller
{
    protected $submissionService;
    protected $fileUploadService;
    protected $userService;

    public function __construct(SubmissionService $submissionService, FileUploadService $fileUploadService, UserService $userService)
    {
        $this->submissionService = $submissionService;
        $this->fileUploadService = $fileUploadService;
        $this->userService = $userService;
    }

    public function getUserProfileById($user_id)
    {
        $user_info = User::find($user_id);
        // user departments from service
        $user_departments = $this->userService->getUserDepartments($user_id);
        $user_info->user_departments = $user_departments;

        // last version
        $submissions = TaskSubmissionsModel::where('ts_submitter', $user_id)
            ->whereNotIn('ts_id', function ($query) {
                $query->select('ts_parent_id')
                    ->from('task_submissions')
                    ->where('ts_parent_id', '!=', -1);
            })
            ->orderBy('created_at', 'desc')
            ->paginate(4);

        $this->submissionService->processSubmissions($submissions);

        // Check if the submission has a task
        $submissions_with_tasks = $submissions->map(function ($submission) {
            return $this->submissionService->getSubmissionTask($submission);
        });


        // they have the same length
        return response()->json([
            'status' => true,
            'user_info' => $user_info,
            'user_submissions' => [
                'pagination' => [
                    'current_page' => $submissions->currentPage(),
                    'last_page' => $submissions->lastPage(),
                    'per_page' => $submissions->perPage(),
                    'total_items' => $submissions->total(),
                ],
                'submissions' => $submissions_with_tasks->values(),
            ],
        ], 200);
    }

    public function updateProfileImage(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'profile_image' => 'required|image|mimes:jpeg,png,jpg',
        ], [
            'profile_image.required' => 'يجب تحديد الصورة الشخصية',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => $validator->errors()->first(),
            ]);
        }

        $user_id = Auth::user()->id;

        $user = User::find($user_id);
        $old_image = $user->image;
        $folder_path = config('constants.profile_images_path');

        // remove the old image from storage
        if ($old_image && Storage::exists('public/' . $folder_path . '/' . basename($old_image))) {
            Storage::delete('public/' . $folder_path . '/' . basename($old_image));
        }

        $file_name = $this->fileUploadService->uploadFile($request->profile_image, $folder_path);
        $user->image = $file_name;

        if ($user->save()) {
            // Return a success response with the new profile image URL
            return response()->json([
                'status' => true,
                'message' => 'تم تحديث الصورة الشخصية بنجاح',
                'image_url' => $file_name,
            ]);
        }

        return response()->json([
            'status' => false,
            'message' => 'حدث خطأ في تحديث الصورة الشخصية',
        ]);
    }

    public function changePassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            // 'user_id' => 'required|exists:users,id',,
            'password' => 'required',
        ], [
            // 'user_id.required' => 'الرجاء كتابة رقم المستخدم',
            // 'user_id.exists' => 'الرجاء كتابة رقم المستخدم بشكل صحيح',
            'password.required' => 'الرجاء كتابة كلمة المرور',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => $validator->errors()->first(),
            ]);
        }

        $user_id = Auth::user()->id;
        $user = User::where('id', $user_id)->first();
        if($user) {
            $user->password = bcrypt($request->password);
            if ($user->save()) {
                return response()->json([
                    'status' => true,
                    'message' => 'تم تغيير كلمة المرور بنجاح',
                ]);
            }
        }

        return response()->json([
            'status' => false,
            'message' => 'حدث خطأ أثناء تغيير كلمة المرور',
        ]);
    }

    public function getUserById($id)
    {
        $user = User::find($id);
        $user->user_departments = $this->userService->getUserDepartments($id);
        $permissions = User::find($id)->getAllPermissions();

        return response()->json([
            'status' => true,
            'user' => $user,
            'permissions' => $permissions
        ]);
    }

    public function getAllUsers(Request $request)
    {
        // Check if pagination is requested
        if ($request->has('paginate') && $request->paginate) {
            $users = User::select('id', 'name', 'image', 'job_title')
                ->when(request('search'), function ($query, $search) {
                    return $query->where('name', 'LIKE', "%{$search}%");
                })
                ->when($request->is_role, function ($query) {
                    return $query->with('roles');
                })
                ->orderBy('updated_at', 'desc')
                ->paginate(10);
        } else {
            // Return all users
            $users = User::select('id', 'name', 'image', 'job_title')
                ->when($request->is_role, function ($query) {
                    return $query->with('roles');
                })->get();
        }

        return response()->json([
            'status' => true,
            'pagination' => $request->has('paginate') && $request->paginate ? [
                'current_page' => $users->currentPage(),
                'last_page' => $users->lastPage(),
                'per_page' => $users->perPage(),
                'total_items' => $users->total(),
            ] : null,
            'users' => $request->has('paginate') && $request->paginate ? $users->values() : $users
        ]);
    }
}
