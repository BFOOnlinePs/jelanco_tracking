<?php

namespace App\Http\Controllers;

use App\Models\AttachmentsModel;
use App\Models\TaskCategoriesModel;
use App\Models\TaskModel;
use App\Models\TaskSubmissionCommentsModel;
use App\Models\User;
use App\Services\FileUploadService;
use GuzzleHttp\Psr7\UploadedFile;
use Illuminate\Http\Request;

class TasksController extends Controller
{
    protected $fileUploadService;

    public function __construct(FileUploadService $fileUploadService)
    {
        $this->fileUploadService = $fileUploadService;
    }

    public function index()
    {
//        $task = TasksModel::with('category','added_by','submissions.submitter' , 'submissions.comments.comment_by')->get();
//        return $task;
        $users = User::all();
        return view('project.tasks.index' , ['users' => $users]);
    }

    public function list_tasks_ajax(Request $request)
    {
        $data = TaskModel::query();
        if ($request->filled('task_name')){
            $data->where('t_content','like','%'.$request->task_name.'%');
        }
        if ($request->filled('task_status')){
            $data->where('t_status',$request->task_status);
        }
        if ($request->filled('assigned_to')){
            $data->whereJsonContains('t_assigned_to',$request->assigned_to);
        }
        if ($request->filled('task_start_time') && $request->filled('task_end_time')) {
            $data->whereBetween('t_planed_start_time', [$request->task_start_time, $request->task_end_time]);
            $data->whereBetween('t_planed_end_time', [$request->task_start_time, $request->task_end_time]);
        } elseif ($request->filled('task_start_time')) {
            $data->where('t_planed_start_time', '>=', $request->task_start_time);
        } elseif ($request->filled('task_end_time')) {
            $data->where('t_planed_end_time', '<=', $request->task_end_time);
        }
        $data = $data->with('taskCategory','addedByUser','submissions.submitter' , 'submissions.comments.comment_by' , 'submissions.comments.attachments')->orderBy('created_at')->paginate(5);
        return response()->json([
            'success' => true,
            'view' => view('project.tasks.ajax.list_tasks_ajax', ['data'=>$data])->render(),
        ]);
    }

    public function add()
    {
        $task_category = TaskCategoriesModel::get();
        $clients = User::get();
        return view('project.tasks.add',['task_category'=>$task_category , 'clients'=>$clients]);
    }

    public function create(Request $request)
    {
        $data = new TaskModel();
        $data->t_content = $request->t_content;
        $data->t_planed_start_time = $request->t_planed_start_time;
        $data->t_planed_end_time = $request->t_planed_end_time;
        $data->t_status = 'active';
        $data->t_category_id = $request->t_category_id;
        $data->t_added_by = auth()->user()->id;
        $data->t_assigned_to = json_encode($request->clients);
        if ($data->save()){
            return redirect()->route('tasks.index')->with(['success'=>'تم اضافة المهمة بنجاح']);
        }
        else{
            return redirect()->route('tasks.index')->with(['fail'=>'هناك خلل ما لم يتم اضافة المهمة بنجاح']);
        }
    }

    public function edit($id)
    {
        $data = TaskModel::find($id);
        $task_category = TaskCategoriesModel::get();
        $clients = User::get();
        return view('project.tasks.edit',['data'=>$data , 'task_category'=>$task_category , 'clients'=>$clients]);
    }

    public function update (Request $request)
    {
        $data = TaskModel::where('t_id',$request->id)->first();
        $data->t_content = $request->t_content;
        $data->t_planed_start_time = $request->t_planed_start_time;
        $data->t_planed_end_time = $request->t_planed_end_time;
        $data->t_status = $request->t_status;
        $data->t_category_id = $request->t_category_id;
        $data->t_added_by = auth()->user()->id;
        $data->t_assigned_to = json_encode($request->clients);
        if ($data->save()){
            return redirect()->route('tasks.index')->with(['success'=>'تم تعديل المهمة بنجاح']);
        }
        else{
            return redirect()->route('tasks.index')->with(['fail'=>'هناك خلل ما لم يتم تعديل المهمة بنجاح']);
        }
    }

    public function task_submission_index($task_id)
    {
        return view('project.tasks.task_submission.index');
    }

    public function add_comment_ajax(Request $request)
    {
        // return response()->json($request->image);
        $data = new TaskSubmissionCommentsModel();
        $data->tsc_task_id = $request->tsc_task_id;
        $data->tsc_task_submission_id = $request->tsc_task_submission_id;
        $data->tsc_parent_id = -1;
        $data->tsc_commented_by = auth()->user()->id;
        $data->tsc_content = $request->tsc_content;
        $client = User::where('id',auth()->user()->id)->first();
        if ($data->save()){
            if($request->has('image')){
                foreach ($request->image as $key){
                    $attachment = new AttachmentsModel();
                    $attachment->a_table = 'task_submission_comments';
                    $attachment->a_fk_id = $data->tsc_id;
                    $attachment->a_attachment = $this->fileUploadService->uploadFile($key,'comments_attachments');
                    $attachment->a_user_id = auth()->user()->id;
                    $attachment->save();
                }
            }
            if($request->has('video')){
                foreach ($request->video as $key){
                    $attachment = new AttachmentsModel();
                    $attachment->a_table = 'task_submission_comments';
                    $attachment->a_fk_id = $data->tsc_id;
                    $attachment->a_attachment = $this->fileUploadService->uploadFile($key,'comments_attachments');
                    $attachment->a_user_id = auth()->user()->id;
                    $attachment->save();
                }
            }
            if($request->has('file')){
                foreach ($request->file as $key){
                    $attachment = new AttachmentsModel();
                    $attachment->a_table = 'task_submission_comments';
                    $attachment->a_fk_id = $data->tsc_id;
                    $attachment->a_attachment = $this->fileUploadService->uploadFile($key,'comments_attachments');
                    $attachment->a_user_id = auth()->user()->id;
                    $attachment->save();
                }
            }
            return response()->json([
                'success' => true,
                'message' => 'تم اضافة التعليق بنجاح',
                'data' => $data,
                'client' => $client,
                'attachments' => AttachmentsModel::where('a_table','task_submission_comments')->where('a_fk_id',$data->tsc_id)->get()
            ]);
        }
    }

    public function get_comment_ajax(Request $request)
    {
        $data = TaskSubmissionCommentsModel::query();
        $data->where('tsc_task_submission_id',$request->tsc_task_submission_id);
        $data = $data->get();
        return response()->json([
            'success' => true,
            'data' => $data
        ]);
    }

}
