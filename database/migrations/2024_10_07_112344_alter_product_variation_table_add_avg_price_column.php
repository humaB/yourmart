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
        Schema::table('inventory_product_variations', function (Blueprint $table) {
            $table->decimal('avg_price', 15 , 2)->after('sale_price')->default(0);
        });

        Schema::table('inventory_purchase_orders', function (Blueprint $table) {
            $table->decimal('delivery_charges', 15 , 2)->after('tax')->default(0);
        });

        Schema::table('inventory_purchase_order_details', function (Blueprint $table) {
            $table->decimal('delivery_charges', 15 , 2)->after('tax')->default(0);
        });


        Schema::table('inventory_purchase_order_store_received_details', function (Blueprint $table) {
            $table->decimal('delivery_charges', 15 , 2)->after('tax')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('inventory_product_variations', function (Blueprint $table) {
            $table->dropColumn('avg_price');
        });

          Schema::table('inventory_purchase_orders', function (Blueprint $table) {
            $table->dropColumn('delivery_charges');
        });

        Schema::table('inventory_purchase_order_details', function (Blueprint $table) {
            $table->dropColumn('delivery_charges');
        });


        Schema::table('inventory_purchase_order_store_received_details', function (Blueprint $table) {
            $table->dropColumn('delivery_charges');
        });
    }
};
