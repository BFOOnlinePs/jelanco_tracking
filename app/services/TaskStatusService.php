<?php

namespace App\Services;

use App\Models\SubmissionEvaluationModel;
use App\Models\TaskModel;
use Illuminate\Support\Facades\Log;

class TaskStatusService
{
    protected $submissionService;

    public function __construct(SubmissionService $submissionService)
    {
        $this->submissionService = $submissionService;
    }

    /**
     * Update the status of a task based on submissions and evaluations.
     *
     * This function determines the appropriate status for the given task
     * based on the following conditions:
     *
     * - If the task is manually set to 'cancelled', the status will not be changed.
     * - If no submissions exist for the task, the status remains 'active'.
     * - If at least one submission exists, the status is set to 'inProgress'.
     * - If all assigned users have submitted at least one submission:
     *   - If all assigned users have at least one submission evaluated, the status is set to 'evaluated'.
     *   - Otherwise, the status is set to 'completed'.
     *
     * @param Task $task The task whose status needs to be updated.
     * @param string|null $status The status to set the task to.
     * @return void
     */

     // call when update task, add submission for the task, or evaluate submission
    public function updateTaskStatus($task, $status = null)
    {
        if ($status) {
            $task->update(['t_status' => $status]);
            return;
        }

        if ($task->t_status == 'canceled') {
            return;
        }

        Log::info("Task status not cancelled");

        // Get all assigned users and submissions
        $assignedUsers =  $this->submissionService->getAssignedUsers($task->t_assigned_to);
        $submissions = $task->submissions;

        if ($submissions->isEmpty()) {
            // No submissions yet, keep task as 'active'
            Log::info("Task status no submission");
            $task->update(['t_status' => 'active']);
            return;
        }

        Log::info("Task status has submission");

        // Check if the first submission has been made
        if ($submissions->count() > 0 && $task->t_status === 'active') {
            Log::info("Task status first submission");
            $task->update(['t_status' => 'inProgress']);
        }

        // Check if all assigned users have submitted at least one submission for the task
        $allUsersSubmitted = $assignedUsers->every(function ($user) use ($submissions) {
            return $submissions->where('ts_submitter', $user->id)->isNotEmpty();
        });

        Log::info("Task status all users submitted: " . $allUsersSubmitted);

        if ($allUsersSubmitted) {
            // Check if at least one submission for each user is evaluated
            $allUsersEvaluated = $assignedUsers->every(function ($user) use ($task) {
                $userSubmissions = $task->submissions->where('ts_submitter', $user->id)->pluck('ts_id');
                return SubmissionEvaluationModel::where('se_task_id', $task->t_id)
                    ->whereIn('se_submission_id', $userSubmissions)
                    ->exists();
            });

            Log::info("Task status all users evaluated: " . $allUsersEvaluated);

            if ($allUsersEvaluated) {
                Log::info("Task status all evaluated");
                $task->update(['t_status' => 'evaluated']);
            } else {
                Log::info("Task status all submitted");
                $task->update(['t_status' => 'completed']);
            }
        }
    }
}
