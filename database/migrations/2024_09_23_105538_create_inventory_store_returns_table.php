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
        Schema::create('inventory_store_returns', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('order_id')->default(0);
            $table->bigInteger('dropshipper_id');
            $table->string('remarks')->nullable();
            $table->bigInteger('added_by');
            $table->timestamps();
        });

        Schema::create('inventory_store_return_details', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('srn_id');
            $table->bigInteger('product_id');
            $table->string('quantity');
            $table->string('price');
            $table->string('total');
            $table->bigInteger('added_by');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('inventory_store_returns');
        Schema::dropIfExists('inventory_store_return_details');
    }
};
