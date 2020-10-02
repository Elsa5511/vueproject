<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRefererComissionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('referer_comissions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('appointment_id');
            $table->foreign('appointment_id')->references('id')->on('appointments')->onDelete('cascade');
            $table->uuid('user_id');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->uuid('refered_id');
            $table->foreign('refered_id')->references('id')->on('users')->onDelete('cascade');
            $table->integer('amount')->default(0);
            $table->string('currency', 32)->default('USD');
            $table->string('status', 32)->default('DEBIDO');
            $table->dateTime('paid_on')->nullable();
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
        Schema::dropIfExists('referer_comissions');
    }
}
