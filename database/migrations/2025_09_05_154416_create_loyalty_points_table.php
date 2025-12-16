<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('loyalty_points', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('customer_id');   // customer
            $table->unsignedBigInteger('order_id')->nullable();
            $table->decimal('points_updated', 10, 2)->default(0);
            $table->decimal('points_balance', 10, 2)->default(0);
            $table->enum('transaction_type', ['CREDIT', 'DEBIT']);
            $table->timestamp('transaction_date')->useCurrent();
            $table->timestamps();

            // Foreign Keys
            // $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            // $table->foreign('order_id')->references('id')->on('orders')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('loyalty_points');
    }
};
