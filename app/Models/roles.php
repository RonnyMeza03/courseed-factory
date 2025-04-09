<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use DateTimeInterface;
use MongoDB\Laravel\Eloquent\Model as EloquentModel;

class Roles extends EloquentModel
{
    use HasFactory;
    protected $connection = 'mongodb';
    protected $table = 'roles';

    protected $fillable = [
        'role'
    ];

     // Configurar nombres en camelCase
     protected $attributes = [
        'createdAt' => null,
        'updatedAt' => null,
    ];

    // Renombrar timestamps
    const CREATED_AT = 'createdAt';
    const UPDATED_AT = 'updatedAt';

    protected function serializeDate(DateTimeInterface $date): string
    {
        return $date->format('Y-m-d\TH:i:s.uP'); // ISO-8601 compatible
    }

    public function users(){
        return $this->belongsToMany(User::class, 'user_has_roles');
    }
}
