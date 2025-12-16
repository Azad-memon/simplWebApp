<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('order_queues', function (Blueprint $table) {
            // New column to track if the queue is closed
            $table->boolean('shift_closed')->default(false)->after('status')->comment('Marks if this queue/shift is closed');
        });
    }

    public function down()
    {
        Schema::table('order_queues', function (Blueprint $table) {
            $table->dropColumn('shift_closed');
        });
    }
};
