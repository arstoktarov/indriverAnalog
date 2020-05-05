<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMaterialOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('m_orders', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid');
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('city_id');
            $table->unsignedBigInteger('material_id');
            $table->string('address')->nullable();
            $table->decimal('lat');
            $table->decimal('long');
            $table->timestamp('delivery_deadline');
            $table->integer('count');
            $table->integer('price');
            $table->longText('description')->nullable();
            $table->timestamps();
        });
        //TODO Add Table Columns
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('m_orders');
    }
}
