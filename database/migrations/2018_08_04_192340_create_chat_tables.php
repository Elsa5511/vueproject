<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateChatTables extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('mc_conversations', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->boolean('private')->default(true);
            $table->text('data')->nullable();
            $table->timestamps();
        });
        Schema::create('mc_messages', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('temp_id')->nullable();
            $table->text('body');
            $table->uuid('conversation_id');
            $table->uuid('user_id');
            $table->string('type')->default('text');
            $table->int('sent_at');
            $table->timestamps();
            $table->foreign('user_id')
                    ->references('id')
                    ->on('users')
                    ->onDelete('cascade');
            $table->foreign('conversation_id')
                    ->references('id')
                    ->on('mc_conversations')
                    ->onDelete('cascade');
        });
        Schema::create('mc_conversation_user', function (Blueprint $table) {
            $table->uuid('user_id');
            $table->uuid('conversation_id');
            $table->primary(['user_id', 'conversation_id']);
            $table->timestamps();
            $table->foreign('conversation_id')
                    ->references('id')->on('mc_conversations')
                    ->onDelete('cascade');
            $table->foreign('user_id')
                    ->references('id')->on('users')
                    ->onDelete('cascade');
        });
        Schema::create('mc_message_notification', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('message_id');
            $table->uuid('conversation_id');
            $table->uuid('user_id');
            $table->boolean('is_seen')->default(false);
            $table->boolean('is_sender')->default(false);
            $table->boolean('flagged')->default(false);
            $table->timestamps();
            $table->softDeletes();
            $table->index(['user_id', 'message_id']);
            $table->foreign('message_id')
                    ->references('id')->on('mc_messages')
                    ->onDelete('cascade');
            $table->foreign('conversation_id')
                    ->references('id')->on('mc_conversations')
                    ->onDelete('cascade');
            $table->foreign('user_id')
                    ->references('id')->on('users')
                    ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::dropIfExists('mc_conversations');
        Schema::dropIfExists('mc_messages');
        Schema::dropIfExists('mc_conversation_user');
        Schema::dropIfExists('mc_message_notification');
    }

}
