<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->integer('type'); //1 for user, 2 for executor
            $table->string('first_name');
            $table->string('last_name');
            $table->string('phone');
            $table->timestamp('phone_verified_at')->nullable();
            $table->unsignedBigInteger('city_id');
            $table->string('password');
            $table->integer('balance');
            $table->boolean('push')->default(1);
            $table->boolean('sound')->default(1);
            $table->enum('lang', ['ru', 'en'])->default('ru');
            $table->rememberToken();
            $table->timestamps();
            $table->foreign('city_id')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
    }
}
