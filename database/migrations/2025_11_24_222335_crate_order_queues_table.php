<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('order_queues', function (Blueprint $table) {
            $table->id();
            $table->integer('order_id');
            $table->integer('branch_id');
            $table->integer('shift_id');
            $table->integer('queue_number');
            $table->date('queue_date');
            $table->enum('status', ['open', 'closed'])->default('open');
            $table->timestamps();

            $table->index(['branch_id', 'shift_id', 'queue_date']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('order_queues');
    }
};
