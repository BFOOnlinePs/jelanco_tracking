<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AttachmentsModel extends Model
{
    use HasFactory;

    protected $table = 'attachments';
    protected $primaryKey = 'a_id';

    protected $fillable = [
        'a_table',
        'a_fk_id',
        'a_attachment',
        'a_user_id',
    ];
}
