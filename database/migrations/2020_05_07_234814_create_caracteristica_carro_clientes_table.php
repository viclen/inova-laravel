<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCaracteristicaCarroClientesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('caracteristica_carro_clientes', function (Blueprint $table) {
            $table->unsignedBigInteger('caracteristica_id');
            $table->unsignedBigInteger('carro_cliente_id');
            $table->string("valor");
            $table->primary(['caracteristica_id', 'carro_cliente_id'], 'caracteristica_carro_cliente_id_primary');

            $table->foreign('caracteristica_id')->references('id')->on('caracteristicas')->onDelete('cascade');
            $table->foreign('carro_cliente_id')->references('id')->on('carro_clientes')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('caracteristica_carro_clientes');
    }
}
