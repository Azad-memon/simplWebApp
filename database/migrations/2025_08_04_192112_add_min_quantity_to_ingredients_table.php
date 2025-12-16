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
    Schema::table('ingredients', function (Blueprint $table) {
        $table->decimal('min_quantity', 10, 2)->nullable()->after('ing_unit'); // Adjust position as needed
    });
}

public function down()
{
    Schema::table('ingredients', function (Blueprint $table) {
        $table->dropColumn('min_quantity');
    });
}
};
