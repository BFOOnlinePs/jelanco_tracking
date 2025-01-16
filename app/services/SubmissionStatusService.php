<?php


namespace App\Services;

class SubmissionStatusService
{
    public function updateSubmissionStatus($submission, $status = null)
    {
        if ($status) { // accepted or rejected
            $submission->update(['ts_status' => $status]);
        }

        if ($submission->ts_status == 'rejected') {
            return;
        }

        if ($submission->evaluations->count() > 0) {
            $submission->update(['ts_status' => 'evaluated']);
            return;
        }

    }
}
