<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use MongoDB\Laravel\Auth\User as Authenticatable;
use DateTimeInterface;

class User extends Authenticatable
{
    use HasFactory;
    protected $connection = 'mongodb';
    protected $table = 'users';

    protected $fillable = [
        'email',
        'password',
        'roles'
    ];

    // Configurar nombres en camelCase
    protected $attributes = [
        'createdAt' => null,
        'updatedAt' => null,
    ];

    // Renombrar timestamps
    const CREATED_AT = 'createdAt';
    const UPDATED_AT = 'updatedAt';

    protected $hidden = [
        'password',
        'remember_token',
    ];
    protected function serializeDate(DateTimeInterface $date): string
    {
        return $date->format('Y-m-d\TH:i:s.uP'); // ISO-8601 compatible
    }

    public function views(){
        return $this->belongsToMany(Courses::class, 'views');
    }

    public function reactions(){
        return $this->belongsToMany(Courses::class, 'reactions');
    }

    public function reviews(){
        return $this->hasMany(Reviews::class);
    }

    public function roles(){
        return $this->belongsToMany(Roles::class, 'user_has_roles');
    }

}

