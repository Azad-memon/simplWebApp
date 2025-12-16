<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('popups', function (Blueprint $table) {
            $table->id();
            $table->string('title')->nullable(); // optional title
            $table->string('image'); // popup image path
            $table->boolean('is_active')->default(true); // active/inactive
            $table->timestamp('start_at')->nullable(); // optional: show from
            $table->timestamp('end_at')->nullable();   // optional: show till
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('popups');
    }
};
