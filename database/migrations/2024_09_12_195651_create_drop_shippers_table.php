<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('drop_shippers', function (Blueprint $table) {
            $table->id();
            $table->string('full_name');
            $table->string('email');
            $table->string('password');
            $table->string('cnic_number');
            $table->string('whatsapp_number');
            $table->string('address');
            $table->bigInteger('city_id');
            $table->bigInteger('bank_id');
            $table->string('account_number');
            $table->string('account_title');

            // Optional Fields
            $table->string('store_name')->nullable();
            $table->string('store_url')->nullable();
            $table->string('social_media_profile_link')->nullable();
            $table->text('business_description')->nullable();

            // Image Fields
            $table->string('cnic_front_image')->nullable();
            $table->string('cnic_back_image')->nullable();
            $table->string('profile_image')->nullable();

            $table->smallInteger('status');

            $table->timestamps();
        });

        Schema::create('banks', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->timestamps();
        });

        Schema::create('cities', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('banks');
        Schema::dropIfExists('cities');
        Schema::dropIfExists('drop_shippers');
    }
};
