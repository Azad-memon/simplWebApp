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
    Schema::create('order_items', function (Blueprint $table) {
        $table->id();
        $table->unsignedBigInteger('order_id');
        $table->unsignedBigInteger('product_variant_id')->nullable();
        $table->integer('quantity')->default(1);
        $table->decimal('price', 10, 2)->default(0.00);
        $table->decimal('total_price', 10, 2)->default(0.00);
        $table->text('notes')->nullable();
        $table->json('addon_id')->nullable();
        $table->json('ing_id')->nullable();
        $table->json('removed_ingredient_ids')->nullable();
        $table->timestamps();

        // // Foreign Key
        // $table->foreign('order_id')
        //       ->references('id')->on('orders')
        //       ->onDelete('cascade');
    });
}


    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('order_items');
    }
};
