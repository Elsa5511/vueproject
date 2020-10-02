<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use App\Models\Appointment;

class CreateAppointmentsTable extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('appointments', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('customer_id');
            $table->foreign('customer_id')->references('id')->on('users')->onDelete('cascade');
            $table->uuid('provider_id');
            $table->foreign('provider_id')->references('id')->on('users')->onDelete('cascade');
            $table->enum('status_name', Appointment::STATUS)->default('AWAITING_ACCEPTANCE');
            $table->datetime('date_from');
            $table->datetime('date_to');
            $table->integer('hours')->unsigned();
            $table->string('currency', 3)->default('USD');
            $table->decimal('base_price', 10, 2);
            $table->decimal('services_price', 10, 2);
            $table->decimal('total_price', 10, 2);
            $table->string('address');
            $table->json('location');
            $table->text('notes')->nullable();
            $table->json('attributes')->nullable();
            $table->json('filters_applied')->nullable();
            $table->uuid('chat_id')->nullable()->key();
            $table->foreign('chat_id')->references('id')->on('mc_conversations')->onDelete('SET NULL');
            $table->datetime('accepted_at')->nullable();
            $table->boolean('accepted')->default(false);
            $table->boolean('finished')->default(false);
            $table->enum('payment_status', Appointment::PAYMENT_STATUS)->default('PENDING');
            $table->enum('payment_method', Appointment::PAYMENT_METHOD)->default('UNDEFINED');            
            $table->boolean('customer_rated')->default(false);
            $table->boolean('provider_rated')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::dropIfExists('appointments');
    }

}
