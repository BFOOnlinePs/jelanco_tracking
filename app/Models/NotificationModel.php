<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NotificationModel extends Model
{
    use HasFactory;

    protected $table = 'notifications';

    protected $fillable = [
        'user_id',
        'title',
        'body',
        'is_read',
        'type',
        'type_id',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
