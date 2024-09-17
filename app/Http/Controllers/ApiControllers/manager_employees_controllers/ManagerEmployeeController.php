<?php

namespace App\Http\Controllers\ApiControllers\manager_employees_controllers;

use App\Http\Controllers\Controller;
use App\Models\ManagerEmployeesModel;
use App\Services\ManagerEmployeesService;
use Illuminate\Http\Request;

class ManagerEmployeeController extends Controller
{
    protected $managerEmployeesService;

    public function __construct(ManagerEmployeesService $managerEmployeesService)
    {
        $this->managerEmployeesService = $managerEmployeesService;
    }

    public function getManagerEmployees()
    {
        $manager_id = auth()->user()->id;

        $manager_employees = $this->managerEmployeesService->getEmployeesByManagerId($manager_id, true);

        return response()->json([
            'status' => true,
            'manager_employees' => $manager_employees
        ]);
    }
}
