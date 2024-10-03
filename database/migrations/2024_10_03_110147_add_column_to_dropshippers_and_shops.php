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
        Schema::table('drop_shippers', function (Blueprint $table) {
            $table->string('group_id')->after('leopard_id')->nullable();
        });

        // Add columns to table2
        Schema::table('drop_shipper_shops', function (Blueprint $table) {
            $table->string('account_head_id')->after('dropshipper_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Drop columns from table1
        Schema::table('drop_shippers', function (Blueprint $table) {
            $table->dropColumn('group_id'); // Drop the columns in case of rollback
        });

        // Drop columns from table2
        Schema::table('drop_shipper_shops', function (Blueprint $table) {
            $table->dropColumn('account_head_id'); // Drop the columns in case of rollback
        });
    }
};
