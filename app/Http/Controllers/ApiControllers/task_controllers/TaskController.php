<?php

namespace App\Http\Controllers\ApiControllers\task_controllers;

use App\Http\Controllers\Controller;
use App\Models\TaskModel;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class TaskController extends Controller
{

    public function getAllTasks()
    {
        $tasks = TaskModel::get();

        return $tasks;
    }

    public function getTasksAddedByUser()
    {
        $user_id = auth()->user()->id;
        $tasks = TaskModel::where('t_added_by', $user_id)
            ->with('taskCategory:c_id,c_name')
            ->orderBy('created_at', 'desc')
            ->paginate(6);

        $tasks->transform(function ($task) {
            $user_ids = json_decode($task->t_assigned_to);
            $temp_users = User::whereIn('id', $user_ids)->select('id', 'name')->get();
            $task->assigned_to_users = $temp_users;
            return $task;
        });

        return response()->json([
            'status' => true,
            'pagination' => [
                'current_page' => $tasks->currentPage(),
                'last_page' => $tasks->lastPage(),
                'per_page' => $tasks->perPage(),
                'total_items' => $tasks->total(),
            ],
            'tasks' => $tasks->values(),
        ]);
    }

    public function getTasksAssignedToUser()
    {
        $userId = auth()->user()->id;
        $tasks = TaskModel::whereJsonContains('t_assigned_to', (string)$userId)
            ->with('taskCategory:c_id,c_name')
            ->with('addedByUser:id,name')
            ->orderBy('created_at', 'desc')
            ->paginate(6);

        return response()->json([
            'status' => true,
            'pagination' => [
                'current_page' => $tasks->currentPage(),
                'last_page' => $tasks->lastPage(),
                'per_page' => $tasks->perPage(),
                'total_items' => $tasks->total(),
            ],
            'tasks' => $tasks->values(),
        ]);
    }

    public function addTask(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'content' => 'required',
            'start_time' => 'nullable',
            'end_time' => 'nullable',
            'category_id' => 'nullable|exists:task_categories,c_id',
            'assigned_to' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => $validator->errors()->first(),
            ]);
        }

        $auth_id = auth()->user()->id;

        $task = new TaskModel();
        $task->t_content = $request->input('content');
        $task->t_planed_start_time = $request->input('start_time');
        $task->t_planed_end_time = $request->input('end_time');
        $task->t_status = 'active'; // default
        $task->t_category_id = $request->input('category_id');
        $task->t_added_by = $auth_id;
        $task->t_assigned_to = $request->input('assigned_to');

        if ($task->save()) {
            return response()->json([
                'status' => true,
                'message' => 'تم إضافة المهمة بنجاح',
                // 'task' => $task,
            ]);
        } else {
            return response()->json([
                'status' => false,
                'message' => 'حدث خلل أثناء إضافة المهمة، حاول لاحقاً',
            ]);
        }
    }

    public function updateTask(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            // 'task_id' => 'required|exists:tasks,t_id',
            'content' => 'required',
            'start_time' => 'nullable',
            'end_time' => 'nullable',
            'category_id' => 'nullable|exists:task_categories,c_id',
            'assigned_to' => 'required',
            'status' => 'required|in:active,notActive'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => $validator->errors()->first(),
            ]);
        }

        $task = TaskModel::where('t_id', $id)->first();

        $auth_id = auth()->user()->id;

        if ($task) {
            if ($auth_id != $task->t_added_by) {
                return response()->json([
                    'status' => false,
                    'message' => 'Not authorized; only the user who added it can edit it.',
                ]);
            }

            $task->update([
                't_content' => $request->input('content'),
                't_planed_start_time' => $request->input('start_time'),
                't_planed_end_time' => $request->input('end_time'),
                't_status' => $request->input('status'),
                't_category_id' => $request->input('category_id'),
                't_assigned_to' => $request->input('assigned_to'),
            ]);

            return response()->json([
                'status' => true,
                'message' => 'تم تعديل المهمة بنجاح',
                // 'task' => $task,
            ]);
        } else {
            return response()->json([
                'status' => false,
                'message' => 'The Task is not exists',
            ]);
        }
    }
}
