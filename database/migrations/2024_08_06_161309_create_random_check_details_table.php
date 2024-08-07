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
        Schema::create('random_check_details', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('random_check_id')->nullable('random_checks');
            $table->unsignedBigInteger('product_id')->nullable('products');
            $table->unsignedBigInteger('location_id')->nullable();
            $table->unsignedBigInteger('variation_id')->nullable();
            $table->string('category_name');
            $table->string('product_name');
            $table->string('sku')->nullable();
            $table->string('brand_name')->nullable();
            $table->decimal('current_stock', 15, 2)->default(0);
            $table->decimal('physical_count', 15, 2)->nullable()->default(0);
            $table->text('comment')->nullable();
            $table->timestamps();
            $table->foreign('random_check_id')->references('id')->on('random_checks')->onDelete('cascade')->name('fk_random_checks_in_details_table');
            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade')->name('fk_product_in_details_table');
            $table->foreign('location_id')->references('id')->on('business_locations')->onDelete('cascade')->name('fk_location_in_details_table');
            $table->foreign('variation_id')->references('id')->on('variations')->onDelete('cascade')->name('fk_variation_in_details_table');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('random_check_details');
    }
};