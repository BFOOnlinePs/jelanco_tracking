<?php

namespace App\Http\Controllers\ApiControllers\user_controllers;

use App\Http\Controllers\Controller;
use App\Models\TaskSubmissionsModel;
use App\Models\User;
use App\Services\SubmissionService;
use Illuminate\Http\Request;

class userController extends Controller
{
    protected $submissionService;
    public function __construct(SubmissionService $submissionService)
    {
        $this->submissionService = $submissionService;
    }

    public function getUserProfileById($user_id)
    {
        $user_info = User::find($user_id);

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

        // $user_info->submissions = $submissions_with_tasks->values();

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

    // only id and name
    public function getAllUsers()
    {
        $users = User::select('id', 'name')->get();

        return response()->json([
            'status' => true,
            'users' => $users
        ]);
    }
}
