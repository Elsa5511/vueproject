<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersTable extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('users', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->string('password');
            $table->string('unique_id')->unique();
            $table->boolean('email_verified')->default(0);
            $table->string('email_token')->nullable();
            $table->boolean('phone_verified')->default(0);
            $table->string('phone_token')->nullable();
            $table->enum('role', \App\Models\User::ROLES)->default('USER');
            $table->integer('agent_id')->unsigned()->nullable();
            $table->enum('presence', \App\Models\User::PRESENCE_STATUS)->default('UNKNOWN')->index();
            $table->enum('status', \App\Models\User::STATUS)->default('ACTIVE')->index();
            $table->enum('gender', \App\Models\User::GENDER)->default('MALE');
            $table->string('avatar')->nullable();
            $table->rememberToken();
            $table->timestamps();

            $table->foreign('agent_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::dropIfExists('users');
    }

}
