<?php

namespace App\Models;

use DateTimeInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use MongoDB\Laravel\Eloquent\Model as EloquentModel;

class UserInterest extends EloquentModel
{
    use HasFactory;
    protected $connection = 'mongodb';
    protected $table = 'user_interests';

    // Definir la clave primaria
    protected $fillable = [
        'profileId',
        'categoryId',
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


    public function userProfile()
    {
        return $this->belongsTo(UserProfile::class, 'profileId');
    }

    public function category()
    {
        return $this->belongsTo(Categories::class, 'categoryId');
    }
}
