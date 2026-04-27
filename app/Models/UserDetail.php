<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserDetail extends Model
{
    /** @use HasFactory<\Database\Factories\UserDetailFactory> */
    use HasFactory;

    protected $fillable = [
        'user_id',
        'biome_id',
        'blood_group',
        'clearance_level',
        'logs',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
