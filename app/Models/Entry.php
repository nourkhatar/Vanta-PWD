<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Entry extends Model
{
    protected $fillable = [
        'user_id',
        'title',
        'username_enc',
        'password_enc',
        'iv',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
