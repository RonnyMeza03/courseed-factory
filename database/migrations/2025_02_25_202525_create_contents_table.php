<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::connection('mongodb')->create('contents', function (Blueprint $collection) {
            $collection->text('descripcion');
            $collection->foreignId('course_id')->constrained();
            $collection->timestamps();
        });
    }

    public function down()
    {
        Schema::connection('mongodb')->dropIfExists('contents');
    }
};