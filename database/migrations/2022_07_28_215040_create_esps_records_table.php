<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEspsRecordsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('esps_records', function (Blueprint $table) {
            $table->id();
            //$table->integer('esp_id');
            $table->string('email');
            //$table->integer('esp_id')->nullable(); //borrar ?
            $table->string('mc_id')->nullable();
            $table->string('at_id')->nullable();
            $table->string('keap_id')->nullable();
            //$table->string('list_id'); //borrar?
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
        Schema::dropIfExists('esps_records');
    }
}
