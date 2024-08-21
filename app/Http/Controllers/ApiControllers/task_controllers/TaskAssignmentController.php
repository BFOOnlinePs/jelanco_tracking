<?php

namespace App\Http\Controllers\ApiControllers\task_controllers;

use App\Http\Controllers\Controller;
use App\Models\TaskModel;
use App\Models\TaskSubmissionsModel;
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


    public function getUserNotSubmittedTasks(Request $request)
    {
        $user_id = auth()->user()->id;
        // $user_tasks_ids = TaskModel::whereJsonContains('t_assigned_to', (string)$user_id)
        //     ->pluck('t_id');

        // $submitted_task_ids = TaskSubmissionsModel::where('ts_submitter', $user_id)
        //     ->whereIn('ts_task_id', $user_tasks_ids)
        //     ->pluck('ts_task_id');

        // $not_submitted_tasks = TaskModel::whereIn('t_id', $user_tasks_ids)
        //     ->whereNotIn('t_id', $submitted_task_ids)
        //     ->get();

        $perPage = $request->query('per_page', 10);

        $not_submitted_tasks = TaskModel::whereJsonContains('t_assigned_to', (string)$user_id)
            ->leftJoin('task_submissions', function ($join) use ($user_id) {
                $join->on('tasks.t_id', '=', 'task_submissions.ts_task_id')
                    ->where('task_submissions.ts_submitter', '=', $user_id);
            })
            ->whereNull('task_submissions.ts_task_id') // Ensure there is no submission for this task by the current user
            ->with('taskCategory:c_id,c_name')
            ->with('addedByUser:id,name')
            ->orderBy('created_at', 'desc')
            ->select('tasks.*')
            ->paginate($perPage);


        return response()->json([
            'status' => true,
            'pagination' => [
                'current_page' => $not_submitted_tasks->currentPage(),
                'last_page' => $not_submitted_tasks->lastPage(),
                'per_page' => $not_submitted_tasks->perPage(),
                'total_items' => $not_submitted_tasks->total(),
            ],
            'tasks' => $not_submitted_tasks->values(),
        ]);
    }
}
