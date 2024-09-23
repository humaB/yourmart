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
        Schema::create('inventory_supplier_returns', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('po_id')->default(0);
            $table->bigInteger('supplier_id');
            $table->string('remarks')->nullable();
            $table->bigInteger('added_by');
            $table->timestamps();
        });

        Schema::create('inventory_supplier_return_details', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('vrn_id');
            $table->bigInteger('product_id');
            $table->string('quantity');
            $table->string('price');
            $table->string('tax')->nullable();
            $table->string('discount')->nullable();
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
        Schema::dropIfExists('inventory_supplier_returns');
        Schema::dropIfExists('inventory_supplier_return_details');
    }
};
