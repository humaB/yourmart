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
            $table->decimal('remaining_amount')->after('paid_amount');
            $table->text('additional_information')->after('order_note')->nullable();
            $table->string('payment_method')->after('remaining_amount');
            $table->string('payment_proof_attachment')->after('payment_method')->nullable();
            $table->string('tracking_number')->after('status')->nullable();
            $table->text('slip_link')->after('tracking_number')->nullable();
        });

        Schema::table('order_items', function (Blueprint $table) {
            $table->decimal('sell_price' ,15 , 2)->after('quantity');
            $table->decimal('courier_cost' ,15 , 2)->after('sell_price')->default(0);
            $table->decimal('packaging_cost' ,15 , 2)->after('courier_cost')->default(0);
        });

        Schema::table('cities', function (Blueprint $table) {
            $table->bigInteger('courier_city_id')->after('name')->default(0);
        });

        Schema::table('drop_shippers', function (Blueprint $table) {
            $table->decimal('total_payable')->after('account_title')->default(0);
            $table->decimal('total_paid')->after('total_payable')->default(0);
            $table->decimal('remaining_amount')->after('total_payable')->default(0);
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
            $table->dropColumn('remaining_amount');
            $table->dropColumn('additional_information');
            $table->dropColumn('payment_method');
            $table->dropColumn('payment_proof_attachment');
            $table->dropColumn('tracking_number');
            //$table->dropColumn('slip_link');
        });

        Schema::table('order_items', function (Blueprint $table) {
            $table->dropColumn('sell_price');
            $table->dropColumn('courier_cost');
            $table->dropColumn('packaging_cost');
        });

        Schema::table('cities', function (Blueprint $table) {
            $table->dropColumn('courier_city_id');
        });

        Schema::table('drop_shippers', function (Blueprint $table) {
            $table->dropColumn('total_payable');
            $table->dropColumn('total_paid');
            $table->dropColumn('remaining_amount');
        });
    }
};
