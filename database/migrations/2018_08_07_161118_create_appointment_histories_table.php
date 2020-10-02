<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use App\Models\Appointment;

class CreateAppointmentHistoriesTable extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('appointment_histories', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('appointment_id');
            $table->foreign('appointment_id')->references('id')->on('appointments')->onDelete('cascade');
            $table->uuid('user_id');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->enum('status', Appointment::STATUS);
            $table->text('description')->nullable();
            $table->json('attributes')->nullable();
            $table->timestamps();
        });

        Schema::table('appointments', function (Blueprint $table) {
            $table->uuid('current_status_id')->nullable();
            $table->foreign('current_status_id')->references('id')->on('appointment_histories')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::table('appointments', function (Blueprint $table) {
            $table->drop('current_status_id');
        });
        Schema::dropIfExists('appointment_histories');
    }

}
