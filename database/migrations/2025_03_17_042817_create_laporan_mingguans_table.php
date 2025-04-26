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
            // $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->unsignedBigInteger('user_id');
            $table->foreign('user_id')->references('pub_id')->on('users')->onDelete('cascade');
            $table->integer('minggu_ke');
            $table->string('d1')->nullable();
            $table->string('d2')->nullable();
            $table->string('d3')->nullable();
            $table->string('d4')->nullable();
            $table->string('d5')->nullable();
            $table->string('d6')->nullable();
            $table->string('d7')->nullable();
            $table->integer('uang_mingguan');
            $table->integer('uang_kedatangan');
            $table->integer('uang_lembur_mingguan');
            $table->enum('status', ['selesai', 'kurang']);
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
