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
            $table->float('valor', 11, 2);
            $table->float('fipe', 11, 2)->nullable();
            $table->integer('ano')->nullable();
            $table->string('cor', 30)->nullable();
            $table->string('chassi')->nullable();
            $table->text('observacoes')->nullable();
            $table->unsignedBigInteger('carro_id');
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
        Schema::dropIfExists('estoques');
    }
}
