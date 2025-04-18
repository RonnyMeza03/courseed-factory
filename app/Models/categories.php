<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use DateTimeInterface;
use MongoDB\Laravel\Eloquent\Model as EloquentModel;

class Categories extends EloquentModel
{
    use HasFactory;
    protected $connection = 'mongodb';
    protected $table = 'categories';

    protected $fillable = [
        'name'
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

    public function courses(){
        return $this->hasMany(Courses::class);
    }

    public function userInterest()
    {
        return $this->belongsToMany(UserProfile::class, 'user_profiles_interests', 'categoryId', 'userId');
    }
}
