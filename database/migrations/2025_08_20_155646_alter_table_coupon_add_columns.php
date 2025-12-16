<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('coupons', function (Blueprint $table) {
            $table->unsignedBigInteger('product_id')->nullable()->after('id');
            $table->unsignedBigInteger('product_variant_id')->nullable()->after('product_id');
            $table->decimal('min_amount', 10, 2)->nullable()->after('max_usage');
            $table->decimal('max_amount', 10, 2)->nullable()->after('min_amount');
        });
    }

    public function down(): void
    {
        Schema::table('coupons', function (Blueprint $table) {
            $table->dropForeign(['product_id']);
            $table->dropForeign(['product_variant_id']);

            $table->dropColumn(['product_id', 'product_variant_id', 'min_amount', 'max_amount']);
        });
    }
};
