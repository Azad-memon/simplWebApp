<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('addon_ingredient_items', function (Blueprint $table) {
            $table->id();
            $table->integer('addon_ingredient_id');
            $table->integer('ingredient_id');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('addon_ingredient_items');
    }
};
