<?php

namespace App\Http\Controllers\ApiControllers\task_controllers;

use App\Http\Controllers\Controller;
use App\Models\TaskModel;
use App\Models\User;
use Illuminate\Http\Request;

class TaskAssignmentController extends Controller
{

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
}
