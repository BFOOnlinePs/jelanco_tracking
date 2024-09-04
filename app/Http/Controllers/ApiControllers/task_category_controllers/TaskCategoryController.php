<?php

namespace App\Http\Controllers\ApiControllers\task_category_controllers;

use App\Http\Controllers\Controller;
use App\Models\TaskCategoriesModel;
use Illuminate\Http\Request;

class TaskCategoryController extends Controller
{
    public function getTaskCategories()
    {
        $categories = TaskCategoriesModel::get();

        return response()->json([
            'status' => true,
            'task_categories' => $categories,
        ]);
    }

    // public function getSubmissionCategories()
    // {
    //     $categories = TaskCategoriesModel::get();

    //     return response()->json([
    //         'status' => true,
    //         'task_categories' => $categories,
    //     ]);
    // }
}
