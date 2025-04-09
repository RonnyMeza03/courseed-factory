<?php

namespace App\Models;

use DateTimeInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use MongoDB\Laravel\Eloquent\Model as EloquentModel;

class UserCourseRecomended extends EloquentModel
{
    use HasFactory;
    protected $connection = 'mongodb';
    protected $table = 'user_course_recomended';
    protected $fillable = [
        'userId',
        'courseId',
        'recomended'
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

    public function users()
    {
        return $this->belongsToMany(UserProfile::class, 'user_course_recomended');
    }

    public function courses()
    {
        return $this->belongsToMany(Courses::class, 'user_course_recomended');
    }
}
