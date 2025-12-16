<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('ingredients', function (Blueprint $table) {
            $table->string('ingredient_label')->nullable()->after('ing_desc');
            $table->boolean('is_quantify')->default(1)->after('ingredient_label');
        });
    }

    public function down(): void
    {
        Schema::table('ingredients', function (Blueprint $table) {
            $table->dropColumn(['ingredient_label', 'is_quantify']);
        });
    }
};
