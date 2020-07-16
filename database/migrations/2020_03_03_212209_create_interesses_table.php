<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInteressesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('interesses', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('cliente_id');
            $table->unsignedBigInteger('carro_id')->nullable();
            $table->text('observacoes')->nullable();
            $table->integer('origem')->default(0)->comment('
                Facebook,
                Whatsapp,
                Instagram,
                Loja,
                Telefone,
                OLX,
                Outro
            ');
            $table->timestamps();

            $table->foreign('carro_id')->references('id')->on('carros')->onDelete('cascade');
            $table->foreign('cliente_id')->references('id')->on('clientes')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('interesses');
    }
}
