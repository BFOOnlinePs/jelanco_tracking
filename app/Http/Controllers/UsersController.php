<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class UsersController extends Controller
{
    public function index(){
        return view('project.users.index');
    }

    public function list_users_ajax(Request $request)
    {
        $data = User::get();
        return response()->json([
            'success' => true,
            'view' => view('project.users.ajax.list_users_ajax',['data'=>$data])->render()
        ]);
    }

    public function add()
    {
        $roles = Role::get();
        return view('project.users.add' , ['roles' => $roles]);
    }

    public function create(Request $request)
    {
        $data = new User();
        $data->name = $request->name;
        $data->email = $request->email;
        $data->password = Hash::make($request->password);
        if($data->save()){
            $role = Role::findById($request->role);
            $data->assignRole($role);
            return redirect()->route('users.index')->with(['success' => 'تم انشاء المستخدم بنجاح']);
        }
        else{
            return redirect()->route('users.index')->with(['fail' => 'هناك خلل ما في انشاء المستخدم']);
        }
    }

    public function edit($id)
    {
        $data = User::where('id',$id)->first();
        $roles = Role::get();
        $userRoles = $data->roles->pluck('name')->toArray();
        return view('project.users.edit',['data'=>$data,'roles'=>$roles,'userRoles'=>$userRoles]);
    }

    public function update(Request $request)
    {
        $data = User::where('id',$request->id)->first();
        $data->name = $request->name;
        $data->email = $request->email;
        if($request->filled('password')){
            $data->password = Hash::make($request->password);
        }
        if($data->save()){
            $role = Role::findById($request->role);
            $data->assignRole($role);
            return redirect()->route('users.index')->with(['success' => 'تم تعديل المستخدم بنجاح']);
        }
        else{
            return redirect()->route('users.index')->with(['fail' => 'هناك خلل ما في تعديل المستخدم']);
        }
    }
}
