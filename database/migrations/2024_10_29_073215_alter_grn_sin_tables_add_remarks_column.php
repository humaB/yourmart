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
        Schema::table('inventory_purchase_order_store_received_details', function (Blueprint $table) {
            $table->string('remarks')->after('total')->nullable();
        });

        Schema::table('inventory_issuance_details', function (Blueprint $table) {
            $table->string('remarks')->after('total')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('inventory_purchase_order_store_received_details', function (Blueprint $table) {
            $table->dropColumn('remarks');
        });

        Schema::table('inventory_issuance_details', function (Blueprint $table) {
            $table->dropColumn('remarks');
        });
    }
};
