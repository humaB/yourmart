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
        $tables = [
            'banks',
            'cities',
            'drop_shippers',
            'drop_shipper_shops',
            'failed_jobs',
            'inventory_products',
            'inventory_product_added_tags',
            'inventory_product_attachments',
            'inventory_product_attributes',
            'inventory_product_attribute_types',
            'inventory_product_brands',
            'inventory_product_categories',
            'inventory_product_colors',
            'inventory_product_dimensions',
            'inventory_product_discount_per_qty',
            'inventory_product_minimum_order_quantities',
            'inventory_product_publish_schedules',
            'inventory_product_publish_upsell_and_crosssells',
            'inventory_product_sale_schedules',
            'inventory_product_shipping_classes',
            'inventory_product_shipping_rates',
            'inventory_product_sizes',
            'inventory_product_tags',
            'inventory_product_variations',
            'inventory_product_variation_images',
            'migrations',
            'orders',
            'order_comments',
            'order_items',
            'password_resets',
            'personal_access_tokens',
            'suppliers',
            'supplier_shops',
            'users'
        ];

        foreach ($tables as $table) {
            Schema::table($table, function (Blueprint $table) {
                $table->softDeletes();
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $tables = [
            'banks',
            'cities',
            'drop_shippers',
            'drop_shipper_shops',
            'failed_jobs',
            'inventory_products',
            'inventory_product_added_tags',
            'inventory_product_attachments',
            'inventory_product_attributes',
            'inventory_product_attribute_types',
            'inventory_product_brands',
            'inventory_product_categories',
            'inventory_product_colors',
            'inventory_product_dimensions',
            'inventory_product_discount_per_qty',
            'inventory_product_minimum_order_quantities',
            'inventory_product_publish_schedules',
            'inventory_product_publish_upsell_and_crosssells',
            'inventory_product_sale_schedules',
            'inventory_product_shipping_classes',
            'inventory_product_shipping_rates',
            'inventory_product_sizes',
            'inventory_product_tags',
            'inventory_product_variations',
            'inventory_product_variation_images',
            'migrations',
            'orders',
            'order_comments',
            'order_items',
            'password_resets',
            'personal_access_tokens',
            'suppliers',
            'supplier_shops',
            'users'
        ];

        foreach ($tables as $table) {
            Schema::table($table, function (Blueprint $table) {
                $table->dropSoftDeletes();
            });
        }
    }
};
