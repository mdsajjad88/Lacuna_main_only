<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
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
         Schema::create('system', function (Blueprint $table) {
             $table->string('key');
             $table->text('value')->nullable();
             $table->primary('key'); // Ensure 'key' is the primary key if it's intended
         });
 
         $version = config('author.app_version');
 
         DB::table('system')->insert([
             'key' => 'db_version',
             'value' => $version
         ]);
     }
 
     public function down()
     {
         Schema::dropIfExists('system');
     }
};
