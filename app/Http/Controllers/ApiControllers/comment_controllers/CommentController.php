<?php

namespace App\Http\Controllers\ApiControllers\comment_controllers;

use App\Http\Controllers\Controller;
use App\Models\AttachmentsModel;
use App\Models\TaskSubmissionCommentsModel;
use App\Models\User;
use App\Services\FileUploadService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CommentController extends Controller
{
    protected $fileUploadService;

    // Inject the FileUploadService into the controller
    public function __construct(FileUploadService $fileUploadService)
    {
        $this->fileUploadService = $fileUploadService;
    }


    private function handleAttachmentsUpload($files, $fk_id) // $fk_id = task_submission_comment_id
    {
        foreach ($files as $file) {
            $attachment = new AttachmentsModel();
            $folderPath = 'comments_attachments';
            $file_name = $this->fileUploadService->uploadFile($file, $folderPath);

            $attachment->a_table = 'task_submission_comments';
            $attachment->a_fk_id = $fk_id;
            $attachment->a_attachment = $file_name;
            $attachment->a_user_id = auth()->user()->id;

            $attachment->save();
        }
    }

    public function addTaskSubmissionComment(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'task_id' => 'required|int',
            'task_submission_id' => 'required|int',
            'parent_id' => 'required|int', // when reply
            'comment_content' => 'required',
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

        $current_user = auth()->user();

        $comment = new TaskSubmissionCommentsModel();
        $comment->tsc_task_id = (int) $request->input('task_id');
        $comment->tsc_task_submission_id = (int) $request->input('task_submission_id');
        $comment->tsc_parent_id = (int) $request->input('parent_id') ?? -1; // -1 parent
        $comment->tsc_commented_by = $current_user->id;
        $comment->tsc_content = $request->input('comment_content');

        if ($comment->save()) {

            $comment->commented_by_user = User::where('id', $current_user->id)->select('id', 'name')->first();

            if ($request->hasFile('images')) {
                $this->handleAttachmentsUpload($request->images, $comment->tsc_id,);
            }

            if ($request->hasFile('videos')) {
                $this->handleAttachmentsUpload($request->videos, $comment->tsc_id,);
            }

            if ($request->hasFile('documents')) {
                $this->handleAttachmentsUpload($request->documents, $comment->tsc_id,);
            }


            return response()->json([
                'status' => true,
                'message' => 'تم إضافة التعليق بنجاح',
                'comment' => $comment
            ]);
        }
    }
}
