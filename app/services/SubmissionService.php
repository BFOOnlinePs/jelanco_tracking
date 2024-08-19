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
     * - `submission_comments`: Comments on the submission (including the user who commented and their attachments).
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

            // Find the first submission of each version chain
            $parent_submission = $submission;
            while ($parent_submission && $parent_submission->ts_parent_id != -1) {
                $parent_submission = TaskSubmissionsModel::where('ts_id', $parent_submission->ts_parent_id)->first();
            }

            // Get the comments of the parent submission
            $submission->submission_comments = TaskSubmissionCommentsModel::where('tsc_task_submission_id', $parent_submission->ts_id)->get();
            $submission->submission_comments->transform(function ($comment) {
                $comment->commented_by_user = User::where('id', $comment->tsc_commented_by)
                                                  ->select('id', 'name')
                                                  ->first();

                // Get media for each comment
                $comment->comment_attachments_categories = $this->mediaService->getMedia('task_submission_comments', $comment->tsc_id);

                return $comment;
            });

            // Get media for the submission
            $submission->submission_attachments_categories = $this->mediaService->getMedia('task_submissions', $submission->ts_id);

            return $submission;
        });

        return $submissions;
    }
}
