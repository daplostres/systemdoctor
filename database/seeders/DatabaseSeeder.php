<?php

namespace Database\Seeders;

use App\Models\Appointment;
use App\Models\MedicalLetter;
use App\Models\MedicalRecord;
use App\Models\Medicine;
use App\Models\Patient;
use App\Models\Schedule;
use App\Models\Service;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Akun Super Admin (All Access)
        User::updateOrCreate(['email' => 'admin@praktek.test'], [
            'name' => 'Administrator Praktek',
            'role' => 'admin',
            'password' => 'password',
        ]);

        // 2. Akun Dokter (Fokus Pemeriksaan Klinis, Pasien, Rekam Medis, Surat, Laporan)
        User::updateOrCreate(['email' => 'dokter@praktek.test'], [
            'name' => 'dr. Eko Andrianto',
            'role' => 'doctor',
            'password' => 'password',
        ]);

        // 3. Akun Petugas Pendaftaran & Antrean On-Site
        User::updateOrCreate(['email' => 'pendaftaran@praktek.test'], [
            'name' => 'Sari (Bag. Pendaftaran)',
            'role' => 'assistant',
            'password' => 'password',
        ]);

        // 4. Akun Petugas Rekam Medis & Surat Keterangan
        User::updateOrCreate(['email' => 'rekammedis@praktek.test'], [
            'name' => 'Dina (Bag. Rekam Medis)',
            'role' => 'medical_record',
            'password' => 'password',
        ]);

        // 5. Akun Petugas Obat / Apotek
        User::updateOrCreate(['email' => 'apotek@praktek.test'], [
            'name' => 'Rudi (Bag. Obat / Apotek)',
            'role' => 'pharmacist',
            'password' => 'password',
        ]);

        // Pengaturan praktek dr. Eko Andrianto — Ngaliyan, Semarang
        $settings = [
            'clinic_name' => 'Praktek dr. Eko Andrianto',
            'doctor_name' => 'dr. Eko Andrianto',
            'doctor_title' => 'Dokter Umum',
            'doctor_sip' => 'SIP. 449.1/082/DINKES-SMG/2024',
            'doctor_bio' => 'Melayani pemeriksaan kesehatan umum bagi anak, dewasa, hingga lansia di wilayah Tambakaji, Ngaliyan, dan sekitarnya di Kota Semarang. Mengutamakan pelayanan yang ramah, teliti, komunikatif, serta biaya yang terjangkau untuk seluruh keluarga.',
            'tagline' => 'Pelayanan kesehatan keluarga yang ramah, dekat, dan terpercaya di Ngaliyan, Kota Semarang. Pendaftaran langsung di tempat (on-site).',
            'address' => 'Jl. Beringin Raya, Tambakaji, Kec. Ngaliyan, Kota Semarang, Jawa Tengah 50244',
            'phone' => '024-7612345',
            'whatsapp' => '6281225001234',
            'email' => 'dr.ekoandrianto@praktek.id',
            'maps_embed' => 'https://www.google.com/maps?q=Jl.+Beringin+Raya,+Tambakaji,+Kec.+Ngaliyan,+Kota+Semarang,+Jawa+Tengah+50244&output=embed',
        ];
        foreach ($settings as $k => $v) {
            Setting::set($k, $v);
        }

        // Layanan
        $services = [
            ['Konsultasi & Pemeriksaan Umum', 'Pemeriksaan dan konsultasi keluhan kesehatan umum untuk dewasa & lansia.', 'bi-chat-heart', 75000],
            ['Pemeriksaan Kesehatan Anak', 'Pemeriksaan kesehatan bayi dan anak, demam, batuk pilek, serta tumbuh kembang.', 'bi-emoji-smile', 80000],
            ['Cek Tensi, Gula Darah & Kolesterol', 'Pemeriksaan tekanan darah, gula darah sewaktu/puasa, kolesterol, dan asam urat.', 'bi-droplet-half', 50000],
            ['Tindakan & Perawatan Luka', 'Perawatan luka ringan, ganti perban, jahit luka, serta nebulizer (uap).', 'bi-bandaid', 100000],
            ['Surat Keterangan Sehat / Sakit', 'Pemeriksaan fisik untuk keperluan surat keterangan sehat kerja/sekolah.', 'bi-file-earmark-medical', 50000],
            ['Kunjungan Rumah (Home Visit)', 'Pemeriksaan pasien di rumah untuk area Tambakaji & Ngaliyan.', 'bi-house-heart', 200000],
        ];
        foreach ($services as [$name, $desc, $icon, $price]) {
            Service::updateOrCreate(['name' => $name], ['description' => $desc, 'icon' => $icon, 'price' => $price, 'is_active' => true]);
        }

        // Jadwal praktek (Senin - Sabtu)
        if (Schedule::count() === 0) {
            foreach ([1, 2, 3, 4, 5] as $day) {
                Schedule::create(['day' => $day, 'start_time' => '16:30', 'end_time' => '21:00', 'quota' => 25, 'note' => 'Ambil antrean langsung di tempat']);
            }
            Schedule::create(['day' => 6, 'start_time' => '08:00', 'end_time' => '12:00', 'quota' => 25, 'note' => 'Praktek pagi (On-Site)']);
        }

        // Master stok obat contoh (lengkap dengan tanggal kedaluwarsa)
        if (Medicine::count() === 0) {
            $medicines = [
                ['Paracetamol 500mg', 'Analgesik & Antipiretik', 'Strip', 8000, 80, 15, now()->addMonths(14)],
                ['Amoxicillin 500mg', 'Antibiotik', 'Strip', 15000, 45, 10, now()->addMonths(10)],
                ['Cetirizine 10mg', 'Antihistamin', 'Strip', 10000, 50, 10, now()->addMonths(12)],
                ['Antasida Doen', 'Lambung', 'Strip', 7000, 60, 15, now()->addMonths(8)],
                ['Omeprazole 20mg', 'Lambung', 'Strip', 14000, 8, 10, now()->addDays(25)], // Stok menipis + hampir expired
                ['Amlodipine 5mg', 'Antihipertensi', 'Strip', 12000, 40, 10, now()->addMonths(18)],
                ['OBH Sirup 100ml', 'Obat Batuk', 'Botol', 22000, 6, 8, now()->addDays(40)], // Stok menipis + hampir expired
                ['Vitamin C 500mg', 'Vitamin & Suplemen', 'Strip', 10000, 75, 15, now()->addMonths(16)],
                ['Salep Gentamicin 0.1%', 'Topikal / Kulit', 'Tube', 12000, 25, 5, now()->addMonths(9)],
                ['Oralit Sachet', 'Pencernaan', 'Sachet', 2500, 90, 20, now()->addMonths(20)],
            ];
            foreach ($medicines as [$name, $cat, $unit, $price, $stock, $min, $exp]) {
                $med = Medicine::create([
                    'name' => $name,
                    'category' => $cat,
                    'unit' => $unit,
                    'price' => $price,
                    'stock' => $stock,
                    'min_stock' => $min,
                    'expired_date' => $exp->toDateString(),
                ]);
                \App\Models\MedicineStockLog::create([
                    'medicine_id' => $med->id,
                    'user_id' => 1,
                    'type' => 'in',
                    'qty' => $stock,
                    'stock_after' => $stock,
                    'description' => 'Stok awal apotek',
                ]);
            }
        }

        // Data contoh pasien Semarang, antrean on-site, rekam medis & surat keterangan
        if (Patient::count() === 0) {
            $patients = [
                ['Budi Santoso', 'L', '1985-03-12', '081225110001', 'O', 'Jl. Beringin Raya No. 24, Tambakaji, Ngaliyan'],
                ['Siti Aminah', 'P', '1990-07-25', '081225110002', 'A', 'Perum Permata Ngaliyan Blok B2, Semarang'],
                ['Rina Wulandari', 'P', '2015-11-02', '081225110003', 'B', 'Jl. Raya Ngaliyan No. 18, Semarang'],
                ['Ahmad Fauzi', 'L', '1972-01-30', '081225110004', 'AB', 'Kel. Gondoriyo, Kec. Ngaliyan, Semarang'],
                ['Dewi Lestari', 'P', '1998-09-14', '081225110005', 'O', 'Jl. Wates Timur, Tambakaji, Ngaliyan'],
            ];
            $diagnoses = ['ISPA', 'Hipertensi', 'Gastritis', 'Observasi Febris', 'Dermatitis Alergi', 'Cephalgia'];
            $methods = ['cash', 'qris', 'transfer'];
            $allMeds = Medicine::all();

            foreach ($patients as $i => [$name, $g, $dob, $phone, $blood, $addr]) {
                $p = Patient::create([
                    'name' => $name, 'gender' => $g, 'birth_date' => $dob, 'phone' => $phone,
                    'blood_type' => $blood, 'address' => $addr,
                ]);

                // Riwayat kunjungan beberapa hari lalu
                foreach (range(1, 2) as $n) {
                    $date = now()->subDays(rand(1, 5));
                    $a = Appointment::create([
                        'patient_id' => $p->id, 'service_id' => 1, 'appointment_date' => $date->toDateString(),
                        'complaint' => 'Demam, batuk, dan badan pegal', 'status' => 'completed',
                    ]);

                    $picked = $allMeds->random(2);
                    $medFee = 0;
                    $prescLines = [];
                    foreach ($picked as $m) {
                        $medFee += $m->price;
                        $prescLines[] = $m->name.' (1 '.$m->unit.') — 3x1 sesudah makan';
                    }

                    $rec = MedicalRecord::create([
                        'patient_id' => $p->id,
                        'appointment_id' => $a->id,
                        'visit_date' => $numDaysAgo = $date->toDateString(),
                        'blood_pressure' => rand(110, 135).'/'.rand(70, 88),
                        'weight' => rand(45, 78),
                        'temperature' => 36 + rand(3, 18) / 10,
                        'complaint' => 'Demam, batuk, dan badan pegal',
                        'diagnosis' => $diagnoses[array_rand($diagnoses)],
                        'treatment' => 'Pemeriksaan fisik, edukasi & terapi obat',
                        'prescription' => implode("\n", $prescLines),
                        'pharmacy_note' => 'Edukasi pasien: minum sesudah makan, jika demam sudah turun Paracetamol boleh dihentikan.',
                        'pharmacy_status' => 'dispensed',
                        'fee' => 75000,
                        'medicine_fee' => $medFee,
                        'payment_method' => $methods[array_rand($methods)],
                    ]);

                    foreach ($picked as $m) {
                        $rec->medicineItems()->create([
                            'medicine_id' => $m->id,
                            'qty' => 1,
                            'price' => $m->price,
                            'dosage' => '3x1 sesudah makan',
                        ]);
                    }
                }

                // Antrean hari ini di tempat (1 sedang dipanggil, sisanya menunggu)
                $statusToday = $i === 0 ? 'confirmed' : 'pending';
                Appointment::create([
                    'patient_id' => $p->id,
                    'service_id' => ($i % 3) + 1,
                    'appointment_date' => now()->toDateString(),
                    'complaint' => 'Pemeriksaan di tempat',
                    'status' => $statusToday,
                    'called_at' => $statusToday === 'confirmed' ? now() : null,
                ]);
            }

            // Contoh Surat Keterangan Sakit & Surat Sehat
            $firstPatient = Patient::first();
            MedicalLetter::create([
                'type' => 'sick',
                'patient_id' => $firstPatient->id,
                'issue_date' => now()->toDateString(),
                'occupation' => 'Karyawan Swasta',
                'rest_days' => 2,
                'start_date' => now()->toDateString(),
                'end_date' => now()->addDay()->toDateString(),
                'notes' => 'Perlu istirahat karena ISPA dan demam.',
            ]);

            MedicalLetter::create([
                'type' => 'healthy',
                'patient_id' => $firstPatient->id,
                'issue_date' => now()->toDateString(),
                'occupation' => 'Karyawan Swasta',
                'height' => 170,
                'weight' => 66,
                'blood_pressure' => '120/80',
                'temperature' => 36.5,
                'color_blind' => 'Tidak Buta Warna',
                'purpose' => 'Melamar Pekerjaan',
            ]);

            MedicalLetter::create([
                'type' => 'referral',
                'patient_id' => $firstPatient->id,
                'issue_date' => now()->toDateString(),
                'occupation' => 'Karyawan Swasta',
                'referral_to' => 'RSUD Adhyatma, MPH (Tugurejo) Semarang',
                'referral_specialist' => 'Poli Spesialis Penyakit Dalam',
                'anamnesis' => 'Demam tinggi 4 hari disertai nyeri kepala dan bintik kemerahan (TD: 115/75 mmHg, Suhu: 38.6 °C).',
                'provisional_diagnosis' => 'Observasi Febris H-4 susp. Dengue Fever',
                'given_therapy' => 'Paracetamol 3x500mg, Vitamin C, edukasi rehidrasi oral.',
                'notes' => 'Mohon pemeriksaan darah rutin / trombosit dan tatalaksana lebih lanjut.',
            ]);
        }
    }
}
