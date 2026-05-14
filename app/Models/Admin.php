<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Admin extends Authenticatable
{
    use Notifiable;

    protected $table = 'admins';

    protected $fillable = [
        'first_name',
        'last_name',
        'phone',
        'email',
        'national_id',
        'father_name',
        'birth_date',
        'position',
        'password',
        'permissions',
        'is_active',
        'is_super',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'permissions' => 'array',
        'is_active' => 'boolean',
        'is_super' => 'boolean',
        'birth_date' => 'date',
        'password' => 'hashed',
    ];

    public function getFullNameAttribute(): string
    {
        return trim($this->first_name.' '.$this->last_name);
    }

    public function hasPermission(string $key): bool
    {
        if ($this->is_super) {
            return true;
        }

        $list = $this->permissions ?? [];

        return in_array($key, $list, true);
    }
}
