<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateIdentityVerificationRequestsTable extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('identity_verification_requests', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('user_id')->key();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->string('identity_id');
            $table->integer('country_id')->unsigned()->nullable();
            $table->foreign('country_id')->references('id')->on('countries')->onDelete('cascade');
            $table->json('data')->nullable();
            $table->string('status')->default('PENDING');
            $table->timestamps();
        });

        Schema::table('users', function (Blueprint $table) {
            $table->uuid('id_verification_request')->nullable()->key();
            $table->foreign('id_verification_request')->references('id')->on('identity_verification_requests')->onDelete('SET NULL');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::dropIfExists('identity_verification_requests');
    }

}
