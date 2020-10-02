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
            $table->uuid('id')->primary();
            $table->string('protocol')->default('unknown');
            $table->uuid('user_id', 48);
            $table->float('latitude', 12, 8);
            $table->float('longitude', 12, 8);
            $table->float('altitude', 12, 8)->nullable()->default(0.0);
            $table->float('speed')->nullable()->default(0.0);
            $table->float('accuracy')->nullable()->default(0.0);
            $table->boolean('valid')->default(true);
            $table->string('heading')->nullable();
            $table->json('attributes')->nullable();
            $table->timestamps();

            //$table->spatialIndex('position');           
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });


        Schema::table('users', function (Blueprint $table) {
            $table->string('position_id', 48)->nullable()->index();
            $table->foreign('position_id')->references('id')->on('positions')->onDelete('SET NULL');
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
