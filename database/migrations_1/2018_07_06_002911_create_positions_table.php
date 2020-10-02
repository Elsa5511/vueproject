<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePositionsTable extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('positions', function (Blueprint $table) {
            $table->increments('id');
            $table->string('protocol')->default('unknown');
            $table->integer('user_id')->unsigned();
            $table->point('position');
            $table->json('attributes')->nullable();
            $table->timestamps();

            $table->spatialIndex('position');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });


        Schema::table('users', function (Blueprint $table) {
            $table->integer('position_id')->unsigned()->nullable();
            $table->foreign('position_id')->references('id')->on('positions')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::dropIfExists('positions');
        Schema::table('users', function (Blueprint $table) {
            $table->drop('position_id');
        });
    }

}
