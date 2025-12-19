<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
{
    Schema::create('dataset_files', function (Blueprint $table) {
        $table->id();
        $table->unsignedBigInteger('dataset_id');
        $table->string('file_path');
        $table->string('file_type')->nullable();
        $table->integer('file_size')->nullable();
        $table->timestamps();

        $table->foreign('dataset_id')->references('id')->on('datasets')->onDelete('cascade');
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dataset_files');
    }
};
