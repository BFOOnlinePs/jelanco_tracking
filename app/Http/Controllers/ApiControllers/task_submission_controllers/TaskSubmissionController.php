<?php

namespace App\Http\Controllers\ApiControllers\task_submission_controllers;

use App\Helpers\SystemPermissions;
use App\Http\Controllers\Controller;
use App\Models\AttachmentsModel;
use App\Models\TaskModel;
use App\Models\TaskSubmissionsModel;
use App\Models\User;
use App\Services\FileUploadService;
use App\Services\ManagerEmployeesService;
use App\Services\MediaService;
use App\Services\SubmissionService;
use App\Services\VideoThumbnailService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class TaskSubmissionController extends Controller
{
    protected $mediaService;
    protected $thumbnailService;
    protected $fileUploadService;
    protected $submissionService;
    protected $managerEmployeesService;

    // Inject the FileUploadService, thumbnailService and MediaService into the controller
    public function __construct(FileUploadService $fileUploadService, VideoThumbnailService $thumbnailService, MediaService $mediaService, SubmissionService $submissionService, ManagerEmployeesService $managerEmployeesService)
    {
        $this->fileUploadService = $fileUploadService;
        $this->thumbnailService = $thumbnailService;
        $this->mediaService = $mediaService;
        $this->submissionService = $submissionService;
        $this->managerEmployeesService = $managerEmployeesService;
    }


    private function handleAttachmentsUpload($files, $task_submission)
    {
        foreach ($files as $file) {
            $attachment = new AttachmentsModel();
            $folderPath = 'uploads';
            $file_name = $this->fileUploadService->uploadFile($file, $folderPath);

            // Check if file is a video based on extension, then add thumbnail
            $allowedVideoExtensions = config('filetypes.video_types');
            $extension = $file->getClientOriginalExtension();
            if (in_array($extension, $allowedVideoExtensions)) {
                $fileNameWithoutExtension = pathinfo($file_name, PATHINFO_FILENAME);
                $thumbnail_file_name = $fileNameWithoutExtension . '.' . config('constants.thumbnail_extension');
                $this->thumbnailService->generateThumbnail(
                    storage_path('app/public/' . $folderPath . '/' . $file_name),
                    storage_path('app/public/thumbnails/' . $thumbnail_file_name),
                );
            }

            $attachment->a_table = 'task_submissions';
            $attachment->a_fk_id = $task_submission->ts_id;
            $attachment->a_attachment = $file_name;
            $attachment->a_user_id = auth()->user()->id;

            $attachment->save();
        }
    }

    // for edit
    private function handleOldAttachments($files_names, $task_submission)
    {
        foreach ($files_names as $file_name) {
            $attachment = new AttachmentsModel();
            // $folderPath = 'uploads';
            // $file_name = $this->fileUploadService->uploadFile($file, $folderPath);

            $attachment->a_table = 'task_submissions';
            $attachment->a_fk_id = $task_submission->ts_id;
            $attachment->a_attachment = $file_name;
            $attachment->a_user_id = auth()->user()->id;

            $attachment->save();
        }
    }

    public function addTaskSubmission(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'parent_id' => 'int', // -1 when no parent (the original/first submission)
            'task_id' => 'required', // -1 when submission has no parent
            'content' => 'required',
            'categories' => 'nullable',
            'start_latitude' => 'required',
            'start_longitude' => 'required',
            'images.*' => 'image|mimes:jpg,png,jpeg,gif,svg',
            'videos.*' => 'mimetypes:video/mp4',
            'documents.*' => 'mimes:pdf,doc,docx,xls,xlsx,ppt,pptx',
            'old_attachments' => 'nullable', // when edit, this contains the old attachments remaining
            'old_attachments.*' => 'string',
        ], [
            'start_latitude.required' => 'الرجاء تشغيل الموقع',
            'images.*.image' => 'يجب اني يكون الملف نوعه صورة',
            'images.*.mimes' => 'يجب ان يكون نوع الصور: jpg, jpeg, png, gif, svg.',
            'videos.*.mimetypes' => 'يجب أن يكون نوع الفيديو: mp4',
            'documents.*.mimes' => 'يجب أن يكون نوع الملفات أحد الأنواع التالية: pdf, doc, docx, xls, xlsx, ppt, pptx.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => $validator->errors()->first(),
            ], 422);
        }

        // submitter from auth
        $submitter = auth()->user()->id;
        $start_time = Carbon::now();

        $task_submission = new TaskSubmissionsModel();
        $task_submission->ts_parent_id = (int) $request->input('parent_id');
        $task_submission->ts_task_id = (int) $request->input('task_id');
        $task_submission->ts_submitter = $submitter;
        $task_submission->ts_content = $request->input('content');
        $task_submission->ts_categories = $request->input('categories');
        $task_submission->ts_start_latitude = $request->input('start_latitude');
        $task_submission->ts_start_longitude = $request->input('start_longitude');
        $task_submission->ts_actual_start_time = $start_time;
        $task_submission->ts_status = 'accepted'; // default

        if ($task_submission->save()) {
            // add the media ...

            if ($request->hasFile('images')) {
                $this->handleAttachmentsUpload($request->images, $task_submission,);
            }

            if ($request->hasFile('videos')) {
                $this->handleAttachmentsUpload($request->videos, $task_submission,);
            }

            if ($request->hasFile('documents')) {
                $this->handleAttachmentsUpload($request->documents, $task_submission,);
            }

            if ($request->has('old_attachments')) {
                $this->handleOldAttachments($request->old_attachments, $task_submission,);
            }

            // $submission_media = $this->mediaService->getMedia('task_submissions', $task_submission->ts_id);

            // $task_submission->submission_attachments_categories = $submission_media;



            $this->submissionService->processSubmission($task_submission);

            $processed_submissions = $this->submissionService->getSubmissionTask($task_submission);


            return response()->json([
                'status' => true,
                'message' => 'تم تسليم المهمة بنجاح',
                'task_submission' => $processed_submissions
            ], 200);
        }
    }

    /**
     * Get task submission versions
     *
     * @param integer $id, id of submission
     * @return void
     */
    public function getTaskSubmissionVersions($id)
    {
        $submissions_versions = [];
        $currentSubmission = TaskSubmissionsModel::where('ts_id', $id)->first();

        // Traverse the versions upwards until we reach the first submission (parent_id = -1)
        while ($currentSubmission && $currentSubmission->ts_parent_id != -1) {
            $submissions_versions[] = $currentSubmission;
            $currentSubmission = TaskSubmissionsModel::where('ts_id', $currentSubmission->ts_parent_id)->first();
        }

        // Add the first submission (with parent_id = -1)
        if ($currentSubmission) {
            $submissions_versions[] = $currentSubmission;
        }

        // Convert the array to a collection so we can use the transform method
        $submissions_versions = collect($submissions_versions);

        $submissions_versions->transform(function ($submission) {
            $submission_media = $this->mediaService->getMedia('task_submissions', $submission->ts_id);

            $submission->submission_attachments_categories = $submission_media;

            return $submission;
        });

        return response()->json([
            'status' => true,
            'submissions_versions' => $submissions_versions
        ]);
    }

    public function getTaskSubmissionWithTaskAndComments($id)
    {
        $task_submission = TaskSubmissionsModel::where('ts_id', $id)->first();

        $this->submissionService->processSubmission($task_submission, true);

        // Check if the submission has a task
        $this->submissionService->getSubmissionTask($task_submission, true);

        // get the comments
        $task_submission->submission_comments = $this->submissionService->getSubmissionComments($task_submission);

        return response()->json([
            'status' => true,
            'task_submission' => $task_submission
        ], 200);
    }



    // get submissions of the current user
    // + submissions of his employees (all submissions even if another manager add them) - if he has the permission
    // for home screen
    public function getUserSubmissions()
    {
        $user = auth()->user();

        $manager_employee_ids = [];

        // if he has the permission
        if (SystemPermissions::hasPermission(SystemPermissions::VIEW_MY_EMPLOYEES_SUBMISSIONS)) {
            $manager_employee_ids = $this->managerEmployeesService->getEmployeesByManagerId($user->id);
        }

        // Combine the user ID with the employee IDs
        $allSubmitters = array_merge([$user->id], $manager_employee_ids);

        // last version
        $submissions = TaskSubmissionsModel::whereIn('ts_submitter', $allSubmitters)
            ->whereNotIn('ts_id', function ($query) {
                $query->select('ts_parent_id')
                    ->from('task_submissions')
                    ->where('ts_parent_id', '!=', -1);
            })
            ->orderBy('created_at', 'desc')
            ->paginate(7);

        $this->submissionService->processSubmissions($submissions);

        // Check if the submission has a task
        $submissions_with_tasks = $submissions->map(function ($submission) {
            return $this->submissionService->getSubmissionTask($submission);
        });


        // they have the same length
        return response()->json([
            'status' => true,
            'pagination' => [
                'current_page' => $submissions->currentPage(),
                'last_page' => $submissions->lastPage(),
                'per_page' => $submissions->perPage(),
                'total_items' => $submissions->total(),
            ],
            'submissions' => $submissions_with_tasks->values(),
        ], 200);
    }


    public function getTodaysSubmissions()
    {
        // $perPage = $request->query('per_page', 10);

        $user_id = auth()->user()->id;

        // last version
        $submissions = TaskSubmissionsModel::where('ts_submitter', $user_id)
            ->whereDate('created_at', Carbon::today())
            ->whereNotIn('ts_id', function ($query) {
                $query->select('ts_parent_id')
                    ->from('task_submissions')
                    ->where('ts_parent_id', '!=', -1);
            })
            ->orderBy('created_at', 'desc')
            ->paginate(7);

        $this->submissionService->processSubmissions($submissions);

        // Check if the submission has a task
        $submissions_with_tasks = $submissions->map(function ($submission) {
            return $this->submissionService->getSubmissionTask($submission);
        });


        // Retrieve today's submissions by the current user,
        // only the last version
        // $submissions = TaskSubmissionsModel::where('ts_submitter', auth()->user()->id)
        //     ->whereDate('created_at', Carbon::today())
        //     ->whereNotIn('ts_id', function ($query) {
        //         $query->select('ts_parent_id')
        //             ->from('task_submissions')
        //             ->where('ts_parent_id', '!=', -1);
        //     })
        //     ->orderBy('created_at', 'desc')
        //     ->select('ts_id', 'ts_content' ,'created_at')
        //     ->paginate($perPage);


        return response()->json([
            'status' => true,
            'pagination' => [
                'current_page' => $submissions->currentPage(),
                'last_page' => $submissions->lastPage(),
                'per_page' => $submissions->perPage(),
                'total_items' => $submissions->total(),
            ],
            'submissions' => $submissions_with_tasks->values()
        ]);
    }
}
