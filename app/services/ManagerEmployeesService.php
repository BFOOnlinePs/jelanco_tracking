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
        // Get the employee IDs from the manager_employees table
        $employeeIds = DB::table('manager_employees')
            ->where('me_manager_id', $managerId)
            ->pluck('me_employee_ids')
            ->first();

        // Decode the JSON array of employee IDs and cast them to integers
        $employeeIds = array_map('intval', json_decode($employeeIds, true) ?? []);

        // If names are requested, fetch employee names along with IDs
        if ($withNames) {
            return DB::table('users')  // assuming 'users' is the table where employee names are stored
                ->whereIn('id', $employeeIds)
                ->select('id', 'name')
                ->get()
                ->toArray();
        }

        // Otherwise, return only the IDs
        return $employeeIds;
    }
}
