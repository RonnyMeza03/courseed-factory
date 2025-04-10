<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use DateTimeInterface;
use MongoDB\Laravel\Eloquent\Model as EloquentModel;

class Courses extends EloquentModel
{
    use HasFactory;
    protected $connection = 'mongodb';
    protected $table = 'courses';

    protected $fillable = [
        'categoryId',
        'modality',
        'price',
        'url',
        'title',
        'image',
        'description',
        'duration',
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

    public function view(){
        return $this->belongsTo(View::class);
    }

    public function reactions(){
        return $this->belongsToMany(User::class, 'reactions');
    }

    public function reviews(){
        return $this->hasMany(Reviews::class);
    }

    public function intitution(){
        return $this->belongsTo(View::class);
    }

    public function category(){
        return $this->belongsTo(Categories::class, 'categoryId', 'id');
    }

    public function userCourseRecomended()
    {
        return $this->hasMany(UserCourseRecomended::class, 'courseId');
    }
    
}
