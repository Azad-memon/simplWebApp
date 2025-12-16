<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement('ALTER TABLE branches ENGINE = InnoDB');

        Schema::create('stations', function (Blueprint $table) {
            $table->engine = 'InnoDB'; 
            $table->id();
            $table->string('s_name', 191);
            $table->unsignedBigInteger('branch_id');
            $table->string('ip', 255);
            $table->timestamps();
            $table->foreign('branch_id')
                  ->references('id')
                  ->on('branches')
                  ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stations');
    }
};
