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
        Schema::create('drop_shipper_levels', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('dropshipper_id');
            $table->bigInteger('user_id');
            $table->string('level');
            $table->smallInteger('is_completed')->default(0);
            $table->timestamps();
        });

        Schema::create('drop_shipper_level_details', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('dropshipper_level_id');
            $table->text('requirement')->nullable();
            $table->smallInteger('is_completed');
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
        Schema::dropIfExists('drop_shipper_levels');
        Schema::dropIfExists('drop_shipper_level_details');
    }
};
