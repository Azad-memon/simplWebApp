<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('shift_ingredients', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('shift_id');
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('branch_id');
            $table->unsignedBigInteger('ingredient_id');
            $table->integer('quantity');
            $table->enum('entry_type', ['opening', 'closing'])->default('opening');
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('shift_ingredients');
    }
};
