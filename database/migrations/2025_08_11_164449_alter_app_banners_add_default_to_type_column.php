<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterAppBannersAddDefaultToTypeColumn extends Migration
{

     public function up()
    {
        // Use raw SQL to modify the enum and set default
        DB::statement("ALTER TABLE app_banners MODIFY COLUMN type ENUM('default', 'category', 'product') DEFAULT 'default'");
    }

    public function down()
    {
        // Revert the enum column to original values (no default)
        DB::statement("ALTER TABLE app_banners MODIFY COLUMN type ENUM('category', 'product')");
    }

};
