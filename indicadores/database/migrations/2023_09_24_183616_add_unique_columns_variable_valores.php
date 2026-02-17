<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddUniqueColumnsVariableValores extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::table('variable_valores', function (Blueprint $table) {
            $table->unique(['id_variable', 'mes', 'ano'], 'unique_fields_variable_valores');            
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('variable_valores', function (Blueprint $table) {
            $table->dropUnique('unique_fields_variable_valores');            
        });
    }
}
