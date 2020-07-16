<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMatchesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('matches', function (Blueprint $table) {
            $table->unsignedBigInteger('interesse_id');
            $table->unsignedBigInteger('estoque_id');
            $table->integer('prioridade');
            $table->json('caracteristicas');

            $table->primary(['interesse_id', 'estoque_id']);

            $table->foreign('interesse_id')->references('id')->on('interesses')->onDelete('cascade');
            $table->foreign('estoque_id')->references('id')->on('estoques')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('matches');
    }
}
