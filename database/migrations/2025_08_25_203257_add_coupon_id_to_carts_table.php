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
  public function up(): void
{
    Schema::table('carts', function (Blueprint $table) {
        $table->unsignedBigInteger('coupon_id')->nullable()->after('user_id');
    });
}

public function down(): void
{
    Schema::table('carts', function (Blueprint $table) {
        $table->dropColumn('coupon_id');
    });
}

};
