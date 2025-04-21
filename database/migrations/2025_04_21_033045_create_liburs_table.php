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
        Schema::create('liburs', function (Blueprint $table) {
            $table->id();
            $table->date('tanggal')->unique(); // satu tanggal hanya satu libur
            $table->string('keterangan')->nullable(); // misal: 'Hari Raya Idul Fitri'
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('liburs');
    }
};
