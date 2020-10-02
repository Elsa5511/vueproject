<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSettingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('settings', function (Blueprint $table) {
           $table->increments('id');
            $table->string('name')->unique();
            $table->string('group')->default('general');
            $table->string('description')->nullable();
            $table->string('type')->default('string');
            $table->text('value')->nullable();
            $table->timestamps();
            $table->index('group');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('settings');
    }
}
