<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateModelosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('modelos', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('nome');
            $table->integer('ano');
            $table->float('preco', 8, 2);
            $table->char('combustivel', 1);
            $table->string('fipe_id', 10);
            $table->unsignedBigInteger('carro_id');
            $table->timestamps();

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
        Schema::dropIfExists('modelos');
    }
}
