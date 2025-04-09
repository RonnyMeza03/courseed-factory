<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::connection('mongodb')->create('views', function (Blueprint $collection) {
            $collection->foreignId('courseId')->constrained()->cascadeOnDelete();
            $collection->foreignId('userId')->constrained()->cascadeOnDelete();
            $collection->timestamps();
        });
    }

    public function down()
    {
        Schema::connection('mongodb')->dropIfExists('views');
    }
};