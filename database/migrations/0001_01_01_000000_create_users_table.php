<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::connection('mongodb')->create('users', function (Blueprint $collection) {
            $collection->string('email')->unique();
            $collection->string('password');
            $collection->array('roles'); // Lista de roles
            $collection->timestamps();
        });
    }

    public function down()
    {
        Schema::connection('mongodb')->dropIfExists('users');
    }
};

