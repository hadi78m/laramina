<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class User extends Model
{
    use \Laramina\Traits\AdminTableTrait;

    protected $table = 'users';
    protected $fillable = ['name', 'email', 'password', 'is_active', 'address'];
    protected $hidden = ['password'];

    public static function adminTransform($user)
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
