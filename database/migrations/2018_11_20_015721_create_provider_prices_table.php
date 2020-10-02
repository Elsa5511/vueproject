<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProviderPricesTable extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('provider_prices', function (Blueprint $table) {
            $table->uuid('provider_id')->key();
            $table->integer('hours');
            $table->string('currency', 3)->nullable();
            $table->integer('value')->nullable();
            $table->timestamps();
            
            $table->foreign('provider_id')->references('id')->on('users')->onDelete('cascade');

            $table->primary(['provider_id', 'hours']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::dropIfExists('provider_prices');
    }

}
