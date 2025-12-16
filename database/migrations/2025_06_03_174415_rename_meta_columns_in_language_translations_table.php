<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RenameMetaColumnsInLanguageTranslationsTable extends Migration
{
    public function up()
    {
        Schema::table('language_translations', function (Blueprint $table) {
            $table->renameColumn('meta_title', 'meta_key');
            $table->renameColumn('meta_desc', 'meta_value');
        });
    }

    public function down()
    {
        Schema::table('language_translations', function (Blueprint $table) {
            $table->renameColumn('meta_key', 'meta_title');
            $table->renameColumn('meta_value', 'meta_desc');
        });
    }
}
