<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::connection('mongodb')->create('user_has_roles', function (Blueprint $collection) {
            $collection->id();
            $collection->foreign('role_id')->references('id')->on('roles')->onDelete('cascade');
            $collection->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $collection->timestamps();
        });
    }

    public function down()
    {
        Schema::connection('mongodb')->dropIfExists('user_has_roles');
    }
};