<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::connection('mongodb')->create('courses', function (Blueprint $collection) {
            $collection->id();
            $collection->string('modality');
            $collection->float('price');
            $collection->text('url');
            $collection->string('title');
            $collection->text('image');
            $collection->text('description');
            $collection->int('duration');
            $collection->foreignId('category_id')->constrained();
            $collection->foreignId('institution_id')->constrained();
            $collection->timestamps();
        });
    }

    public function down()
    {
        Schema::connection('mongodb')->dropIfExists('courses');
    }
};

