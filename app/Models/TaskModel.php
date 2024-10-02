<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TaskModel extends Model
{
    use HasFactory;

    protected $table = 'tasks';
    protected $primaryKey = 't_id';


    protected $fillable = [
        't_content',
        't_planed_start_time',
        't_planed_end_time',
        't_status',
        't_added_by',
        't_category_id',
        't_assigned_to',
    ];

    public function taskCategory()
    {
        return $this->belongsTo(TaskCategoriesModel::class, 't_category_id', 'c_id');
    }

    public function addedByUser()
    {
        return $this->belongsTo(User::class, 't_added_by', 'id');
    }

    public function submissions()
    {
        return $this->hasMany(TaskSubmissionsModel::class, 'ts_task_id', 't_id');
    }

    protected $casts = [
        't_category_id' => 'integer',
    ];
}
