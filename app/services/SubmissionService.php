<?php

namespace App\Services;

use App\Models\TaskCategoriesModel;
use App\Models\TaskModel;
use App\Models\User;
use App\Models\TaskSubmissionsModel;
use App\Models\TaskSubmissionCommentsModel;
use App\Services\MediaService;

class SubmissionService
{
    protected $mediaService;

    public function __construct(MediaService $mediaService)
    {
        $this->mediaService = $mediaService;
    }


    /**
     * Process a collection of submissions to enrich them with additional data.
     *
     * This method transforms each submission in the provided collection by adding:
     * - The submitter's user details.
     * - The submission's parent chain until the root submission (with `ts_parent_id = -1`).
     * - Comments associated with the root submission, along with the user who commented and the comment media.
     * - Media attached to the submission.
     *
     * @param \Illuminate\Support\Collection $submissions The collection of task submissions to process.
     *
     * @return \Illuminate\Support\Collection The transformed collection of submissions, with additional data such as:
     * - `submitter_user`: The user who submitted the task.
     * - `submission_attachments_categories`: Media attached to the submission.
     */

    public function processSubmissions($submissions)
    {
        $submissions->getCollection()->transform(function ($submission) {
            $submission = $this->processSubmission($submission);
            return $submission;
        });

        return $submissions;
    }

    public function processSubmission($submission, $includeCategories = false)
    {
        // Get the submitter user
        $user = User::where('id', $submission->ts_submitter)
            ->select('id', 'name')
            ->first();

        $submission->submitter_user = $user;


        $submission->comments_count = $this->getCommentCountTillParent($submission->ts_id);

        // submission categories
        if ($includeCategories) {
            $submission->submission_categories = $this->getSubmissionCategories($submission->ts_categories);
        }
        // // Get the comments of the parent submission

        // Find the first submission of each version chain
        // $parent_submission = $submission;
        // while ($parent_submission && $parent_submission->ts_parent_id != -1) {
        //     $parent_submission = TaskSubmissionsModel::where('ts_id', $parent_submission->ts_parent_id)->first();
        // }


        // $submission->submission_comments = TaskSubmissionCommentsModel::where('tsc_task_submission_id', $parent_submission->ts_id)->get();
        // $submission->submission_comments->transform(function ($comment) {
        //     $comment->commented_by_user = User::where('id', $comment->tsc_commented_by)
        //         ->select('id', 'name')
        //         ->first();

        //     // Get media for each comment
        //     $comment->comment_attachments_categories = $this->mediaService->getMedia('task_submission_comments', $comment->tsc_id);

        //     return $comment;
        // });



        // Get media for the submission
        $submission->submission_attachments_categories = $this->mediaService->getMedia('task_submissions', $submission->ts_id);

        return $submission;
    }

    // check if submission has a task and return it
    public function getSubmissionTask($submission, $includeAssignedUsers = false)
    {
        if ($submission->ts_task_id != -1) {
            $task_details = TaskModel::where('t_id', $submission->ts_task_id)
                ->with('taskCategory:c_id,c_name')
                ->with('addedByUser:id,name')
                ->first();

            // Check if assigned users should be fetched
            if ($includeAssignedUsers) {
                $task_details->assigned_to_users = $this->getAssignedUsers($task_details->t_assigned_to);
            }

            $submission->task_details = $task_details;
        } else {
            $submission->task_details = null;
        }
        return $submission;
    }

    public function getAssignedUsers($assigned_to) // ex: ["1","2","3"] as String
    {
        // Decode the JSON string into an array
        $user_ids = json_decode($assigned_to, true);
        if (is_array($user_ids) && !empty($user_ids)) {
            return User::whereIn('id', $user_ids)->select('id', 'name')->get();
        }

        // Return an empty collection if $user_ids is not valid
        return collect();
    }

    public function getSubmissionCategories($categories) // ex: ["1","2","3"] as String
    {
        // Decode the JSON string into an array
        $category_ids = json_decode($categories, true);
        if (is_array($category_ids) && !empty($category_ids)) {
            return TaskCategoriesModel::whereIn('c_id', $category_ids)->select('c_id', 'c_name')->get();
        }
    }

    /**
     * Get the total count of comments for a task submission and all its parent submissions.
     *
     * @param int $task_submission_id ID of the task submission to start from.
     * @return int Total count of comments.
     */

    public function getCommentCountTillParent($task_submission_id)
    {
        $comment_count = 0;

        // Start with the current submission
        $current_submission = TaskSubmissionsModel::where('ts_id', $task_submission_id)->first();

        // Traverse upwards and accumulate comments for all submissions
        while ($current_submission) {
            // Count the comments on the current submission
            $comment_count += TaskSubmissionCommentsModel::where('tsc_task_submission_id', $current_submission->ts_id)->count();

            // Break if we've reached the parent submission (ts_parent_id = -1)
            if ($current_submission->ts_parent_id == -1) {
                break;
            }

            // Move upwards to the parent submission
            $current_submission = TaskSubmissionsModel::where('ts_id', $current_submission->ts_parent_id)->first();
        }

        return $comment_count;
    }


    public function getSubmissionComments($submission)
    {
        $all_submission_versions = collect([$submission]); // Start with the current submission
        $parent_submission = $submission;

        // Traverse upwards to collect all versions of the submission, including the parent with ts_parent_id = -1
        while ($parent_submission && $parent_submission->ts_parent_id != -1) {
            $parent_submission = TaskSubmissionsModel::where('ts_id', $parent_submission->ts_parent_id)->first();
            if ($parent_submission) {
                $all_submission_versions->push($parent_submission);
            }
        }

        // Gather the IDs of all versions of the submission
        $submission_ids = $all_submission_versions->pluck('ts_id');

        // Retrieve all comments related to these submission IDs
        $comments = TaskSubmissionCommentsModel::whereIn('tsc_task_submission_id', $submission_ids)->get();

        $comments->transform(function ($comment)  use ($submission) {
            $comment->commented_by_user = User::where('id', $comment->tsc_commented_by)->select('id', 'name')->first();
            $comment->comment_attachments_categories = $this->mediaService->getMedia('task_submission_comments', $comment->tsc_id);
            $comment->is_current_version = ($comment->tsc_task_submission_id == $submission->ts_id);

            return $comment;
        });

        return $comments;
    }
}
