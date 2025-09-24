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


    protected $appends = ['module', 'action_label'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function target(): MorphTo
    {
        return $this->morphTo(__FUNCTION__, 'target_type', 'target_id');
    }


    public function getModuleAttribute(): string
    {
        return class_basename((string) $this->target_type) ?: '—';
    }


    public function getActionLabelAttribute(): string
    {
        $a = (string) $this->action;

        $tokens = preg_split('/[._]/', $a) ?: [];
        $tokens = array_map('strtolower', $tokens);

        $label = [];
        $isBatch = in_array('batch', $tokens, true);

        foreach ($tokens as $t) {
            if ($t === 'approved' || $t === 'approve') {
                $label[] = 'APPROVAL';
            } elseif ($t === 'denied' || $t === 'deny') {
                $label[] = 'DENIED';
            } elseif ($t === 'created' || $t === 'create') {
                $label[] = 'CREATED';
            } elseif ($t === 'updated' || $t === 'update') {
                $label[] = 'UPDATED';
            } elseif ($t === 'batch') {

            } else {
                $label[] = strtoupper($t);
            }
        }

        $out = trim(implode(' ', $label));
        if ($isBatch) {
            $out .= ' (BATCH)';
        }

        return $out !== '' ? $out : strtoupper($a);
    }
}
