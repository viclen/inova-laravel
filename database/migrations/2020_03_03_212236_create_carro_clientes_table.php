<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCarroClientesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('carro_clientes', function (Blueprint $table) {
            $table->unsignedBigInteger('carro_id');
            $table->unsignedBigInteger('cliente_id');
            $table->float('valor');

            $table->unique(['carro_id', 'cliente_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('carro_clientes');
    }
}
