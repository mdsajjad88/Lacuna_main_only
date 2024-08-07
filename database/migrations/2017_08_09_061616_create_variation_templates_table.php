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
        Schema::create('variation_templates', function (Blueprint $table) {
            $table->id(); // Auto-incrementing primary key `id`
            $table->string('name', 191);
            $table->unsignedBigInteger('business_id')->nullable();
            $table->unsignedInteger('woocommerce_attr_id')->nullable();
            $table->timestamps(); // Creates `created_at` and `updated_at` columns
            $table->foreign('business_id')->references('id')->on('business')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('variation_templates');
    }
};
