<?php

namespace App\Services;

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
            // Get the submitter user
            $user = User::where('id', $submission->ts_submitter)
                ->select('id', 'name')
                ->first();
            $submission->submitter_user = $user;


            $submission->comments_count = $this->getCommentCountTillParent($submission->ts_id);

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
        });

        return $submissions;
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