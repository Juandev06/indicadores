<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCantMesesAuxPeriodos extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('aux_periodos', function (Blueprint $table) {
            $table->tinyInteger('cant_meses');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('aux_periodos', function (Blueprint $table) {
            $table->dropColumn('cant_meses');            
        });
    }
}
