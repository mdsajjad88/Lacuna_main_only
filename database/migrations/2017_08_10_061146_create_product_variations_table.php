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
        Schema::create('product_variations', function (Blueprint $table) {
            $table->id(); // Auto-incrementing primary key `id`
            $table->unsignedBigInteger('variation_template_id')->nullable();
            $table->string('name', 191);
            $table->unsignedBigInteger('product_id')->nullable();
            $table->boolean('is_dummy')->default(1);
            $table->timestamps(); // Creates `created_at` and `updated_at` columns

            // Optionally, add foreign key constraints if needed
            $table->foreign('variation_template_id')->references('id')->on('variation_templates')->onDelete('set null');
            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');

           
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('product_variations');
    }
};
