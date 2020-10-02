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
            $table->uuid('id');
            $table->string('name');
            $table->date('dob')->nullable();
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->string('password');
            $table->boolean('email_verified')->default(0);
            $table->string('email_token')->nullable();
            $table->boolean('phone_verified')->default(0);
            $table->string('phone_token')->nullable();
            $table->enum('role', \App\Models\User::ROLES)->default('USER')->index();
            $table->uuid('agent_id')->nullable();
            $table->enum('presence', \App\Models\User::PRESENCE_STATUS)->default('UNKNOWN')->index();
            $table->enum('status', \App\Models\User::STATUS)->default('ACTIVE')->index();
            $table->enum('work_status', \App\Models\User::WORK_STATUS)->default('UNAVAILABLE')->index();
            $table->enum('gender', \App\Models\User::GENDER)->default('MALE')->index();
            $table->boolean('identity_verified')->default(false);
            $table->string('avatar')->nullable();
            $table->rememberToken();
            $table->json('attributes')->nullable();
            $table->decimal('rate', 3, 2)->unsigned()->default(0);
            $table->timestamps();
            $table->primary('id');
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
