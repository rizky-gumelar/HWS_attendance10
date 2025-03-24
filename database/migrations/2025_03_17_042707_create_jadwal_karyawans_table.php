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
        Schema::create('jadwal_karyawan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('shift_id')->constrained('shift')->onDelete('cascade');
            $table->foreignId('lembur_id')->nullable()->constrained('lembur')->onDelete('set null');
            $table->foreignId('absen_id')->nullable()->constrained('absensi_harian')->onDelete('set null');
            $table->date('tanggal');
            $table->boolean('cek_keterlambatan')->nullable()->default(false);
            $table->integer('lembur_jam')->default(0);
            $table->integer('total_lembur')->default(0);
            $table->text('keterangan')->nullable();
            $table->integer('minggu_ke');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('jadwal_karyawans');
    }
};
