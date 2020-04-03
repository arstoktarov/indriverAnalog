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
            $table->integer('type')->default(1); //1 for user, 2 for executor
            $table->string('name')->default('Noname');
            $table->string('phone');
            $table->string('phone_verification_code')->nullable();
            $table->timestamp('phone_verified_at')->nullable();
            $table->unsignedBigInteger('city_id')->nullable();
            $table->string('password')->default('nopassword');
            $table->integer('balance')->default(0);
            $table->boolean('push')->default(1);
            $table->boolean('sound')->default(1);
            $table->enum('lang', ['ru', 'en'])->default('ru');
            $table->string('token', 60)->nullable();
            $table->timestamps();
            $table->foreign('city_id')->references('id')->on('cities');
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
