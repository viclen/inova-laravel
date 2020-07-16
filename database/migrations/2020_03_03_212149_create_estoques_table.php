<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEstoquesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('estoques', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('carro_id');
            $table->unsignedBigInteger('modelo_id')->nullable();
            $table->text('observacoes')->nullable();
            $table->timestamps();

            $table->foreign('modelo_id')->references('id')->on('modelos')->onDelete('cascade');
            $table->foreign('carro_id')->references('id')->on('carros')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('estoques');
    }
}
