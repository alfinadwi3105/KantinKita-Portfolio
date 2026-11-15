<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
{
    Schema::create('antrian', function (Blueprint $table) {
        $table->id();
        $table->string('kode')->unique();
        $table->unsignedBigInteger('menu_id');
        $table->string('menu');
        $table->unsignedInteger('qty');
        $table->unsignedInteger('total');
        $table->string('status')->default('diproses');
        $table->timestamp('dipesan_pada');
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('antrian');
    }
};
