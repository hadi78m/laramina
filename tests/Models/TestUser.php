<?php

namespace Tests\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Laramina\Traits\AdminTableTrait;

class TestUser extends Model
{
    use HasFactory, AdminTableTrait;

    protected $table = 'users';

    protected $fillable = [
        'name',
        'email',
        'password',
        'is_active',
        'is_default',
        'address',
    ];

    protected $hidden = [
        'password',
    ];

    public static function adminTransform($user): array
    {
        return [
            'id'         => $user->id,
            'name'       => $user->name,
            'email'      => $user->email,
            'is_active'  => $user->is_active,
            'created_at' => $user->created_at?->format('Y/m/d H:i'),
        ];
    }
}
