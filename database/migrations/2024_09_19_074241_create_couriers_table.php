<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCouriersTable extends Migration
{
    public function up()
    {
        Schema::create('couriers', function (Blueprint $table) {
            $table->id();
            $table->string('courier_name');
            $table->string('contact_person')->nullable();
            $table->string('contact_person_contact')->nullable();
            $table->bigInteger('added_by');
            $table->timestamps();
        });

        Schema::create('courier_added_categories', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('courier_id');
            $table->bigInteger('category_id');
            $table->bigInteger('added_by');
            $table->timestamps();
        });

        Schema::create('courier_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('internal_label');
            $table->string('description')->nullable();
            $table->bigInteger('added_by');
            $table->timestamps();
        });

        Schema::create('courier_categories_ranges', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('category_id');
            $table->string('minimum_quantity')->default(0);
            $table->string('maximum_quantity')->default(0);
            $table->string('base_rate')->default(0);
            $table->string('per_kg')->default(0);
            $table->string('per_kg_rate')->default(0);
            $table->string('fac_tax')->default(0);
            $table->string('gst_tax')->default(0);
            $table->string('total')->default(0);
            $table->bigInteger('added_by');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('couriers');
        Schema::dropIfExists('courier_categories');
        Schema::dropIfExists('courier_categories_range');
        Schema::dropIfExists('courier_added_categories');
    }
}
