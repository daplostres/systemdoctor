<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Pengaturan praktek (key-value)
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->text('value')->nullable();
            $table->timestamps();
        });

        // Layanan praktek
        Schema::create('services', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('icon')->default('bi-heart-pulse');
            $table->unsignedInteger('price')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Jadwal praktek mingguan
        Schema::create('schedules', function (Blueprint $table) {
            $table->id();
            $table->unsignedTinyInteger('day'); // 0 = Minggu ... 6 = Sabtu
            $table->time('start_time');
            $table->time('end_time');
            $table->unsignedSmallInteger('quota')->default(20);
            $table->boolean('is_active')->default(true);
            $table->string('note')->nullable();
            $table->timestamps();
        });

        // Data pasien
        Schema::create('patients', function (Blueprint $table) {
            $table->id();
            $table->string('medical_record_no')->unique(); // No. Rekam Medis
            $table->string('name');
            $table->string('nik', 20)->nullable();
            $table->enum('gender', ['L', 'P']);
            $table->date('birth_date')->nullable();
            $table->string('phone', 20)->index();
            $table->text('address')->nullable();
            $table->string('blood_type', 3)->nullable();
            $table->text('allergies')->nullable();
            $table->timestamps();
        });

        // Janji temu / antrian
        Schema::create('appointments', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->foreignId('patient_id')->constrained()->cascadeOnDelete();
            $table->foreignId('service_id')->nullable()->constrained()->nullOnDelete();
            $table->date('appointment_date');
            $table->unsignedSmallInteger('queue_number');
            $table->text('complaint')->nullable();
            $table->enum('status', ['pending', 'confirmed', 'completed', 'cancelled'])->default('pending');
            $table->text('admin_note')->nullable();
            $table->timestamps();

            $table->index(['appointment_date', 'status']);
        });

        // Rekam medis
        Schema::create('medical_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('patient_id')->constrained()->cascadeOnDelete();
            $table->foreignId('appointment_id')->nullable()->constrained()->nullOnDelete();
            $table->date('visit_date');
            $table->string('blood_pressure', 15)->nullable();
            $table->decimal('weight', 5, 1)->nullable();
            $table->decimal('temperature', 4, 1)->nullable();
            $table->text('complaint');
            $table->text('diagnosis');
            $table->text('treatment')->nullable();
            $table->text('prescription')->nullable();
            $table->text('notes')->nullable();
            $table->unsignedInteger('fee')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('medical_records');
        Schema::dropIfExists('appointments');
        Schema::dropIfExists('patients');
        Schema::dropIfExists('schedules');
        Schema::dropIfExists('services');
        Schema::dropIfExists('settings');
    }
};

