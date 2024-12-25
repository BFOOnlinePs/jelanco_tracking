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
        $user_employees = $this->managerEmployeesService->getEmployeesByManagerId($manager_id, true);
        // the user_managers so they will be disabled in front end list
        $user_manager_ids = ManagerEmployeesModel::whereJsonContains('me_employee_ids', strval($manager_id))
            ->pluck('me_manager_id');

        return response()->json([
            'status' => true,
            'user_manager_ids' => $user_manager_ids,
            'manager_employees' => $user_employees
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
            'employee_ids' => 'nullable', // as json
            // when employee_ids json is empty, so delete the row from database
            'is_remove' => 'nullable|boolean',
        ], [
            'manager_id.exists' => 'المسؤول غير موجود.',
            'manager_id.required' => 'يجب تحديد المسؤول.',
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

        if ($request->input('is_remove')) {
            $manager_employees->delete();
            return response()->json([
                'status' => true,
                'message' => 'تمت العملية بنجاح',
            ]);
        }

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


    public function assignEmployeeForManagers(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'manager_ids' => 'nullable|array',
            'manager_ids.*' => 'integer|exists:users,id',
            'employee_id' => 'required|integer|exists:users,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => $validator->errors()->first(),
            ]);
        }

        $employeeId = (string) $request->input('employee_id');
        $managerIds = $request->input('manager_ids');

        foreach ($managerIds as $managerId) {
            // Check if the row exists for the manager
            $row = ManagerEmployeesModel::where('me_manager_id', $managerId)->first();

            if ($row) {
                $employeeIds = array_map('strval', json_decode($row->me_employee_ids, true) ?? []);

                // Add the new employee if not already in the list
                if (!in_array($employeeId, $employeeIds)) {
                    $employeeIds[] = $employeeId;
                }

                // Update or delete the row based on the updated employee list
                if (!empty($employeeIds)) {
                    ManagerEmployeesModel::where('me_manager_id', $managerId)
                        ->update(['me_employee_ids' => json_encode(array_values($employeeIds))]);
                } else {
                    ManagerEmployeesModel::where('me_manager_id', $managerId)
                        ->delete();
                }
            } else {
                // Add a new row if it doesn't exist
                ManagerEmployeesModel::insert([
                    'me_manager_id' => $managerId,
                    'me_employee_ids' => json_encode([(string) $employeeId]),
                ]);
            }
        }

        // Remove the employee from managers not in the input list
        $rowsToUpdate = ManagerEmployeesModel::whereNotIn('me_manager_id', $managerIds)
            ->whereJsonContains('me_employee_ids', $employeeId)
            ->get();

        foreach ($rowsToUpdate as $row) {
            $employeeIds = array_map('strval', json_decode($row->me_employee_ids, true) ?? []);
            $employeeIds = array_values(array_diff($employeeIds, [$employeeId])); // Remove the employee ID

            if (!empty($employeeIds)) {
                // Update the row if there are remaining employees
                ManagerEmployeesModel::where('me_manager_id', $row->me_manager_id)
                    ->update(['me_employee_ids' => json_encode($employeeIds)]);
            } else {
                // Delete the row if no employees remain
                ManagerEmployeesModel::where('me_manager_id', $row->me_manager_id)
                    ->delete();
            }
        }


        return response()->json([
            'status' => true,
            'message' => 'تمت العملية بنجاح',
        ]);
    }


    public function deleteManager(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'manager_id' => 'required|exists:users,id',
        ], [
            'manager_id.exists' => 'المسؤول غير موجود.',
            'manager_id.required' => 'يجب تحديد المسؤول.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => $validator->errors()->first(),
            ]);
        }

        $manager_employees = ManagerEmployeesModel::where('me_manager_id', $request->input('manager_id'));
        // if exists delete
        if (!$manager_employees->exists()) {
            return response()->json([
                'status' => false,
                'message' => 'المسؤول غير موجود.',
            ]);
        }

        $manager_employees->delete();

        return response()->json([
            'status' => true,
            'message' => 'تم إزالة المسؤول بنجاح',
        ]);
    }

    public function getManagersAndEmployeesOfUser($user_id)
    {
        $user_managers = ManagerEmployeesModel::whereJsonContains('me_employee_ids', strval($user_id))
            ->pluck('me_manager_id');
        $user_employees = ManagerEmployeesModel::where('me_manager_id', $user_id)
            ->pluck('me_employee_ids')
            ->first();

        $user_employees = $user_employees ? collect(json_decode($user_employees))
            ->map(function ($id) {
                return (int)$id;
            })
            ->toArray() : [];

        return response()->json([
            'status' => true,
            'manager_ids' => $user_managers,
            'employee_ids' => $user_employees
        ]);
    }
}
