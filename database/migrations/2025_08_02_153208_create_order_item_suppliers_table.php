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
        Schema::create('order_item_suppliers', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('order_id');
            $table->bigInteger('order_item_id');
            $table->bigInteger('supplier_id');
            $table->bigInteger('product_id');
            $table->string('quantity');
            $table->timestamps();
        });

        Schema::create('supplier_stocks', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('product_id');
            $table->bigInteger('supplier_id');
            $table->string('quantity');
            $table->timestamps();
        });

        Schema::table('inventory_purchase_order_store_received', function (Blueprint $table) {
            $table->bigInteger('supplier_id')->after('po_id')->nullable();
        });

        Schema::table('inventory_purchase_order_store_received_details', function (Blueprint $table) {
            $table->bigInteger('supplier_id')->after('grn_id')->nullable();
        });

        Schema::table('inventory_purchase_orders', function (Blueprint $table) {
            $table->smallInteger('supplier_stock')->after('approved_date')->default('0')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('order_item_suppliers');
        Schema::dropIfExists('supplier_stocks');

        Schema::table('inventory_purchase_order_store_received', function (Blueprint $table) {
            $table->dropColumn('supplier_id');
        });

        Schema::table('inventory_purchase_order_store_received_details', function (Blueprint $table) {
            $table->dropColumn('supplier_id');
        });

        // Schema::table('inventory_purchase_orders', function (Blueprint $table) {
        //     $table->dropColumn('supplier_stock');
        // });

    }
};
