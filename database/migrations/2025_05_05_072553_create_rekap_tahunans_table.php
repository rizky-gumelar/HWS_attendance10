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
        Schema::create('rekap_tahunan', function (Blueprint $table) {
            $table->integer('user_id');
            $table->integer('tahun');
            $table->integer('cuti')->default(0);
            $table->integer('cf')->default(0);
            $table->integer('sakit')->default(0);
            $table->float('setengah_hari', 8, 2)->default(0);
            $table->integer('saldo_cuti')->default(0);
            $table->integer('poin_ketidakhadiran')->default(0);

            $table->float('cuti_terpakai', 8, 2)->storedAs('cuti + setengah_hari');
            $table->float('poin_terpakai', 8, 2)->storedAs('cuti + cf + setengah_hari + sakit');
            $table->float('cuti_akhir', 8, 2)->storedAs('saldo_cuti - cuti_terpakai');
            $table->float('poin_akhir', 8, 2)->storedAs('poin_ketidakhadiran - poin_terpakai');
            $table->primary(['user_id', 'tahun']); // Primary key gabungan
            $table->timestamps(); // Menambahkan created_at dan updated_at jika diperlukan
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rekap_tahunan');
    }
};
