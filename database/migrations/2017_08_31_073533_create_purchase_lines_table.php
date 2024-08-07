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
        Schema::create('purchase_lines', function (Blueprint $table) {
            $table->id(); // Auto-incrementing primary key `id`
            $table->unsignedBigInteger('transaction_id')->nullable();
            $table->unsignedBigInteger('product_id')->nullable();
            $table->unsignedBigInteger('variation_id')->nullable();
            $table->decimal('quantity', 22, 4)->default(0.0000);
            $table->decimal('secondary_unit_quantity', 22, 4)->default(0.0000);
            $table->decimal('pp_without_discount', 22, 4)->default(0.0000)->comment('Purchase price before inline discounts');
            $table->decimal('discount_percent', 5, 2)->default(0.00)->comment('Inline discount percentage');
            $table->decimal('purchase_price', 22, 4);
            $table->decimal('purchase_price_inc_tax', 22, 4)->default(0.0000);
            $table->decimal('item_tax', 22, 4)->comment('Tax for one quantity');
            $table->unsignedBigInteger('tax_id')->nullable();
            $table->unsignedInteger('purchase_requisition_line_id')->nullable();
            $table->decimal('quantity_sold', 22, 4)->default(0.0000)->comment('Quantity sold from this purchase line');
            $table->decimal('quantity_adjusted', 22, 4)->default(0.0000)->comment('Quantity adjusted in stock adjustment from this purchase line');
            $table->decimal('po_quantity_purchased', 22, 4)->default(0.0000);
            $table->date('mfg_date')->nullable();
            $table->date('exp_date')->nullable();
            $table->string('lot_number', 191)->nullable();
            $table->timestamps(); // Creates `created_at` and `updated_at` columns
            
            // Optionally, add foreign key constraints if needed
            $table->foreign('transaction_id')->references('id')->on('transactions')->onDelete('cascade');
            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
            $table->foreign('variation_id')->references('id')->on('variations')->onDelete('cascade');
            $table->foreign('tax_id')->references('id')->on('tax_rates')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('purchase_lines');
    }
};
