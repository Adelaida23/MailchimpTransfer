<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEspRecordsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('esp_records', function (Blueprint $table) {
            $table->id();
            $table->integer('esp_id')->nullable();
            $table->integer('mc_id')->nullable();
            $table->integer('at_id')->nullable();
            $table->integer('keap_id')->nullable();
            $table->string('list_id'); //borrar?
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
        Schema::dropIfExists('esp_records');
    }
}
