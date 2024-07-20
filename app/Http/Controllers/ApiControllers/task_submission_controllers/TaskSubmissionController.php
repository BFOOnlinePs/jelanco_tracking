<?php

namespace App\Http\Controllers\ApiControllers\task_submission_controllers;

use App\Http\Controllers\Controller;
use App\Models\TaskSubmissionsModel;
use App\Services\FileUploadService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class TaskSubmissionController extends Controller
{
    protected $fileUploadService;

    // Inject the FileUploadService into the controller
    public function __construct(FileUploadService $fileUploadService)
    {
        $this->fileUploadService = $fileUploadService;
    }

    public function addTaskSubmission(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'parent_id' => 'int', // -1 when no parent (the original/first submission)
            'task_id' => 'required|exists:tasks,t_id',
            'content' => 'required',
            'start_latitude' => 'required',
            'start_longitude' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => true,
                'message' => $validator->errors()->first(),
            ], 422);
        }

        // submitter from auth
        $submitter = auth()->user()->id;
        $start_time = Carbon::now();

        $task_submission = new TaskSubmissionsModel();
        $task_submission->ts_parent_id = $request->input('parent_id');
        $task_submission->ts_task_id = $request->input('task_id');
        $task_submission->ts_submitter = $submitter;
        $task_submission->ts_content = $request->input('content');
        $task_submission->ts_start_latitude = $request->input('start_latitude');
        $task_submission->ts_start_longitude = $request->input('start_longitude');
        $task_submission->ts_actual_start_time = $start_time;
        $task_submission->ts_status = 'inProgress'; // default

        if ($task_submission->save()) {
            // add the media ...

            //$file_name = $this->fileUploadService->uploadFile($request->file('p_video'), 'posts/videos');

            return response()->json([
                'status' => true,
                'message' => 'تم تسليم المهمة بنجاح',
                'task_submission' => $task_submission
            ], 200);
        }
    }
}
