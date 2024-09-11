<?php

namespace App\Services;

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
        $employeeIds = DB::table('manager_employees')
            ->where('me_manager_id', $managerId)
            ->pluck('me_employee_ids')
            ->first();

        $employeeIds = array_map('intval', json_decode($employeeIds, true) ?? []);

        if ($withNames) {
            return DB::table('users')
                ->whereIn('id', $employeeIds)
                ->select('id', 'name')
                ->get()
                ->toArray();
        }

        return $employeeIds;
    }
}
