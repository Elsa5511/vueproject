<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProviderServicesTable extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('provider_services', function (Blueprint $table) {
            $table->uuid('provider_id');
            $table->uuid('service_id');
            $table->decimal('price', 10, 2)->default(0.0);
            $table->string('description')->nullable();
            $table->json('attributes')->nullable();
            $table->timestamps();


            $table->primary('provider_id', 'service_id');
            $table->foreign('provider_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('service_id')->references('id')->on('services')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::dropIfExists('provider_services');
    }

}
