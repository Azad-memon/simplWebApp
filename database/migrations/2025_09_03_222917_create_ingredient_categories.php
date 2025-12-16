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
        Schema::create('ingredient_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');                     // Category name
            $table->text('description')->nullable();    // Optional description
            $table->unsignedBigInteger('parent_id')->nullable(); // For subcategories
            $table->boolean('is_active')->default(true); // Active/Inactive
            $table->timestamps();

            // Self-relation for parent category
            // $table->foreign('parent_id')
            //       ->references('id')
            //       ->on('ingredient_categories')
            //       ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ingredient_categories');
    }
};
