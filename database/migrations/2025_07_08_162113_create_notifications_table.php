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
        Schema::create('notifications', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('message');
            $table->text('link')->nullable();
            $table->string('image')->nullable();
            $table->string('color');
            $table->smallInteger('is_public')->default('0');
            $table->bigInteger('user_id')->default('0')->nullable();
            $table->timestamps();
        });

        Schema::create('notification_seens', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('notification_id');
            $table->bigInteger('user_id');
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
        Schema::dropIfExists('notifications');
    }
};
