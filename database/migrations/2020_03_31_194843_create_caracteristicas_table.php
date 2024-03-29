<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCaracteristicasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('caracteristicas', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('nome', 191)->unique();
            $table->string('valor_padrao')->nullable();
            $table->tinyInteger('tipo')->comment('
                0: texto,
                1: int,
                2: float,
                3: opcao,
                4: boolean
            ');
            $table->boolean('exclusoria')->default(0);
            $table->integer('peso')->default(1);
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
        Schema::dropIfExists('caracteristicas');
    }
}
