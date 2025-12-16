<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Modify the ENUM field to include 'ready'
        DB::statement("
            ALTER TABLE orders
            MODIFY COLUMN status
            ENUM(
                'pending',
                'accepted',
                'processing',
                'preparing',
                'dispatched',
                'ready',
                'completed',
                'cancelled'
            ) NOT NULL DEFAULT 'pending'
        ");
    }

    public function down(): void
    {
        // Revert without 'ready'
        DB::statement("
            ALTER TABLE orders
            MODIFY COLUMN status
            ENUM(
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
