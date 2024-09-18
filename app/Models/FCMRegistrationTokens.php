<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FCMRegistrationTokens extends Model
{
    use HasFactory;
    protected $table = 'fcm_registration_tokens';
    protected $primaryKey = 'frt_id';

    protected $fillable = [
        'frt_user_id',
        'frt_registration_token',
        'frt_date'
    ];
}
