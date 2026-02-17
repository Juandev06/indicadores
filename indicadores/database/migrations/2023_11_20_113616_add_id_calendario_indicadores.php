<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddIdCalendarioIndicadores extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('indicadores', function (Blueprint $table) {
            $table->bigInteger('id_calendario')->default(1)->unsigned()->after('id_periodo');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('indicadores', function (Blueprint $table) {
            $table->dropColumn('id_calendario');            
        });
    }
}
