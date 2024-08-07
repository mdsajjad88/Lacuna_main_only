<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRandomChecksTable extends Migration
{
    public function up()
    {
        Schema::create('random_checks', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('checked_by')->nullable();
            $table->unsignedBigInteger('modified_by')->nullable();
            $table->string('check_no')->unique();
            $table->text('comment')->nullable();
            $table->timestamps();
            $table->foreign('checked_by')->references('id')->on('users')->onDelete('set null')->name('fk_users_in_random_table');
            $table->foreign('modified_by')->references('id')->on('users')->onDelete('set null')->name('fk_users_id_in_random_table');
        });
    }

    public function down()
    {
        Schema::dropIfExists('random_checks');
    }
}