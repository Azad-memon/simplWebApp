<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('coupons', function (Blueprint $table) {
            // Change datatype
            $table->json('product_id')->nullable()->change();
            $table->json('product_variant_id')->nullable()->change();
        });
    }

    public function down()
    {
        Schema::table('coupons', function (Blueprint $table) {
            // Rollback to bigint
            $table->bigInteger('product_id')->unsigned()->nullable()->change();
            $table->bigInteger('product_variant_id')->unsigned()->nullable()->change();
        });
    }
};

