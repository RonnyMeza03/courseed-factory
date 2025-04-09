<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use DateTimeInterface;
use MongoDB\Laravel\Eloquent\Model as EloquentModel;

class Reviews extends EloquentModel
{
    use HasFactory;
    protected $connection = 'mongodb';
    protected $table = 'reviews';

    // Configurar nombres en camelCase
    protected $fillable = ['createdAt', 'updatedAt', 'content', 'rating', 'userId', 'courseId'];

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

    public function user(){
        return $this->belongsTo(User::class, 'userId', '_id');
    }

    public function course(){
        return $this->belongsTo(Courses::class);
    }
}
