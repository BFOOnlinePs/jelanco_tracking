<?php

namespace App\Http\Controllers\ApiControllers\task_submission_controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class TaskSubmissionController extends Controller
{  
    public function addTaskSubmission(Request $request){
        $validator = Validator::make($request->all(), [
           'task_id' => 'required|exists:tasks,t_id',
           'content' => 'required',
           'file' => 'nullable|mimes:png,jpg,jpeg,pdf', //
        //    'status' => 'required|in:active,notActive'

        ]);

        // submitter from auth
        // actual_start_time
        // actual_end_time
        // start_latitude
        // start_longitude
        //
        //
    }


}
