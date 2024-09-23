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
        Schema::create('inventory_purchase_orders', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('supplier_id');
            $table->string('total_amount');
            $table->string('remaining_amount');
            $table->string('tax');
            $table->string('discount');
            $table->smallInteger('payment_term_advance');
            $table->smallInteger('payment_term_after_delivery');
            $table->bigInteger('approved_by')->default(0);
            $table->string('approved_date')->nullable();
            $table->smallInteger('status');
            $table->bigInteger('added_by');
            $table->timestamps();
        });

        Schema::create('inventory_purchase_order_details', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('po_id');
            $table->bigInteger('product_id');
            $table->bigInteger('product_variation_id');
            $table->string('gate_received_quantity');
            $table->string('store_received_quantity');
            $table->string('quantity');
            $table->string('price');
            $table->string('tax')->nullable();
            $table->string('discount')->nullable();
            $table->string('total');
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
        Schema::dropIfExists('inventory_purchase_orders');
        Schema::dropIfExists('inventory_purchase_order_details');
    }
};
