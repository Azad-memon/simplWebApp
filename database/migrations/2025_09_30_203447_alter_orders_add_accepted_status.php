<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // MySQL ENUM ko directly alter karna hota hai
        DB::statement("ALTER TABLE orders MODIFY COLUMN status
            ENUM('pending','accepted','cancelled','completed')
            NOT NULL DEFAULT 'pending'");
    }

    public function down(): void
    {
        // rollback karte waqt accepted ko hata do
        DB::statement("ALTER TABLE orders MODIFY COLUMN status
            ENUM('pending','cancelled','completed')
            NOT NULL DEFAULT 'pending'");
    }
};
