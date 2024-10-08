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
        Schema::create('order_leopard_statuses', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('order_id');
            $table->string('leopard_label');
            $table->string('short_code');
            $table->string('internal_label');
            $table->string('receiver_name')->nullable();
            $table->string('reason')->nullable();
            $table->timestamp('time')->useCurrent();
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
        Schema::dropIfExists('order_leopard_statuses');
    }
};
