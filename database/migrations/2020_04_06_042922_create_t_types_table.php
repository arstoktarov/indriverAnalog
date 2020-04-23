<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTTypesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('t_types', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('description', 500)->default('');
            $table->string('image', 255)->nullable();
            $table->string('charac_title');
            $table->string('charac_unit');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('t_types');
    }
}
