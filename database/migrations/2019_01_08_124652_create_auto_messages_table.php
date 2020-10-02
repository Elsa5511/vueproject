<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAutoMessagesTable extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('auto_messages', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->integer('position')->default(1);
            $table->enum('send_to', ['USER', 'PROVIDER', 'BOTH']);
            $table->text('message');
            $table->boolean('enabled')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::dropIfExists('auto_messages');
    }

}
