<?php

namespace App\Http\Controllers\ApiControllers\task_controllers;

use App\Http\Controllers\Controller;
use App\Models\AttachmentsModel;
use App\Models\TaskModel;
use App\Models\TaskSubmissionCommentsModel;
use App\Models\TaskSubmissionsModel;
use App\Models\User;
use App\Services\MediaService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use SplFileInfo;

class TaskController extends Controller
{
    protected $mediaService;

    public function __construct(MediaService $mediaService)
    {
        $this->mediaService = $mediaService;
    }

    public function getAllTasks()
    {
        $tasks = TaskModel::get();

        return $tasks;
    }

    public function getTaskWithSubmissionsAndComments($id)
    {
        $task = TaskModel::where('t_id', $id)
            ->with('taskCategory:c_id,c_name')
            ->with('addedByUser:id,name')
            ->first();

        if ($task) {
            // $task->task_submissions = TaskSubmissionsModel::where('ts_task_id', $id)->get();

            // to get the last submission of each versions chain
            $task->task_submissions = TaskSubmissionsModel::where('ts_task_id', $id)
                ->whereNotIn('ts_id', function ($query) {
                    $query->select('ts_parent_id')
                        ->from('task_submissions')
                        ->where('ts_parent_id', '!=', -1);
                })
                ->get();

            $task->assigned_to_users = User::whereIn('id', json_decode($task->t_assigned_to))->select('id', 'name')->get();

            $task->task_submissions->transform(function ($submission) {
                // to get the submitter user
                $user_id = $submission->ts_submitter;
                $user = User::where('id', $user_id)->select('id', 'name')->first();
                $submission->submitter_user = $user;

                // to get the task submission comments
                $submission->submission_comments = TaskSubmissionCommentsModel::where('tsc_task_submission_id', $submission->ts_id)->get();
                $submission->submission_comments->transform(function ($comment) {
                    $comment->commented_by_user = User::where('id', $comment->tsc_commented_by)->select('id', 'name')->first();
                    $comment_media = $this->mediaService->getMedia('task_submission_comments', $comment->tsc_id);

                    $comment->comment_attachments_categories = $comment_media;
                    return $comment;
                });

                $submission_media = $this->mediaService->getMedia('task_submissions', $submission->ts_id);

                $submission->submission_attachments_categories = $submission_media;

                return $submission;
            });
        } else {
            return response()->json([
                'status' => false,
                'message' => 'The Task is not exists',
            ]);
        }

        return response()->json([

            'status' => true,
            'task' => $task,
        ]);
    }

    public function addTask(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'content' => 'required',
            'assigned_to' => 'required',
            'start_time' => 'nullable',
            'end_time' => 'nullable',
            'category_id' => 'nullable|exists:task_categories,c_id',
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
