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
        Schema::table('shift_cash_notes', function (Blueprint $table) {
            $table->decimal('total', 12, 2)->after('quantity')->default(0);
        });
    }

    public function down()
    {
        Schema::table('shift_cash_notes', function (Blueprint $table) {
            $table->dropColumn('total');
        });
    }
};
