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
    Schema::create('branch_settings', function (Blueprint $table) {
        $table->id();
        $table->integer('branch_id');
        $table->string('printer_ip')->nullable();
        $table->string('printer_port')->nullable()->default('9100');
        $table->json('settings')->nullable(); // JSON array for future custom settings
        $table->timestamps();
    });
}

public function down()
{
    Schema::dropIfExists('branch_settings');
}
};
