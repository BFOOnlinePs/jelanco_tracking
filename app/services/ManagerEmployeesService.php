<?php

namespace App\Services;

use App\Models\ManagerEmployeesModel;
use App\Models\User;

class ManagerEmployeesService
{
    /**
     * Get employee IDs and names (optional) for a given manager ID
     *
     * @param int $managerId
     * @param bool $withInfo
     * @return array
     */
    public function getEmployeesByManagerId($managerId, $withInfo = false)
    {
        $employeeIds = ManagerEmployeesModel::where('me_manager_id', $managerId)
            ->pluck('me_employee_ids')
            ->first();

        $employeeIds = array_map('intval', json_decode($employeeIds, true) ?? []);

        // with names, job title and images
        if ($withInfo) {
            return User::whereIn('id', $employeeIds)
                ->select('id', 'name', 'image', 'job_title')
                ->get()
                ->toArray();
        }

        return $employeeIds;
    }
}
