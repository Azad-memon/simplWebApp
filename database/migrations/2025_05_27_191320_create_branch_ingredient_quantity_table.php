<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('branch_ingredient_quantity', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('ing_id');
            $table->unsignedBigInteger('updated_by');
            $table->decimal('qty', 10, 2);
            $table->timestamps();

            $table->foreign('ing_id')->references('ing_id')->on('ingredients')->onDelete('cascade');
            $table->foreign('updated_by')->references('id')->on('users')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('branch_ingredient_quantity');
    }
};
