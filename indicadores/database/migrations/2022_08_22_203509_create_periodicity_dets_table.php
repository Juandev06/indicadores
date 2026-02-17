<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePeriodicityDetsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('periodicity_dets', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('id_periodicity')->unsigned();
            $table->string('name', 20);
            $table->tinyInteger('order');
            $table->timestamps();

            $table->foreign('id_periodicity')->references('id')->on('periodicities');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('periodicity_dets');
    }
}
