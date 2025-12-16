<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {

        DB::statement('ALTER TABLE categories ENGINE = InnoDB');

        Schema::create('station_category', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->id();
            $table->unsignedBigInteger('station_id');
            $table->unsignedBigInteger('category_id');
            $table->timestamps();

            $table->foreign('station_id')
                ->references('id')
                ->on('stations')
                ->onDelete('cascade');

            $table->foreign('category_id')
                ->references('id')
                ->on('categories')
                ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('station_category');
    }
};
