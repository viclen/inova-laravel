<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOpcaoCaracteristicasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('opcao_caracteristicas', function (Blueprint $table) {
            $table->unsignedBigInteger('caracteristica_id');
            $table->integer("ordem");
            $table->string("valor");
            $table->primary(["caracteristica_id", "ordem"]);

            $table->foreign('caracteristica_id')->references('id')->on('caracteristicas')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('opcao_caracteristicas');
    }
}
