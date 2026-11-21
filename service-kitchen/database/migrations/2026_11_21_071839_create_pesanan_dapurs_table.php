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
        Schema::create('pesanan_dapur', function (Blueprint $table) {
            $table->id();
            $table->uuid('message_id')->unique();
            $table->string('kode')->index();
            $table->unsignedBigInteger('menu_id');
            $table->string('menu');
            $table->unsignedInteger('qty');
            $table->string('status')->default('masuk');
            $table->timestamp('diterima_pada');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pesanan_dapurs');
    }
};
