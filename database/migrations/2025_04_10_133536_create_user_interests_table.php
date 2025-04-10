<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::connection('mongodb')->create('user_interests', function (Blueprint $collection) {
            // Definir la clave primaria
            $collection->id(); // Cambia esto si necesitas un tipo de ID diferente
            $collection->foreign('profileId')->references('_id')->on('user_profiles')->onDelete('cascade');
            $collection->foreign('categoryId')->references('_id')->on('categories')->onDelete('cascade');
            $collection->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_interests');
    }
};
