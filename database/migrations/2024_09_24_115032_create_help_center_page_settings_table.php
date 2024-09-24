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
        Schema::create('help_center_page_settings', function (Blueprint $table) {
            $table->id(); // Primary key
            $table->string('name'); // Name column
            $table->text('description'); // Description column
            $table->string('type'); // Type column
            $table->unsignedBigInteger('added_by'); // For tracking who added the content
            $table->timestamps(); // Created at and updated at columns
            $table->softDeletes(); // Soft delete column
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('help_center_page_settings');
    }
};
