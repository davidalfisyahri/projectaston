<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $primaryKey = 'id_user';

protected $fillable = [
    'name_user',
    'username',
    'password',
    'role',
    'position',
    'nik',
    'office_branch'
];

protected $hidden = [
    'password',
    'remember_token',
];

protected $casts = [
    'password' => 'hashed',
];

}
