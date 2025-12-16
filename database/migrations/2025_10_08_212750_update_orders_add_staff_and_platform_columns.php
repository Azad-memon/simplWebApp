<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateOrdersAddStaffAndPlatformColumns extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('orders', function (Blueprint $table) {

            $table->unsignedBigInteger('user_id')->nullable()->change();
            $table->unsignedBigInteger('staff_id')->nullable()->after('user_id');
            $table->string('platform')->nullable()->after('staff_id');

            // Optional: foreign key (if staff table or users table exists)
            // $table->foreign('staff_id')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('orders', function (Blueprint $table) {
            // Revert changes
            $table->unsignedBigInteger('user_id')->nullable(false)->change();
            $table->dropColumn(['staff_id', 'platform']);
        });
    }
}
