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
        Schema::create('inventory_product_shipping_classes', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->smallInteger('is_active')->default(0);
            $table->bigInteger('added_by');
            $table->timestamps();
        });

        Schema::create('inventory_product_shipping_rates', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('shipping_class_id');
            $table->string('rate_type'); // 'free', 'flat', 'weight_based', 'dimension_based'
            $table->integer('minimum_order')->nullable();
            $table->decimal('flat_rate', 10, 2)->nullable();
            $table->decimal('base_rate', 10, 2)->nullable();
            $table->decimal('rate_per_unit', 10, 2)->nullable();
            $table->bigInteger('added_by');
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
        Schema::dropIfExists('inventory_product_shipping_classes');
        Schema::dropIfExists('inventory_product_shipping_rates');
    }
};
