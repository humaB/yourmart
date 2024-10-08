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
        Schema::table('drop_shipper_shops', function (Blueprint $table) {
            $table->decimal('total_payable')->after('business_description')->default(0);
            $table->decimal('total_paid',15 ,2)->after('total_payable')->default(0);
            $table->decimal('total_remaining',15 ,2)->after('total_paid')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('drop_shipper_shops', function (Blueprint $table) {
            $table->dropColumn('total_payable');
            $table->dropColumn('total_paid');
            $table->dropColumn('total_remaining');
        });
    }
};
