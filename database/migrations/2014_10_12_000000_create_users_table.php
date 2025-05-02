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
        Schema::create('users', function (Blueprint $table) {
            $table->unsignedBigInteger('id')->primary();
            $table->foreignId('toko_id')->constrained('toko')->onDelete('cascade');
            $table->foreignId('default_shift_id')->constrained('shift')->onDelete('cascade');
            $table->string('nama_karyawan');
            $table->foreignId('divisi_id')->constrained('divisi')->onDelete('cascade');
            $table->string('no_hp')->unique()->nullable();
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->date('tanggal_masuk');
            $table->string('password');
            $table->enum('role', ['admin', 'spv', 'karyawan']);
            $table->float('total_cuti', 8, 2)->default(0);
            $table->float('poin_tidak_hadir', 8, 2)->default(24);
            $table->rememberToken();
            $table->timestamps();
            $table->enum('status', ['aktif', 'nonaktif'])->default('aktif');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
