<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Master stok obat
        Schema::create('medicines', function (Blueprint $table) {
            $table->id();
            $table->string('code', 30)->unique();
            $table->string('name');
            $table->string('category', 50)->nullable();
            $table->string('unit', 30)->default('Tablet'); // Tablet, Kapsul, Botol, Strip, Tube, Ampul
            $table->unsignedInteger('price')->default(0); // Harga jual satuan
            $table->integer('stock')->default(0);
            $table->unsignedInteger('min_stock')->default(10);
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        // Rincian obat pada rekam medis (resep terstruktur)
        Schema::create('medical_record_medicines', function (Blueprint $table) {
            $table->id();
            $table->foreignId('medical_record_id')->constrained()->cascadeOnDelete();
            $table->foreignId('medicine_id')->constrained()->restrictOnDelete();
            $table->unsignedInteger('qty')->default(1);
            $table->unsignedInteger('price')->default(0); // Harga satuan saat transaksi
            $table->string('dosage', 100)->nullable(); // Aturan pakai, misal 3x1 sesudah makan
            $table->timestamps();
        });

        // Tambah kolom biaya obat & nomor kuitansi di medical_records
        Schema::table('medical_records', function (Blueprint $table) {
            $table->string('invoice_no', 30)->nullable()->unique()->after('id');
            $table->unsignedInteger('medicine_fee')->default(0)->after('fee');
            $table->enum('payment_method', ['cash', 'qris', 'transfer'])->default('cash')->after('medicine_fee');
        });
    }

    public function down(): void
    {
        Schema::table('medical_records', function (Blueprint $table) {
            $table->dropColumn(['invoice_no', 'medicine_fee', 'payment_method']);
        });
        Schema::dropIfExists('medical_record_medicines');
        Schema::dropIfExists('medicines');
    }
};

