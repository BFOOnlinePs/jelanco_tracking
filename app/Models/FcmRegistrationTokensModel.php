<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FcmRegistrationTokensModel extends Model
{
    use HasFactory;

    protected $table = 'fcm_registration_tokens';
    protected $primaryKey = 'frt_id';
}
