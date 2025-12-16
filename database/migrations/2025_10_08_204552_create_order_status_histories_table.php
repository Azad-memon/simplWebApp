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
        Schema::create('order_tracking', function (Blueprint $table) {
           $table->bigIncrements('id');
          // $table->string('order_id')->nullable()->index();
           $table->unsignedBigInteger('order_id')->index();
           $table->string('status')->index();
           $table->unsignedBigInteger('changed_by')->nullable()->index();
           $table->text('note')->nullable();
           $table->timestamps();

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('order_tracking');
    }
};
