<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Silber\Bouncer\Database\HasRolesAndAbilities;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable, HasRolesAndAbilities;
    
    protected $fillable = [
        'name',
        'email',
        'password',
        'needs_password_change',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Get the user's details.
     */
    public function detail()
    {
        return $this->hasOne(UserDetail::class);
    }

    /**
     * Get the user's sent messages.
     */
    public function messages()
    {
        return $this->hasMany(Message::class, 'sender_id');
    }
}
