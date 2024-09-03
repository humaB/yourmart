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
        Schema::create('inventory_product_brands', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug');
            $table->text('description')->nullable();
            $table->string('logo')->nullable();
            $table->bigInteger('added_by');
            $table->timestamps();
        });

        Schema::create('inventory_product_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug');
            $table->text('description')->nullable();
            $table->bigInteger('parent_id')->default(0);
            $table->bigInteger('added_by');
            $table->timestamps();
        });

        Schema::create('inventory_product_colors', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->char('code',4)->uppercase();
            $table->string('hex')->nullable();
            $table->string('image')->nullable();
            $table->bigInteger('added_by');
            $table->timestamps();
        });

        Schema::create('inventory_product_sizes', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->char('code',4)->uppercase();
            $table->integer('sort_by');
            $table->bigInteger('added_by');
            $table->timestamps();
        });

        Schema::create('inventory_product_tags', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug'); // URL-friendly version of the tag name
            $table->bigInteger('added_by');
            $table->timestamps();
        });

        Schema::create('inventory_product_attribute_types', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug');
            $table->text('description')->nullable();
            $table->bigInteger('parent_id')->default(0);
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
        Schema::dropIfExists('inventory_product_brands');
        Schema::dropIfExists('inventory_product_categories');
        Schema::dropIfExists('inventory_product_colors');
        Schema::dropIfExists('inventory_product_sizes');
        Schema::dropIfExists('inventory_product_tags');
        Schema::dropIfExists('inventory_product_attribute_types');
    }
};
