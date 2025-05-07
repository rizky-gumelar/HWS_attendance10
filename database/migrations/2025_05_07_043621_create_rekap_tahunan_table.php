<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRekapTahunanTable extends Migration
{
    public function up()
    {
        Schema::create('rekap_tahunan', function (Blueprint $table) {
            // Menambahkan kolom 'id' sebagai auto-increment primary key
            $table->bigIncrements('id'); // Kolom auto-increment

            // Kolom lainnya
            $table->integer('user_id');
            $table->integer('tahun');
            $table->integer('cuti')->default(0);
            $table->integer('cf')->default(0);
            $table->integer('sakit')->default(0);
            $table->double('setengah_hari', 8, 2)->default(0.00);
            $table->integer('saldo_cuti')->default(0);
            $table->integer('poin_ketidakhadiran')->default(0);

            $table->float('cuti_terpakai', 8, 2)->storedAs('cuti + setengah_hari');
            $table->float('poin_terpakai', 8, 2)->storedAs('cuti + cf + setengah_hari + sakit');
            $table->float('cuti_akhir', 8, 2)->storedAs('saldo_cuti - cuti_terpakai');
            $table->float('poin_akhir', 8, 2)->storedAs('poin_ketidakhadiran - poin_terpakai');
            $table->timestamps();

            // Menambahkan unique constraint pada kombinasi user_id dan tahun
            $table->unique(['user_id', 'tahun']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('rekap_tahunan');
    }
}
