<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TaskSubmissionCommentsModel extends Model
{
    use HasFactory;

    protected $table = 'task_submission_comments';
    protected $primaryKey = 'tsc_id';

    public function comment_by()
    {
        return $this->belongsTo(User::class , 'tsc_commented_by' , 'id');
    }

    public function attachments(){
        return $this->hasMany(AttachmentsModel::class , 'a_fk_id' , 'tsc_id')->where('a_table','task_submission_comments');
    }

}
