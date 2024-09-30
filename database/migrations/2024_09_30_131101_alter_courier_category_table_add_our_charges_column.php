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
        Schema::table('courier_categories', function (Blueprint $table) {
            $table->decimal('our_charges', 15 , 2)->after('internal_label')->default(0);
        });

        Schema::table('courier_categories_ranges', function (Blueprint $table) {
            $table->decimal('our_charges', 15 , 2)->after('id')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('courier_categories', function (Blueprint $table) {
            $table->dropColumn('our_charges');
        });

        Schema::table('courier_categories_ranges', function (Blueprint $table) {
            $table->dropColumn('our_charges');
        });
    }
};
