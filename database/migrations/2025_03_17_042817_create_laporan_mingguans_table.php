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
        Schema::create('laporan_mingguan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->integer('minggu_ke');
            $table->string('nama_karyawan');
            $table->string('toko');
            $table->integer('total_kehadiran');
            $table->integer('total_terlambat');
            $table->integer('total_lembur');
            $table->integer('uang_mingguan');
            $table->integer('uang_kedatangan');
            $table->integer('uang_lembur_mingguan');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('laporan_mingguan');
    }
};
