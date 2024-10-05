<?php

namespace App\Http\Controllers\ApiControllers\manager_employees_controllers;

use App\Http\Controllers\Controller;
use App\Models\ManagerEmployeesModel;
use App\Models\TaskModel;
use App\Models\User;
use App\Services\ManagerEmployeesService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ManagerEmployeeController extends Controller
{
    protected $managerEmployeesService;

    public function __construct(ManagerEmployeesService $managerEmployeesService)
    {
        $this->managerEmployeesService = $managerEmployeesService;
    }

    public function getManagerEmployees() // the current logged in manager
    {
        $manager_id = auth()->user()->id;

        $manager_employees = $this->managerEmployeesService->getEmployeesByManagerId($manager_id, true);

        return response()->json([
            'status' => true,
            'manager_employees' => $manager_employees
        ]);
    }

    public function getManagerEmployeesById($manager_id)
    {
        $manager_employees = $this->managerEmployeesService->getEmployeesByManagerId($manager_id, true);

        return response()->json([
            'status' => true,
            'manager_employees' => $manager_employees
        ]);
    }

    public function getManagerEmployeesWithTaskAssignees(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'task_id' => 'required|exists:tasks,t_id',
        ], [
            'task_id.exists' => 'المهمة غير موجودة.',
            'task_id.required' => 'يجب تحديد المهمة.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => $validator->errors()->first(),
            ]);
        }

        $manager_id = auth()->user()->id;

        $manager_employees = $this->managerEmployeesService->getEmployeesByManagerId($manager_id, true);

        $task_assignees = $this->getSelectedEmployeesByTaskId($request->input('task_id'));

        // Merge the two arrays (manager_employees + filtered_task_assignees)
        $merged_employees = array_merge($manager_employees, $task_assignees);

        // Remove duplicates based on 'id'
        $unique_employees = array_values(array_reduce($merged_employees, function ($carry, $item) {
            $carry[$item['id']] = $item;
            return $carry;
        }, []));


        return response()->json([
            'status' => true,
            'manager_employees' => $unique_employees
        ]);
    }


    public function getSelectedEmployeesByTaskId($task_id)
    {
        $task_assignee_ids = TaskModel::where('t_id', $task_id)
            ->pluck('t_assigned_to')
            ->first();

        $task_assignee_ids = array_map('intval', json_decode($task_assignee_ids, true) ?? []);

        // with names, job title and images
        $task_assignees = User::whereIn('id', $task_assignee_ids)
            ->select('id', 'name', 'image', 'job_title')
            ->get()
            ->toArray();

        return $task_assignees;
    }

    public function getManagers()
    {
        // Get the manager IDs from the ManagerEmployeesModel
        $managerIds = ManagerEmployeesModel::pluck('me_manager_id');

        // Retrieve the user details of the managers in one query using whereIn
        $managers = User::whereIn('id', $managerIds)
            ->select('id', 'name', 'image')
            ->get();

        return response()->json([
            'status' => true,
            'managers' => $managers
        ]);
    }

    public function addEditManagerEmployees(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'manager_id' => 'required|exists:users,id',
            'employee_ids' => 'required', // as json
        ], [
            'manager_id.exists' => 'المدير غير موجود.',
            'manager_id.required' => 'يجب تحديد المدير.',
            'employee_ids.required' => 'يجب تحديد الموظفين.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => $validator->errors()->first(),
            ]);
        }


        $manager_id = $request->input('manager_id');
        $employee_ids = $request->input('employee_ids');

        // if the manager already has employees then edit them
        $manager_employees = ManagerEmployeesModel::where('me_manager_id', $manager_id)
            ->first();

        if ($manager_employees) {
            $manager_employees->me_employee_ids = $employee_ids;
            $manager_employees->save();
        } else {
            // if the manager doesn't have employees then add them
            $manager_employees = new ManagerEmployeesModel();
            $manager_employees->me_manager_id = $manager_id;
            $manager_employees->me_employee_ids = $employee_ids; // as json
            $manager_employees->save();
        }

        return response()->json([
            'status' => true,
            'message' => 'تمت العملية بنجاح',
        ]);
    }
}
