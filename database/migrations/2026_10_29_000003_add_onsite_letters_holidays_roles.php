<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Role pengguna: admin (all access), doctor, assistant, medical_record, pharmacist
        Schema::table('users', function (Blueprint $table) {
            $table->string('role', 30)->default('admin')->after('email');
        });

        // Catatan khusus dokter ke pengelola obat (apotek) & status penyerahan obat
        Schema::table('medical_records', function (Blueprint $table) {
            $table->text('pharmacy_note')->nullable()->after('prescription');
            $table->enum('pharmacy_status', ['pending', 'dispensed'])->default('pending')->after('pharmacy_note');
        });

        // Tambah waktu panggil di tabel appointments untuk layar antrean live
        Schema::table('appointments', function (Blueprint $table) {
            $table->timestamp('called_at')->nullable()->after('status');
        });

        // Tanggal libur / tutup sementara praktek
        Schema::create('holidays', function (Blueprint $table) {
            $table->id();
            $table->date('start_date');
            $table->date('end_date');
            $table->string('reason');
            $table->timestamps();
        });

        // Surat Keterangan Sakit & Surat Keterangan Sehat
        Schema::create('medical_letters', function (Blueprint $table) {
            $table->id();
            $table->string('letter_no', 40)->unique();
            $table->enum('type', ['sick', 'healthy']); // sick = Surat Sakit, healthy = Surat Sehat
            $table->foreignId('patient_id')->constrained()->cascadeOnDelete();
            $table->foreignId('medical_record_id')->nullable()->constrained()->nullOnDelete();
            $table->date('issue_date');
            $table->string('occupation', 100)->nullable();
            // Khusus Surat Sakit
            $table->unsignedSmallInteger('rest_days')->nullable();
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            // Khusus Surat Sehat
            $table->unsignedSmallInteger('height')->nullable();
            $table->decimal('weight', 5, 1)->nullable();
            $table->string('blood_pressure', 15)->nullable();
            $table->decimal('temperature', 4, 1)->nullable();
            $table->string('color_blind', 30)->nullable(); // Tidak Buta Warna / Buta Warna Parsial / Total
            $table->string('purpose', 255)->nullable(); // Keperluan surat sehat
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('medical_letters');
        Schema::dropIfExists('holidays');
        Schema::table('appointments', function (Blueprint $table) {
            $table->dropColumn('called_at');
        });
        Schema::table('medical_records', function (Blueprint $table) {
            $table->dropColumn(['pharmacy_note', 'pharmacy_status']);
        });
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('role');
        });
    }
};

