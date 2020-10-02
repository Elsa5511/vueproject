<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateResourcesTable extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('resources', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->enum('type', \App\Models\Resource::TYPES)->index();
            $table->string('owner_type');
            $table->string('owner_id');
            $table->index(['owner_type', 'owner_id']);
            $table->string('mime_type')->nullable();
            $table->string('uri');
            $table->string('name')->nullable();
            $table->string('description')->nullable();
            $table->float('size')->nullable();
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
        Schema::dropIfExists('resources');
    }

}
