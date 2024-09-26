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
            $table->bigInteger('order_no')->after('id');
            $table->bigInteger('range_id')->after('courier_service_id');
            $table->decimal('courier_service_price',10,2)->after('range_id');
            $table->decimal('packaging_price',10,2)->after('selling_price');
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
            $table->dropColumn('order_no');
            $table->dropColumn('range_id');
            $table->dropColumn('courier_service_price',10,2);
            $table->dropColumn('packaging_price',10,2);
        });
    }
};
