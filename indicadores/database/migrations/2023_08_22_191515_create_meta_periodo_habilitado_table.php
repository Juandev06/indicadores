<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMetaPeriodoHabilitadoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('meta_periodo_habilitado', function (Blueprint $table) {
            $table->id();
            $table->smallInteger('ano');
            $table->string('estado', 1)->default('A'); // A: activo, I: inactivo
            $table->dateTime('fecha_activacion');
            $table->bigInteger('id_usuario_activacion')->unsigned();
            $table->string('obs_activacion', 1024)->nullable();
            $table->dateTime('fecha_inactivacion')->nullable();
            $table->bigInteger('id_usuario_inactivacion')->nullable()->unsigned();
            $table->string('obs_inactivacion', 1024)->nullable();

            $table->foreign('id_usuario_activacion')->references('id')->on('users');
            $table->foreign('id_usuario_inactivacion')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('meta_periodo_habilitado');
    }
}
