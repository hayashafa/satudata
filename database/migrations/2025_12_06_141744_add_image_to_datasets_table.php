<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    
    /**
     * Reverse the migrations.
     */
    public function up()
{
    Schema::table('datasets', function (Blueprint $table) {
        $table->string('image')->nullable()->after('file_path');
    });
}

public function down()
{
    Schema::table('datasets', function (Blueprint $table) {
        $table->dropColumn('image');
    });

    }
};
