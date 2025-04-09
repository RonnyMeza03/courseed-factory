<?php

namespace App\Models;

use DateTimeInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use MongoDB\Laravel\Eloquent\Model as EloquentModel;

class UserProfile extends EloquentModel
{
    use HasFactory;
    protected $connection = 'mongodb';
    protected $table = 'user_profiles';

    protected $fillable = [
        'userId',
        'knowledgeLevel',
        'availableTime',
        'budget',
        'platformPreference',
    ];

    // Renombrar timestamps
    const CREATED_AT = 'createdAt';
    const UPDATED_AT = 'updatedAt';

    // Configurar nombres en camelCase
    protected $attributes = [
        'createdAt' => null,
        'updatedAt' => null,
    ];
    protected function serializeDate(DateTimeInterface $date): string
    {
        return $date->format('Y-m-d\TH:i:s.uP'); // ISO-8601 compatible
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'userId');
    }

    public function userCourseRecomended()
    {
        return $this->hasMany(UserCourseRecomended::class, 'userId');
    }
}
