<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TaskCategoriesModel extends Model
{
    use HasFactory;
    protected $table = 'task_categories';
    protected $primaryKey = 'c_id';

}
