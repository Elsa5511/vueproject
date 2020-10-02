<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUserTokensTable extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('user_tokens', function (Blueprint $table) {
            $table->increments('id');
            $table->uuid('user_id');
            $table->boolean('is_user')->default(true);
            $table->string('token', 1024);
            $table->string('push_token', 1024)->key()->nullable();
            $table->string('version', 8)->nullable()->default('0.0.0');
            $table->string('client_type', 32)->nullable()->default('unknown');
            $table->string('last_ip')->nullable();
            $table->json('attributes')->nullable();
            $table->timestamp('last_access')->useCurrent();
            $table->timestamps();

            //$table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::dropIfExists('user_tokens');
    }

}
