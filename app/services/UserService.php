<?php

namespace App\Services;

use App\Models\DepartmentModel;
use App\Models\User;

class UserService
{
    public function getUserDepartments($userId)
    {
        $user = User::find($userId);

        $departmentIds = json_decode($user->departments, true);

        if (!empty($departmentIds)) {
            return DepartmentModel::whereIn('d_id', $departmentIds)
                // ->select('d_id', 'd_name')
                ->get();
        }

        // Return an empty collection if there are no department IDs
        return collect(); // or return [];
    }


}
