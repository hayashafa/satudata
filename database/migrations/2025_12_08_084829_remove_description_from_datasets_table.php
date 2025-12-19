<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('datasets', function (Blueprint $table) {
            if (Schema::hasColumn('datasets', 'description')) {
                $table->dropColumn('description');
            }
        });
    }

    public function down()
    {
        Schema::table('datasets', function (Blueprint $table) {
            // kalau ingin menambah kembali kolom (optional)
            if (!Schema::hasColumn('datasets', 'description')) {
                $table->text('description')->nullable();
            }
        });
    }
};
