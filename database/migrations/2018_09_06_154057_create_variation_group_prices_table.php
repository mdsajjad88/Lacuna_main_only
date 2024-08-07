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
        Schema::create('variation_group_prices', function (Blueprint $table) {
            $table->id(); // Auto-incrementing primary key `id`
            $table->unsignedBigInteger('variation_id')->nullable();
            $table->unsignedBigInteger('price_group_id')->nullable();
            $table->decimal('price_inc_tax', 22, 4);
            $table->string('price_type', 191)->default('fixed');
            $table->timestamps(); // Creates `created_at` and `updated_at` columns

            // Optionally, add foreign key constraints if needed
            $table->foreign('variation_id')->references('id')->on('variations')->onDelete('cascade');
            $table->foreign('price_group_id')->references('id')->on('selling_price_groups')->onDelete('cascade');
           
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('variation_group_prices');
    }
};
