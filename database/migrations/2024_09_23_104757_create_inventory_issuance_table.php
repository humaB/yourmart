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
        Schema::create('inventory_issuances', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('order_id');
            $table->bigInteger('added_by');
            $table->timestamps();
        });

        Schema::create('inventory_issuance_details', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('sin_id');
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
        Schema::dropIfExists('inventory_issuances');
        Schema::dropIfExists('inventory_issuance_details');
    }
};
