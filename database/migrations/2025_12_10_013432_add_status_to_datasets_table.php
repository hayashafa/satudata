<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('datasets', function (Blueprint $table) {
            $table->string('status')->default('pending')->after('image'); // pending / approved
            $table->timestamp('approved_at')->nullable()->after('status');
        });
    }

    public function down()
    {
        Schema::table('datasets', function (Blueprint $table) {
            $table->dropColumn(['status', 'approved_at']);
        });
    }
};