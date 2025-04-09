<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::connection('mongodb')->create('reactions', function (Blueprint $collection) {
            $collection->id();
            $collection->foreign('courseId')->references('id')->on('courses')->onDelete('cascade');
            $collection->foreign('userId')->references('id')->on('users')->onDelete('cascade');
            $collection->string('type');
            $collection->timestamps();
        });
    }

    public function down()
    {
        Schema::connection('mongodb')->dropIfExists('reactions');
    }
};