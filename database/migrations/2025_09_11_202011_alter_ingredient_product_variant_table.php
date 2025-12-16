<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('ingredient_product_variant', function (Blueprint $table) {
           $table->unsignedBigInteger('ing_category_id')->after('id')->nullable();
            $table->unsignedBigInteger('ingredient_id')->nullable()->change();
            $table->unsignedBigInteger('default_ing')->after('ingredient_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ingredient_product_variant', function (Blueprint $table) {
            $table->dropColumn('ing_category_id');
            $table->unsignedBigInteger('ingredient_id')->nullable(false)->change();
            $table->dropColumn('default_ing');
        });
    }
};
