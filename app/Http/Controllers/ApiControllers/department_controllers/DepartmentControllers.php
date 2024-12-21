<?php

namespace App\Http\Controllers\ApiControllers\department_controllers;

use App\Http\Controllers\Controller;
use App\Models\DepartmentModel;
use Illuminate\Http\Request;

class DepartmentControllers extends Controller
{
    public function getDepartments(){
        $departments = DepartmentModel::get();

        return response()->json([
            'status' => true,
            'departments' => $departments
        ]);
    }
}
