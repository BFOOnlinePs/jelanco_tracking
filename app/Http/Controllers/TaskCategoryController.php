<?php

namespace App\Http\Controllers;

use App\Models\TaskCategoriesModel;
use Illuminate\Http\Request;

class TaskCategoryController extends Controller
{
    public function index()
    {
        $data = TaskCategoriesModel::get();
        return view('project.tasks_category.index',['data'=>$data]);
    }

    public function add()
    {
        return view('project.tasks_category.add');
    }

    public function create(Request $request)
    {
        $data = new TaskCategoriesModel();
        $data->c_name = $request->name;
        if ($data->save()){
            return redirect()->route('tasks_category.index')->with(['success'=>'تم انشاء الفئة بنجاح']);
        }
        else{
            return redirect()->route('tasks_category.index')->with(['fail'=>'هناك خلل ما لم يتم انشاء الفئة']);
        }
    }

    public function edit($id)
    {
        $data = TaskCategoriesModel::find($id);
        return view('project.tasks_category.edit',['data'=>$data]);
    }

    public function update(Request $request)
    {
        $data = TaskCategoriesModel::where('c_id',$request->id)->first();
        $data->c_name = $request->name;
        if ($data->save()){
            return redirect()->route('tasks_category.index')->with(['success'=>'تم انشاء الفئة بنجاح']);
        }
        else{
            return redirect()->route('tasks_category.index')->with(['fail'=>'هناك خلل ما لم يتم اضافة الفئة']);
        }
    }
}
