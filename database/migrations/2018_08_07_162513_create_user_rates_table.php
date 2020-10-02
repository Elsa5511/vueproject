<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUserRatesTable extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('user_rates', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('appointment_id');
            $table->foreign('appointment_id')->references('id')->on('appointments')->onDelete('cascade');
            $table->uuid('user_id');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->enum('user_role', ['CUSTOMER', 'PROVIDER']);
            $table->integer('rate')->unsigned();
            $table->text('review')->nullable();
            $table->uuid('author_id');
            $table->foreign('author_id')->references('id')->on('users')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::dropIfExists('user_rates');
    }

}
