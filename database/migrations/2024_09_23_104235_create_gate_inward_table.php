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
        Schema::create('inventory_purchase_order_gate_passes', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('po_id');
            $table->bigInteger('added_by');
            $table->timestamps();
        });

        Schema::create('inventory_purchase_order_gate_pass_details', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('igp_id');
            $table->bigInteger('product_id');
            $table->string('quantity');
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
        Schema::dropIfExists('inventory_purchase_order_gate_passes');
        Schema::dropIfExists('inventory_purchase_order_gate_pass_details');
    }
};
