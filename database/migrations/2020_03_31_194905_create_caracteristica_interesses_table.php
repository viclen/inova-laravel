<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCaracteristicaInteressesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('caracteristica_interesses', function (Blueprint $table) {
            $table->unsignedBigInteger('caracteristica_id');
            $table->unsignedBigInteger('interesse_id');
            $table->string('valor');
            $table->string('comparador', 1)->comment("
                <: menor,
                >: maior,
                =: igual,
                ~: perto
            ")->default("1");
            $table->primary(['caracteristica_id', 'interesse_id']);

            $table->foreign('caracteristica_id')->references('id')->on('caracteristicas')->onDelete('cascade');
            $table->foreign('interesse_id')->references('id')->on('interesses')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('caracteristica_interesses');
    }
}
