<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBranchStockManagmentTable extends Migration
{
    public function up()
    {
        Schema::create('branch_stock_managment', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('branch_id');
            $table->unsignedBigInteger('ingredient_id');
            $table->decimal('quantity', 10, 2);
            $table->enum('type', ['in', 'out']); // stock in or out
            $table->unsignedBigInteger('updated_by')->nullable(); // optional9
            $table->timestamps();

        });
    }

    public function down()
    {
        Schema::dropIfExists('branch_stock_managment');
    }
}

