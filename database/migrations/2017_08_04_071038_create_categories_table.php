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
        Schema::create('categories', function (Blueprint $table) {

            $table->id(); // Adds an auto-incrementing primary key field `id`
            $table->string('name', 191);
            $table->string('ecommerce_name', 255)->nullable();
            $table->unsignedBigInteger('business_id');
            $table->string('short_code', 191)->nullable();
            $table->unsignedBigInteger('parent_id')->nullable();
            $table->boolean('is_ecommerce')->default(0);
            $table->unsignedBigInteger('created_by');
            $table->integer('woocommerce_cat_id')->nullable();
            $table->string('category_type', 191)->nullable();
            $table->text('description')->nullable();
            $table->boolean('is_us_product')->default(0);
            $table->string('slug', 191)->nullable();
            $table->timestamps();
            $table->softDeletes();


             $table->foreign('business_id')->references('id')->on('business');
            $table->foreign('created_by')->references('id')->on('users');
            $table->foreign('parent_id')->references('id')->on('categories');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('categories');
    }
};
