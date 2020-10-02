<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateGalleriesTable extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('galleries', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->enum('type', \App\Models\Resource::TYPES)->index();
            $table->string('owner_type');
            $table->string('owner_id');
            $table->index(['owner_type', 'owner_id']);
            $table->string('name')->nullable();
            $table->string('description')->nullable();
            $table->string('cover_id', 48)->nullable();
            $table->foreign('cover_id')->references('id')->on('resources')->onDelete('SET NULL');
            $table->json('attributes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::dropIfExists('galleries');
    }

}
