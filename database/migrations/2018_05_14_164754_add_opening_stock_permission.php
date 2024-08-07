<?php

use Illuminate\Database\Migrations\Migration;
use Spatie\Permission\Models\Permission;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $insert_data = [
            'name' => 'product.opening_stock',
            'guard_name' => 'web',
        ];

        if (!Permission::where('name', $insert_data['name'])->exists()) {
            Permission::create($insert_data);
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Optionally, you could delete the permission if it exists
        Permission::where('name', 'product.opening_stock')->delete();
    }
};
