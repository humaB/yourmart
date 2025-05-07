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
            $table->string('postex_store_code')->nullable()->after('leopard_id');
        });

        Schema::table('couriers', function (Blueprint $table) {
            $table->smallInteger('is_active')->nullable()->default('1')->after('contact_person_contact');
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
            $table->dropColumn('postex_store_code');
        });

        Schema::table('couriers', function (Blueprint $table) {
            $table->dropColumn('is_active');
        });

    }
};
