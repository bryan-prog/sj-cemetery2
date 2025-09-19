<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class ActionLog extends Model
{
    protected $fillable = [
        'user_id',
        'username',
        'action',
        'target_type',
        'target_id',
        'happened_at',
        'details',
    ];

    protected $casts = [
        'happened_at' => 'datetime',
        'details'     => 'array',
    ];


    public function user()
    {
        return $this->belongsTo(User::class);
    }


    public function target(): MorphTo
    {
        return $this->morphTo(__FUNCTION__, 'target_type', 'target_id');
    }
}
