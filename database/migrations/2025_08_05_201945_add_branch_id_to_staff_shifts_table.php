<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddBranchIdToStaffShiftsTable extends Migration
{
    public function up()
    {
        Schema::table('staff_shifts', function (Blueprint $table) {
            $table->unsignedBigInteger('branch_id')->nullable()->after('id');

            // If you have a `branches` table and want foreign key constraint:
            $table->foreign('branch_id')->references('id')->on('branches')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::table('staff_shifts', function (Blueprint $table) {
            $table->dropForeign(['branch_id']);
            $table->dropColumn('branch_id');
        });
    }
}
