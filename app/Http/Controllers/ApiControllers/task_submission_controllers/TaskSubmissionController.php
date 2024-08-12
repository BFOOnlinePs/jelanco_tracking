<?php

namespace App\Http\Controllers\ApiControllers\task_submission_controllers;

use App\Http\Controllers\Controller;
use App\Models\AttachmentsModel;
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

    private function handleAttachmentsUpload($files, $task_submission)
    {
        foreach ($files as $file) {
            $attachment = new AttachmentsModel();
            $folderPath = 'uploads';
            $file_name = $this->fileUploadService->uploadFile($file, $folderPath);

            $attachment->a_table = 'task_submissions';
            $attachment->a_fk_id = $task_submission->ts_id;
            $attachment->a_attachment = $file_name;
            $attachment->a_user_id = auth()->user()->id;

            $attachment->save();
        }
    }

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
            'task_id' => 'required|exists:tasks,t_id',
            'content' => 'required',
            'start_latitude' => 'required',
            'start_longitude' => 'required',
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

            return response()->json([
                'status' => true,
                'message' => 'تم تسليم المهمة بنجاح',
                'task_submission' => $task_submission
            ], 200);
        }
    }

    public function getTaskSubmission($id){
        // $validator = Validator::make($request->all(), [
        //     'task_submission_id' => 'required|exists:task_submissions,ts_id',
        // ]);

        // if ($validator->fails()) {
        //     return response()->json([
        //         'status' => false,
        //         'message' => $validator->errors()->first(),
        //     ], 422);
        // }

        // if id not exist

        $task_submission = TaskSubmissionsModel::where('ts_id', $id)->first();

        return response()->json([
            'status' => true,
            'task_submission' => $task_submission
        ], 200);
    }
}
