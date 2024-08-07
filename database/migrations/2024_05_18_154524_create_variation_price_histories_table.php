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
        Schema::create('variation_price_histories', function (Blueprint $table) {
            $table->id(); // Auto-incrementing primary key `id`
            $table->unsignedBigInteger('variation_id')->nullable();
            $table->string('old_price', 191);
            $table->string('new_price', 191);
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->string('type', 191)->nullable();
            $table->string('h_type', 191)->nullable();
            $table->string('ref_no', 191)->nullable();
            $table->timestamps(); // Creates `created_at` and `updated_at` columns with default values

            // Define foreign key constraints
            $table->foreign('variation_id')->references('id')->on('variations')->onDelete('cascade')->name('fk_variation_id_in_variation_price_table');
            $table->foreign('updated_by')->references('id')->on('users')->onDelete('cascade')->name('fk_updated_by_user_in_variation_table');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('variation_price_histories');
    }
};
