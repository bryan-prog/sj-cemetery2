<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Support\Facades\Hash;


class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'lname',
        'fname',
        'mname',
        'suffix',
        'designation',
        'permission',
        'username',
        'password',
        'active',
         'can_approve_deny',
        'can_view_actionlogs',
        'can_edit_permits',
        'can_print_permits',



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
          'can_approve_deny'    => 'boolean',
        'can_view_actionlogs' => 'boolean',
        'can_edit_permits'    => 'boolean',
        'can_print_permits'   => 'boolean',
    ];


    public function actionLogs()
{
    return $this->hasMany(\App\Models\ActionLog::class);
}


public function getDisplayNameAttribute(): string
{

    if (!empty($this->username)) return $this->username;

    $parts = array_filter([$this->fname ?? null, $this->mname ?? null, $this->lname ?? null, $this->suffix ?? null]);
    return $parts ? implode(' ', $parts) : 'Unknown User';
}




}
