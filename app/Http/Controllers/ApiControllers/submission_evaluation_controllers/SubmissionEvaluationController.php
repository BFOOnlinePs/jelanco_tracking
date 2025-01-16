<?php

namespace App\Http\Controllers\ApiControllers\submission_evaluation_controllers;

use App\Http\Controllers\Controller;
use App\Models\SubmissionEvaluationModel;
use App\Models\TaskModel;
use App\Models\TaskSubmissionsModel;
use App\Services\SubmissionStatusService;
use App\Services\TaskStatusService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class SubmissionEvaluationController extends Controller
{
    protected $taskStatusService;
    protected $submissionStatusService;

    public function __construct(TaskStatusService $taskStatusService, SubmissionStatusService $submissionStatusService)
    {
        $this->taskStatusService = $taskStatusService;
        $this->submissionStatusService = $submissionStatusService;
    }

    public function evaluate(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'task_id' => 'nullable|exists:tasks,t_id',
            'task_submission_id' => 'required|exists:task_submissions,ts_id',
            'rating' => 'required|numeric|min:0|max:5',
            'evaluator_notes' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => $validator->errors()->first(),
            ], 400);
        }


        $evaluation = SubmissionEvaluationModel::create([
            'se_task_id' => $request->task_id,
            'se_submission_id' => $request->task_submission_id,
            'se_evaluator_id' => auth()->user()->id,
            'se_rating' => $request->rating,
            'se_evaluator_notes' => $request->evaluator_notes
        ]);

        if ($request->has('task_id') && $request->task_id) {
            // Update task status
            $task = TaskModel::where('t_id', $request->task_id)->first();
            $this->taskStatusService->updateTaskStatus($task);
        } else {
            // update submission status
            $taskSubmission = TaskSubmissionsModel::where('ts_id', $request->task_submission_id)->first();
            $this->submissionStatusService->updateSubmissionStatus($taskSubmission);
        }

        return response()->json([
            'status' => true,
            'message' => 'تم التقييم بنجاح',
            'evaluation' => $evaluation
        ], 200);
    }
}
