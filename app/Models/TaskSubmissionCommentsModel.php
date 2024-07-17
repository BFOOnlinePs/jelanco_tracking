<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TaskSubmissionCommentsModel extends Model
{
    use HasFactory;

    protected $table = 'task_submission_comments';
    protected $primaryKey = 'tsc_id';

}
