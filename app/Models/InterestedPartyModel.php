<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InterestedPartyModel extends Model
{
    use HasFactory;

    protected $table = 'interested_parties';
    protected $primaryKey = 'ip_id';
    protected $fillable = [
        'ip_article_type',
        'ip_article_id',
        'ip_interested_party_id',
        'ip_added_by_id',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'ip_interested_party_id', 'id');
    }

    public function addedByUser()
    {
        return $this->belongsTo(User::class, 'ip_added_by_id', 'id');
    }

    public function task()
    {
        return $this->belongsTo(TaskModel::class, 'ip_article_id', 't_id');
    }

    public function submission()
    {
        return $this->belongsTo(TaskSubmissionsModel::class, 'ip_article_id', 'ts_id');
    }
}
