<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCaracteristicaEstoquesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('caracteristica_estoques', function (Blueprint $table) {
            $table->unsignedBigInteger('caracteristica_id');
            $table->unsignedBigInteger('estoque_id');
            $table->string("valor");
            $table->primary(['caracteristica_id', 'estoque_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('caracteristica_estoques');
    }
}
