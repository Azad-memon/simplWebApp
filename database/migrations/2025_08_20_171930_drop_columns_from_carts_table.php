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
        Schema::table('carts', function (Blueprint $table) {
            $table->dropColumn(['product_variant_id', 'quantity', 'notes']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('carts', function (Blueprint $table) {
            $table->integer('product_variant_id')->after('user_id');
            $table->integer('quantity')->default(1)->after('product_variant_id');
            $table->text('notes')->nullable()->after('quantity');
        });
    }
};
