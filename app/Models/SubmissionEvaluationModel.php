<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SubmissionEvaluationModel extends Model
{
    use HasFactory;

    protected $table = 'submission_evaluations';
    protected $primaryKey = 'se_id';

    protected $fillable = [
        'se_task_id',
        'se_submission_id',
        'se_evaluator_id',
        'se_rating',
        'se_evaluator_notes',
    ];

    public function evaluatorUser()
    {
        return $this->belongsTo(User::class, 'se_evaluator_id', 'id');
    }
}
