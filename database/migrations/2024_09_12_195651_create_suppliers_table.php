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
        Schema::create('suppliers', function (Blueprint $table) {
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

            // Image Fields
            $table->string('cnic_front_image')->nullable();
            $table->string('cnic_back_image')->nullable();
            $table->string('profile_image')->nullable();

            $table->smallInteger('status');

            $table->bigInteger('user_id')->default(0);

            $table->timestamps();
        });

        Schema::create('supplier_shops', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('supplier_id');
            $table->string('store_name');
            $table->string('store_type');
            // Optional Fields
            $table->string('store_url')->nullable();
            $table->string('social_media_profile_link')->nullable();
            $table->text('business_description')->nullable();
            $table->text('product_description')->nullable();
            $table->text('comment')->nullable();
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
        Schema::dropIfExists('suppliers');
        Schema::dropIfExists('supplier_shops');
    }
};
