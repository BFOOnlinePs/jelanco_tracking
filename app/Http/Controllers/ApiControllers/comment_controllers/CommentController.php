<?php

namespace App\Http\Controllers\ApiControllers\comment_controllers;

use App\Http\Controllers\Controller;
use App\Models\AttachmentsModel;
use App\Models\FcmRegistrationTokensModel;
use App\Models\TaskSubmissionCommentsModel;
use App\Models\TaskSubmissionsModel;
use App\Models\User;
use App\Services\FileUploadService;
use App\Services\MediaService;
use App\Services\SubmissionService;
use App\Services\VideoThumbnailService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use App\Services\FcmService as ServicesFcmService;

class CommentController extends Controller
{
    protected $mediaService;
    protected $thumbnailService;
    protected $fileUploadService;
    protected $submissionService;
    protected $fcmService;

    // Inject the FileUploadService, thumbnailService and MediaService into the controller
    public function __construct(
        FileUploadService $fileUploadService,
        VideoThumbnailService $thumbnailService,
        MediaService $mediaService,
        SubmissionService $submissionService,
        ServicesFcmService $fcmService
    ) {
        $this->fileUploadService = $fileUploadService;
        $this->thumbnailService = $thumbnailService;
        $this->mediaService = $mediaService;
        $this->submissionService = $submissionService;
        $this->fcmService = $fcmService;
    }

    private function handleAttachmentsUpload($files, $fk_id) // $fk_id = task_submission_comment_id
    {
        foreach ($files as $file) {
            $attachment = new AttachmentsModel();
            $folderPath = 'comments_attachments';
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
            'parent_id' => 'required|int', // when reply - not used yet
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

        // // Find the parent submission to add the comment on it
        // $parent_submission = TaskSubmissionsModel::where('ts_id', $request->input('task_submission_id'))->first();
        // // Traverse upwards until we find the submission with parent_id = -1
        // while ($parent_submission && $parent_submission->ts_parent_id != -1) {
        //     $parent_submission = TaskSubmissionsModel::where('ts_id', $parent_submission->ts_parent_id)->first();
        // }

        $current_user = auth()->user();

        $comment = new TaskSubmissionCommentsModel();
        $comment->tsc_task_id = (int) $request->input('task_id');
        $comment->tsc_task_submission_id = (int) $request->input('task_submission_id');
        $comment->tsc_parent_id = (int) $request->input('parent_id') ?? -1; // -1 parent
        $comment->tsc_commented_by = $current_user->id;
        $comment->tsc_content = $request->input('comment_content');

        if ($comment->save()) {

            $comment->commented_by_user = User::where('id', $current_user->id)->select('id', 'name', 'image')->first();

            if ($request->hasFile('images')) {
                $this->handleAttachmentsUpload($request->images, $comment->tsc_id,);
            }

            if ($request->hasFile('videos')) {
                $this->handleAttachmentsUpload($request->videos, $comment->tsc_id,);
            }

            if ($request->hasFile('documents')) {
                $this->handleAttachmentsUpload($request->documents, $comment->tsc_id,);
            }

            $comment_media = $this->mediaService->getMedia('task_submission_comments', $comment->tsc_id);

            $comment->comment_attachments_categories = $comment_media;


            try {
                // Emit the event to the Socket.IO server
                $this->emitSocketIOEvent($comment);
            } catch (Exception $e) {
            };



            // send notification

            // id of the user how added the submission
            $user_id = TaskSubmissionsModel::where('ts_id', $comment->tsc_task_submission_id)
                ->value('ts_submitter');

            $tokens = FcmRegistrationTokensModel::where('frt_user_id', $user_id) // Match tokens for the user
                ->pluck('frt_registration_token') // Get all registration tokens
                ->toArray();

            Log::info('FCM Tokens for comment:', $tokens);

            if (!empty($tokens)) {
                // Loop through tokens and send message
                foreach ($tokens as $token) {
                    $this->fcmService->sendNotification(
                        'تم إضافة تعليق من قبل ' . auth()->user()->name,
                        $comment->tsc_content,
                        $token,
                        config('constants.notification_type.comment'),
                        $comment->tsc_task_submission_id // id of the submission
                    );
                }
            }


            return response()->json([
                'status' => true,
                'message' => 'تم إضافة التعليق بنجاح',
                'comment' => $comment
            ]);
        }
    }


    protected function emitSocketIOEvent($comment)
    {
        $client = new \GuzzleHttp\Client();
        $url = config('constants.socket_io_url');
        $client->post($url, [
            'json' => [
                'event' => 'new-comment',
                // 'dataaaa' => $comment
            ]
        ]);
    }
    public function getSubmissionComments($id)
    {
        $submission = TaskSubmissionsModel::where('ts_id', $id)->first();

        $submission_comments = $this->submissionService->getSubmissionComments($submission);

        return response()->json([
            'status' => true,
            'submission_comments' => $submission_comments
        ]);
    }

    public function getSubmissionCommentCount($id)
    {
        $comments_count = $this->submissionService->getCommentCountTillParent($id);

        return response()->json([
            'status' => true,
            'comments_count' => $comments_count
        ], 200);
    }
}
