<?php

namespace App\Services;

use App\Models\ManagerEmployeesModel;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class ManagerEmployeesService
{
    /**
     * Get employee IDs and names (optional) for a given manager ID
     *
     * @param int $managerId
     * @param bool $withNames
     * @return array
     */
    public function getEmployeesByManagerId($managerId, $withNames = false)
    {
        $employeeIds = ManagerEmployeesModel::where('me_manager_id', $managerId)
            ->pluck('me_employee_ids')
            ->first();

        $employeeIds = array_map('intval', json_decode($employeeIds, true) ?? []);

        // with names and images
        if ($withNames) {
            return User::whereIn('id', $employeeIds)
                ->select('id', 'name', 'image')
                ->get()
                ->toArray();
        }

        return $employeeIds;
    }
}
