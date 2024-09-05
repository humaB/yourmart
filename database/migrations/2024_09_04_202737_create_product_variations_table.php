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
        // Main Products Table
        Schema::create('inventory_products', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug');
            $table->text('short_description')->nullable();
            $table->bigInteger('brand_id');
            $table->bigInteger('category_id');
            $table->bigInteger('shipping_method_id');
            $table->string('hero_image')->nullable();
            $table->string('video_link')->nullable();
            $table->text('product_description')->nullable();
            $table->text('product_highlight')->nullable();
            $table->text('warranty')->nullable();
            $table->string('max_quantity')->nullable();
            $table->string('quantity_step')->nullable();
            $table->smallInteger('status')->nullable();
            $table->bigInteger('added_by');
            $table->timestamps();
        });


        // Product Tag Table
        Schema::create('inventory_product_added_tags', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('product_id');
            $table->bigInteger('tag_id');
            $table->bigInteger('added_by');
            $table->timestamps();
        });

        // Product Attributes Table
        Schema::create('inventory_product_attributes', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('product_id');
            $table->bigInteger('attribute_id');
            $table->bigInteger('added_by');
            $table->timestamps();
        });

        // Product Variations Table
        Schema::create('inventory_product_variations', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('product_id');
            $table->string('sku');
            $table->bigInteger('color_id')->default(0);
            $table->bigInteger('size_id')->default(0);
            $table->string('regular_price');
            $table->string('sale_price')->nullable();
            $table->string('stock');
            $table->bigInteger('added_by');
            $table->timestamps();
        });

        // Product Variation Images Table
        Schema::create('inventory_product_variation_images', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('product_variation_id');
            $table->string('image_id');
            $table->bigInteger('added_by');
            $table->timestamps();
        });

        // Discounts per Quantity Table
        Schema::create('inventory_product_discount_per_qty', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('product_id');
            $table->string('quantity');
            $table->string('price'); // Discounted price
            $table->bigInteger('added_by');
            $table->timestamps();
        });

        Schema::create('inventory_product_dimensions', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('product_id');
            $table->string('weight')->nullable();
            $table->string('length')->nullable();
            $table->string('height')->nullable();
            $table->string('width')->nullable();
            $table->bigInteger('added_by');
            $table->timestamps();
        });

        Schema::create('inventory_product_sale_schedules', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('product_id');
            $table->string('from');
            $table->string('to');
            $table->string('price');
            $table->bigInteger('added_by');
            $table->timestamps();
        });

        Schema::create('inventory_product_publish_schedules', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('product_id');
            $table->string('date');
            $table->bigInteger('added_by');
            $table->timestamps();
        });

        Schema::create('inventory_product_publish_upsell_and_crosssells', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('product_id');
            $table->string('type');
            $table->bigInteger('reference_product_id');
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
        Schema::dropIfExists('inventory_products');
        Schema::dropIfExists('inventory_product_added_tags');
        Schema::dropIfExists('inventory_product_attributes');
        Schema::dropIfExists('inventory_product_variations');
        Schema::dropIfExists('inventory_product_discount_per_qty');
        Schema::dropIfExists('inventory_product_dimensions');
        Schema::dropIfExists('inventory_product_sale_schedules');
        Schema::dropIfExists('inventory_product_publish_schedules');
        Schema::dropIfExists('inventory_product_variation_images');
        Schema::dropIfExists('inventory_product_publish_upsell_and_crosssells');
    }
};
