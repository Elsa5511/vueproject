<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUserProfilesTable extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('user_profiles', function (Blueprint $table) {
            $table->uuid('user_id')->primary();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->string('first_name')->nullable();
            $table->string('last_name')->nullable();
            $table->string('contact_email')->nullable();
            $table->string('identity_id')->nullable();
            $table->integer('country_id')->unsigned()->nullable();
            $table->foreign('country_id')->references('id')->on('countries')->onDelete('cascade');
            $table->integer('city_id')->unsigned()->nullable();
            $table->foreign('city_id')->references('id')->on('cities')->onDelete('cascade');
            $table->string('address')->nullable();
            $table->string('state')->nullable();
            $table->json('payment_info')->nullable();
            $table->integer('postal_code')->unsigned(20)->nullable();
            $table->string('contact_phone')->nullable();
            $table->string('website')->nullable();
            $table->string('linkedin_profile')->nullable();
            $table->string('facebook_profile')->nullable();
            $table->string('twitter_profile')->nullable();
            $table->string('youtube_profile')->nullable();
            $table->string('instagram_profile')->nullable();
            $table->string('hair_color', 32)->nullable();
            $table->string('eyes_color', 32)->nullable();
            $table->string('skin_color', 32)->nullable();
            $table->string('corporal_dimensions', 32)->nullable();
            $table->float('weight')->nullable();
            $table->string('corporal_complexion', 32)->nullable();
            $table->float('height')->nullable();            
            $table->string('sexual_orientation', 32)->nullable();
            $table->float('hourly_rate')->nullable()->default(0.00);
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
        Schema::dropIfExists('user_profiles');
    }

}
