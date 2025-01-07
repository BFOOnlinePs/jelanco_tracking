<?php

namespace App\Http\Controllers\ApiControllers\interested_parties_controllers;

use App\Http\Controllers\Controller;
use App\Models\InterestedPartyModel;
use App\Models\TaskModel;
use App\Models\TaskSubmissionsModel;
use App\Services\InterestedPartiesService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class InterestedPartiesController extends Controller
{
    protected $InterestedPartiesService;

    public function __construct(InterestedPartiesService $InterestedPartiesService)
    {
        $this->InterestedPartiesService = $InterestedPartiesService;
    }

    public function handleInterestedParties(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'article_type' => 'required|string|in:task,submission',
            'article_id' => [
                'required',
                'integer',
                function ($attribute, $value, $fail) use ($request) {
                    Log::info("article id: $value");
                    Log::info("request->article_type: " . $request->article_type);
                    if ($request->article_type == 'task') {
                        $taskExists = TaskModel::where('t_id', $value)->exists();
                        if (!$taskExists) {
                            return $fail('The article ID must exist in the tasks table.');
                        }
                    } elseif ($request->article_type == 'submission') {
                        $submissionExists = TaskSubmissionsModel::where('ts_id', $value)->exists();
                        if (!$submissionExists) {
                            return $fail('The article ID must exist in the task submissions table.');
                        }
                    } else {
                        return $fail('Invalid article type provided.');
                    }
                },
            ], // Make sure article exists
            'interested_party_ids' => 'array',
            'interested_party_ids.*' => 'integer|exists:users,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => $validator->errors()->first()
            ], 422);
        }

        $this->InterestedPartiesService->addRemoveInterestedParties($request->article_type, $request->article_id, $request->interested_party_ids);

        return response()->json([
            'status' => true,
            'message' => 'تم تحديث الأطراف المعنية بنجاح '
        ]);
    }
}
