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
        Schema::create('inventory_oboselete_products', function (Blueprint $table) {
            $table->id();
            $table->string('type');
            $table->string('remarks')->nullable();
            $table->bigInteger('added_by');
            $table->timestamps();
        });

        Schema::create('inventory_oboselete_product_details', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('pob_id');
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
        Schema::dropIfExists('inventory_oboselete_products');
        Schema::dropIfExists('inventory_oboselete_product_details');
    }
};
