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
        Schema::table('orders', function (Blueprint $table) {
            $table->integer('subtotal_tax')->default(0)->nullable()->after('remaining_amount');
            $table->integer('product_cost')->default(0)->nullable()->after('subtotal_tax');
            $table->integer('shipping_tax')->default(0)->nullable()->after('courier_service_price');
            $table->integer('profit_tax')->default(0)->nullable()->after('total_profit');
        });

        Schema::table('order_items', function (Blueprint $table) {
            $table->integer('subtotal_tax')->default(0)->nullable()->after('quantity');
            $table->integer('shipping_tax')->default(0)->nullable()->after('courier_cost');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn('subtotal_tax');
            $table->dropColumn('product_cost');
            $table->dropColumn('shipping_tax');
            $table->dropColumn('profit_tax');
        });

        Schema::table('order_items', function (Blueprint $table) {
            $table->dropColumn('subtotal_tax');
            $table->dropColumn('shipping_tax');
        });
    }
};
