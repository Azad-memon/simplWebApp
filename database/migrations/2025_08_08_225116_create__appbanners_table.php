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
    Schema::create('app_banners', function (Blueprint $table) {
        $table->id();
        $table->string('banner_title');
        $table->longText('banner_description');
        $table->enum('type', ['category', 'product']);
        $table->Integer('category_id')->nullable();
        $table->Integer('product_id')->nullable();
        $table->timestamps();

        // Foreign keys for category and product (optional, if you're using categories and products tables)
      //  $table->foreign('category_id')->references('id')->on('categories')->onDelete('set null');
     //   $table->foreign('product_id')->references('id')->on('products')->onDelete('set null');
    });
}


    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('_appbanners');
    }
};
