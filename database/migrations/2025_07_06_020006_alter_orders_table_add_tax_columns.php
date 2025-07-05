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
            $table->integer('subtotal_tax')->default(0)->after('remaining_amount');
            $table->integer('shipping_tax')->default(0)->after('courier_service_price');
            $table->integer('profit_tax')->default(0)->after('total_profit');
        });

        Schema::table('order_items', function (Blueprint $table) {
            $table->integer('subtotal_tax')->default(0)->after('quantity');
            $table->integer('shipping_tax')->default(0)->after('courier_cost');
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
            $table->dropColumn('shipping_tax');
            $table->dropColumn('profit_tax');
        });

        Schema::table('order_items', function (Blueprint $table) {
            $table->dropColumn('subtotal_tax');
            $table->dropColumn('shipping_tax');
        });
    }
};
