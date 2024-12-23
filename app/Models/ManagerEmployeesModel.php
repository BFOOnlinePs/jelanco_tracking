<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ManagerEmployeesModel extends Model
{
    use HasFactory;

    protected $table = 'manager_employees';
    protected $primaryKey = 'me_id';

    protected $fillable = [
        'me_manager_id',
        'me_employee_id',
    ];

}
