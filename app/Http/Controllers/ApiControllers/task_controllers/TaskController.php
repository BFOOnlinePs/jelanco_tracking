<?php

namespace App\Http\Controllers\ApiControllers\task_controllers;

use App\Http\Controllers\Controller;
use App\Models\TaskModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class TaskController extends Controller
{

    public function getAllTasks(){
        $tasks = TaskModel::get();

        return $tasks;
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
}
