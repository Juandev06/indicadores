<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMonthPeriodicityTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('month_periodicity', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table -> integer('periodicity_id');
            $table->unsignedBigInteger('months_id');
            $table->foreign('months_id')->references('id')->on('months');            
            $table -> string('status');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('month_periodicity');
    }
}
