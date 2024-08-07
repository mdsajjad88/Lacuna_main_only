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
        Schema::create('variations', function (Blueprint $table) {

            $table->id(); // Auto-incrementing primary key field `id`
            $table->string('name', 191);
            $table->unsignedBigInteger('product_id')->nullable();
            $table->string('sub_sku', 191)->nullable();
            $table->unsignedBigInteger('product_variation_id')->nullable();
            $table->integer('woocommerce_variation_id')->nullable();
            $table->integer('variation_value_id')->nullable();
            $table->decimal('default_purchase_price', 22, 4)->nullable();
            $table->decimal('dpp_inc_tax', 22, 4)->default(0.0000);
            $table->decimal('profit_percent', 22, 4)->default(0.0000);
            $table->decimal('default_sell_price', 22, 4)->nullable();
            $table->decimal('sell_price_inc_tax', 22, 4)->nullable()->comment('Sell price including tax');
            $table->timestamps(); // Creates `created_at` and `updated_at` columns
            $table->timestamp('deleted_at')->nullable();
            $table->text('combo_variations')->nullable()->comment('Contains the combo variation details');
            $table->decimal('foreign_p_price', 22, 4)->nullable();
            $table->string('currency_code', 4)->nullable();
            $table->decimal('currency_rate', 22, 4)->nullable();
            $table->boolean('is_foreign')->default(0);
            $table->decimal('foreign_s_price', 22, 4)->nullable();
            $table->decimal('foreign_p_price_inc_tex', 22, 4)->nullable();
            $table->decimal('foreign_s_price_inc_tex', 22, 4)->nullable();

            // Optional: Add indexes or foreign key constraints if needed
            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');           
            $table->foreign('product_variation_id')->references('id')->on('product_variations')->onDelete('cascade');           
            $table->foreign('variation_value_id')->references('id')->on('variation_value_templates')->onDelete('cascade');           
            $table->softDeletes();

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('variations');
    }
};
