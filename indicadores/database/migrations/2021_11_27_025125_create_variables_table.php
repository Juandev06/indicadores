<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVariablesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('variables', function (Blueprint $table) {
            $table->id();
            $table->string('nombre', 255);
            $table->string('estado', 1)->default('A');
            $table->string('obs', 255);
            $table->unsignedBigInteger('types_id');
            $table->string('tipo', 1)->default('N'); // N: numérico, P: porcentual
            $table->unsignedBigInteger('id_periodo');
            $table->unsignedBigInteger('id_area');
            $table->unsignedBigInteger('user_id');
            
            $table->timestamps();

            $table->foreign('types_id')->references('id')->on('types');
            $table->foreign('id_periodo')->references('id')->on('aux_periodos');
            $table->foreign('id_area')->references('id')->on('areas');
            $table->foreign('user_id')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('variables');
    }
}
