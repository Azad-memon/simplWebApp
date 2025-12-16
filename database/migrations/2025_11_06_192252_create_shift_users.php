<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
       Schema::create('shift_users', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id');
            $table->integer('branch_id')->nullable();
            $table->integer('shift_id')->nullable();
            $table->decimal('last_amount', 12, 2)->default(0);
            $table->enum('status', ['open', 'closed'])->default('open');
            $table->date('shift_date');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('shift_users');
    }
};
