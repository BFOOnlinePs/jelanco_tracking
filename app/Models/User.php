<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles;

    protected $table = 'users';
    protected $primaryKey = 'id';


    public function notificationTokens()
    {
        return $this->hasMany(FcmRegistrationTokensModel::class, 'frt_user_id', 'id');
    }

    public function notifications()
    {
        return $this->hasMany(NotificationModel::class);
    }

    public function hasSubmittedTask($taskId, $userId)
    {
        return TaskSubmissionsModel::where('ts_task_id', $taskId)->where('ts_submitter', $userId)->exists();
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'name',
        'job_title',
        'departments',
        'phone_number',
        'user_status'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];
}
