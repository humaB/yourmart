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
        Schema::create('inventory_product_other_charges', function (Blueprint $table) {
            $table->id();
            $table->string('type');
            $table->decimal('amount');
            $table->smallInteger('status')->default(0);
            $table->bigInteger('added_by');
            $table->timestamps();
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->string('no_of_labels')->after('total_weight')->default('0');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('inventory_product_other_charges');
    }
};
