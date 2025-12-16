<?php

use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::statement("
        ALTER TABLE orders
        MODIFY COLUMN status ENUM(
            'pending',
            'accepted',
            'processing',
            'preparing',
            'dispatched',
            'ready',
            'completed',
            'cancelled',
            'refunded'
        ) NOT NULL DEFAULT 'pending'
    ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("
        ALTER TABLE orders
        MODIFY COLUMN status ENUM(
            'pending',
            'accepted',
            'processing',
            'preparing',
            'dispatched',
            'completed',
            'cancelled'
        ) NOT NULL DEFAULT 'pending'
    ");
    }
};
