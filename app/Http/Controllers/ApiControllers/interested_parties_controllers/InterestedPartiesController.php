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

    public function getInterestedParties(Request $request)
    {
        // query params
        $validator = Validator::make($request->all(), [
            'article_type' => 'required|string|in:task,submission',
            'article_id' => 'required|integer',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => $validator->errors()->first()
            ], 422);
        }

        return response()->json([
            'status' => true,
            'interested_parties' => $this->InterestedPartiesService->getInterestedParties($request->article_type, $request->article_id),
        ]);
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

        // Log::info('interested_party_ids' . $request->interested_party_ids);

        $this->InterestedPartiesService->addRemoveInterestedParties($request->article_type, $request->article_id, $request->interested_party_ids);

        return response()->json([
            'status' => true,
            'message' => 'تم تحديث الجهات المعنية بنجاح '
        ]);
    }

    public function getArticlesOfInterest(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'interested_party_id' => 'required|integer|exists:users,id',
            'article_type' => 'required|string|in:task,submission',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => $validator->errors()->first()
            ], 422);
        }

        $articles_of_interest = $this->InterestedPartiesService->getArticlesOfInterest($request->interested_party_id, $request->article_type);

        return response()->json([
            'status' => true,
            'pagination' => [
                'current_page' => $articles_of_interest->currentPage(),
                'last_page' => $articles_of_interest->lastPage(),
                'per_page' => $articles_of_interest->perPage(),
                'total_items' => $articles_of_interest->total(),
            ],
            'articles_of_interest' => $articles_of_interest->values(),
        ]);
    }
}
