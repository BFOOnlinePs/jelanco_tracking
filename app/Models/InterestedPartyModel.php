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
}
