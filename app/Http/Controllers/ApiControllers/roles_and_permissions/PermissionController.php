<?php

namespace App\Http\Controllers\ApiControllers\roles_and_permissions;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;

class PermissionController extends Controller
{
    public function get_permission()
    {
        $data = Permission::get();
        return response()->json($data);
    }
}
