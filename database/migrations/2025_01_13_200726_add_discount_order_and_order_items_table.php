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
            $table->decimal('discount', 15, 2)->default(0)->after('remaining_amount');
        });

        Schema::table('order_items', function (Blueprint $table) {
            $table->decimal('discount', 15, 2)->default(0)->after('courier_cost');
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
            $table->dropColumn('discount');
        });

        Schema::table('order_items', function (Blueprint $table) {
            $table->dropColumn('discount');
        });
    }
};
