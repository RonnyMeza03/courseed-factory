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
        Schema::connection('mongodb')->create('user_course_recomended', function (Blueprint $collection) {
            $collection->id();
            $collection->string('userId');
            $collection->string('courseId');
            $collection->string('courseCategory')->default('none');
            $collection->string('courseModality')->default('none');
            $collection->int('courseHours')->default(0);
            $collection->float('ratingAvg')->default(0);
            $collection->string('maxReaction')->default('none');
            $collection->integer('visitsCount')->default(0);
            $collection->integer('reviewsCount')->default(0);
            $collection->boolean('recomended')->default(false);
            $collection->string('createdAt')->default(now());
            $collection->string('updatedAt')->default(now());
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_course_recomended');
    }
};
