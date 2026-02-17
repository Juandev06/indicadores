<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSetValuesMetasIndicatorsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('set_values_goals_indicators', function (Blueprint $table) {
            $table->id();
            $table->integer('id_indicador');
            $table->integer('mes');
            $table->integer('value');
            $table->integer('formule_id');
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
        Schema::dropIfExists('set_values_goals_indicators');
    }
}
