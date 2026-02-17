<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddIdCalendarioAuxPeriodoDets extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('aux_periodo_dets', function (Blueprint $table) {
            $table->bigInteger('id_calendario')->default(1)->unsigned();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('aux_periodo_dets', function (Blueprint $table) {
            $table->dropColumn('id_calendario');            
        });
    }
}
