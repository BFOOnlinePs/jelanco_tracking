<?php

namespace App\Http\Controllers\ApiControllers\task_controllers;

use App\Helpers\SystemPermissions;
use App\Http\Controllers\Controller;
use App\Models\AttachmentsModel;
use App\Models\FcmRegistrationTokensModel;
use App\Models\TaskModel;
use App\Models\TaskSubmissionsModel;
use App\Models\User;
use App\Services\MediaService;
use App\Services\SubmissionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use App\Services\FcmService as ServicesFcmService;
use App\Services\FileUploadService;
use App\Services\VideoThumbnailService;
use PSpell\Config;

class TaskController extends Controller
{
    protected $mediaService;
    protected $thumbnailService;
    protected $submissionService;
    protected $fcmService;
    protected $fileUploadService;

    public function __construct(
        FileUploadService $fileUploadService,
        VideoThumbnailService $thumbnailService,
        MediaService $mediaService,
        SubmissionService $submissionService,
        ServicesFcmService $fcmService
    ) {
        $this->mediaService = $mediaService;
        $this->thumbnailService = $thumbnailService;
        $this->submissionService = $submissionService;
        $this->fcmService = $fcmService;
        $this->fileUploadService = $fileUploadService;
    }

    private function handleAttachmentsUpload($files, $task)
    {
        foreach ($files as $file) {
            $attachment = new AttachmentsModel();
            $folderPath = config('constants.tasks_attachments_path');
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

            $attachment->a_table = 'tasks';
            $attachment->a_fk_id = $task->t_id;
            $attachment->a_attachment = $file_name;
            $attachment->a_user_id = auth()->user()->id;

            $attachment->save();
        }
    }


    // for edit
    private function handleOldAttachments($files_names, $task)
    {
        foreach ($files_names as $file_name) {
            $attachment = new AttachmentsModel();
            // $folderPath = 'uploads';
            // $file_name = $this->fileUploadService->uploadFile($file, $folderPath);

            $attachment->a_table = 'tasks';
            $attachment->a_fk_id = $task->t_id;
            $attachment->a_attachment = $file_name;
            $attachment->a_user_id = auth()->user()->id;

            $attachment->save();
        }
    }


    public function getAllTasks()
    {
        $tasks = TaskModel::get();

        return $tasks;
    }

    // task details screen
    public function getTaskWithSubmissionsAndComments($id)
    {
        $task = TaskModel::where('t_id', $id)
            ->with('taskCategory:c_id,c_name')
            ->with('addedByUser:id,name,image')
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
                ->orderBy('created_at', 'desc')
                ->get();

            $task->assigned_to_users = User::whereIn('id', json_decode($task->t_assigned_to))->select('id', 'name')->get();

            $task->task_attachments_categories = $this->mediaService->getMedia('tasks', $task->t_id);


            $task->task_submissions->transform(function ($submission) {
                // to get the submitter user
                $user_id = $submission->ts_submitter;
                $user = User::where('id', $user_id)->select('id', 'name', 'image')->first();
                $submission->submitter_user = $user;

                // if the user has the permission
                if (SystemPermissions::hasPermission(SystemPermissions::VIEW_COMMENTS)) {
                    $submission->submission_comments = $this->submissionService->getSubmissionComments($submission);
                }


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
            // paginate
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
            'images.*' => 'image|mimes:jpg,png,jpeg,gif,svg',
            'videos.*' => 'mimetypes:video/mp4',
            'documents.*' => 'mimes:pdf,doc,docx,xls,xlsx,ppt,pptx',
        ], [
            'images.*.image' => 'يجب اني يكون الملف نوعه صورة',
            'images.*.mimes' => 'يجب ان يكون نوع الصور: jpg, jpeg, png, gif, svg.',
            'videos.*.mimetypes' => 'يجب أن يكون نوع الفيديو: mp4',
            'documents.*.mimes' => 'يجب أن يكون نوع الملفات أحد الأنواع التالية: pdf, doc, docx, xls, xlsx, ppt, pptx.',
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
            // add the media

            if ($request->hasFile('images')) {
                $this->handleAttachmentsUpload($request->images, $task,);
            }

            if ($request->hasFile('videos')) {
                $this->handleAttachmentsUpload($request->videos, $task,);
            }

            if ($request->hasFile('documents')) {
                $this->handleAttachmentsUpload($request->documents, $task,);
            }



            $users_id = json_decode($task->t_assigned_to);

            $tokens = FcmRegistrationTokensModel::whereIn('frt_user_id', $users_id) // Match tokens for the user
                ->pluck('frt_registration_token') // Get all registration tokens
                ->toArray();

            Log::info('FCM Tokens:', $tokens);

            if (!empty($tokens)) {
                // Loop through tokens and send message
                foreach ($tokens as $token) {
                    $this->fcmService->sendNotification(
                        'تم إسناد تكليف جديد',
                        $task->t_content,
                        $token,
                        config('constants.notification_type.task'),
                        $task->t_id
                    );
                }
            }

            return response()->json([
                'status' => true,
                'message' => 'تم إضافة المهمة بنجاح',
                'task' => $task,
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
            'status' => 'required|in:active,notActive',
            'images.*' => 'image|mimes:jpg,png,jpeg,gif,svg',
            'videos.*' => 'mimetypes:video/mp4',
            'documents.*' => 'mimes:pdf,doc,docx,xls,xlsx,ppt,pptx',
            'old_attachments' => 'nullable', // when edit, this contains the old attachments remaining
            'old_attachments.*' => 'string',
        ], [
            'images.*.image' => 'يجب اني يكون الملف نوعه صورة',
            'images.*.mimes' => 'يجب ان يكون نوع الصور: jpg, jpeg, png, gif, svg.',
            'videos.*.mimetypes' => 'يجب أن يكون نوع الفيديو: mp4',
            'documents.*.mimes' => 'يجب أن يكون نوع الملفات أحد الأنواع التالية: pdf, doc, docx, xls, xlsx, ppt, pptx.',

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

            // add the media ...

            if ($request->hasFile('images')) {
                $this->handleAttachmentsUpload($request->images, $task,);
            }

            if ($request->hasFile('videos')) {
                $this->handleAttachmentsUpload($request->videos, $task,);
            }

            if ($request->hasFile('documents')) {
                $this->handleAttachmentsUpload($request->documents, $task,);
            }

            if ($request->has('old_attachments')) {
                $this->handleOldAttachments($request->old_attachments, $task,);
            }


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
