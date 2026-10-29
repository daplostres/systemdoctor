<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Tambah kolom tanggal kedaluwarsa (expired_date) pada tabel medicines
        Schema::table('medicines', function (Blueprint $table) {
            $table->date('expired_date')->nullable()->after('min_stock');
        });

        // Tabel riwayat mutasi stok obat (masuk / keluar)
        Schema::create('medicine_stock_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('medicine_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->enum('type', ['in', 'out']);
            $table->unsignedInteger('qty');
            $table->integer('stock_after');
            $table->string('description')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('medicine_stock_logs');
        Schema::table('medicines', function (Blueprint $table) {
            $table->dropColumn('expired_date');
        });
    }
};

