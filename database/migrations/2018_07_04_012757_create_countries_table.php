<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCountriesTable extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('countries', function (Blueprint $table) {
            $table->increments('id');
            $table->string('iso_02', 2)->unique();
            $table->string('name')->index();
            $table->integer('phonecode');
            $table->boolean('work_enabled')->default(false);
            $table->boolean('register_enabled')->default(true);
        });

        Schema::table('users', function (Blueprint $table) {
            $table->integer('country_id')->nullable()->unsigned();
            $table->foreign('country_id')->references('id')->on('countries')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::dropIfExists('countries');

        Schema::table('users', function (Blueprint $table) {
            $table->drop('country_id');
        });
    }

}
