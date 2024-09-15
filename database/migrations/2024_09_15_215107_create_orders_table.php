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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('customer_name');
            $table->string('address');
            $table->string('phone_number');
            $table->string('phone_number2')->nullable();
            $table->bigInteger('city_id');
            $table->bigInteger('courier_service_id')->default(0);
            $table->bigInteger('shop_id')->default(0);
            $table->text('instructions')->nullable();
            $table->string('total_bill');
            $table->string('paid_amount');
            $table->smallInteger('status');
            $table->bigInteger('belongs_to');
            $table->timestamps();
        });

        Schema::create('order_items', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('order_id');
            $table->bigInteger('product_variation_id');
            $table->string('price');
            $table->string('quantity');
            $table->string('selling_price');
            $table->bigInteger('belongs_to');
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
        Schema::dropIfExists('orders');
        Schema::dropIfExists('order_items');
    }
};
