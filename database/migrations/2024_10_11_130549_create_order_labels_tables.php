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
        Schema::create('order_labels', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('order_id');
            $table->string('attachment');
            $table->timestamps();
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->string('type')->after('id')->default('Normal');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('order_labels');

        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn('type');
        });
    }
};
