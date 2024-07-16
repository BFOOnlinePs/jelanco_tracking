<?php

namespace App\Http\Controllers\ApiControllers\user_controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class userController extends Controller
{
    // only id and name
    public function getAllUsers(){
        $users = User::select('id', 'name')->get();

        return response()->json([
            'status' => true,
            'users' => $users
        ]);
    }
}
