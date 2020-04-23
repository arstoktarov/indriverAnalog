<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTechnicsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('technics', function (Blueprint $table) {
            $table->id();
            //$table->unsignedBigInteger('category_id');
            $table->unsignedBigInteger('type_id');
            $table->double('charac_value');
            $table->timestamps();
            $table->foreign('type_id')->references('id')->on('t_types')->cascadeOnDelete();
        });
        //TODO Add table columns
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('technics');
    }
}
