<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('medical_letters', function (Blueprint $table) {
            // Ubah kolom type menjadi string agar mendukung 'sick', 'healthy', dan 'referral'
            $table->string('type', 20)->default('sick')->change();
            // Kolom khusus Surat Rujukan
            $table->string('referral_to', 150)->nullable()->after('purpose'); // RS / Lab / Klinik Tujuan
            $table->string('referral_specialist', 120)->nullable()->after('referral_to'); // Poli / Dokter Spesialis / Pemeriksaan Lab
            $table->text('anamnesis')->nullable()->after('referral_specialist'); // Keluhan & hasil pemeriksaan
            $table->string('provisional_diagnosis', 255)->nullable()->after('anamnesis'); // Diagnosis sementara
            $table->text('given_therapy')->nullable()->after('provisional_diagnosis'); // Terapi/tindakan yang sudah diberikan
        });
    }

    public function down(): void
    {
        Schema::table('medical_letters', function (Blueprint $table) {
            $table->dropColumn([
                'referral_to', 'referral_specialist', 'anamnesis', 'provisional_diagnosis', 'given_therapy',
            ]);
        });
    }
};

