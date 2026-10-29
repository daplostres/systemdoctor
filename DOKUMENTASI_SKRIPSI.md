# Dokumentasi & Rekapitulasi Skripsi — Sistem Informasi Praktek dr. Eko Andrianto

Dokumen ini disusun sebagai bahan referensi penulisan **Skripsi (Bab I – Bab V)** untuk proyek **Sistem Informasi Manajemen Praktek Dokter Mandiri & Antrean On-Site Berbasis Laravel 13 dan MySQL (Studi Kasus: Praktek dr. Eko Andrianto, Ngaliyan, Kota Semarang)**.

---

## 1. Alternatif Judul Skripsi
1. **Rancang Bangun Sistem Informasi Manajemen Praktek Dokter Mandiri dan Antrean *On-Site* Berbasis Web Menggunakan Framework Laravel dan MySQL (Studi Kasus: Praktek dr. Eko Andrianto Semarang)**
2. **Pengembangan Sistem Informasi Pelayanan Medis, Rekam Medis Elektronik, dan Manajemen Apotek Berbasis *Multi-Role* pada Praktek dr. Eko Andrianto Ngaliyan Semarang**
3. **Implementasi Sistem Informasi Antrean *Real-Time*, Rekam Medis, dan Pengelolaan Stok Obat Berbasis Web dengan Framework Laravel pada Praktek Dokter Mandiri**

---

## 2. Profil Objek Penelitian (Bab I & Bab III)
- **Nama Praktek:** Praktek dr. Eko Andrianto
- **Dokter:** dr. Eko Andrianto (`SIP. 449.1/082/DINKES-SMG/2024`)
- **Alamat:** Jl. Beringin Raya, Tambakaji, Kec. Ngaliyan, Kota Semarang, Jawa Tengah 50244
- **Sistem Pelayanan:** Pendaftaran dan pengambilan nomor antrean langsung di lokasi praktek (*On-Site*), dilengkapi pemantauan nomor antrean secara *real-time* dari website dan layar TV ruang tunggu.

---

## 3. Pembagian Hak Akses 5 Role Pengguna (*Role-Based Access Control*)

| No | Role | Email Default | Password | Fokus Menu & Hak Akses |
| :-: | :--- | :--- | :--- | :--- |
| 1 | **Super Admin (`admin`)** | `admin@praktek.test` | `password` | **All Access:** Semua modul operasional + Layanan & Tarif, Jadwal Praktek, Tanggal Libur, Kelola Akun Petugas, Pengaturan Website, dan Backup Database `.sql`. |
| 2 | **Dokter (`doctor`)** | `dokter@praktek.test` | `password` | **Pemeriksaan Klinis:** Dashboard Pemeriksaan, Antrean Pasien, Data Pasien & Grafik Tanda Vital, Rekam Medis (+ Instruksi Obat ke Apotek), Surat Sakit/Sehat/Rujukan, dan Laporan Keuangan. |
| 3 | **Petugas Obat (`pharmacist`)** | `apotek@praktek.test` | `password` | **Apotek & Kasir Obat:** Dashboard Obat, Resep & Keterangan Dokter, Konfirmasi Penyerahan Obat, Cetak Etiket Plastik Obat, Cetak Kuitansi, Stok Obat, Peringatan Expired, dan Riwayat Mutasi Stok. |
| 4 | **Petugas Pendaftaran (`assistant`)** | `pendaftaran@praktek.test` | `password` | **Front Office:** Dashboard Antrean, Pendaftaran Antrean *On-Site* (Pasien Lama/Baru), Cetak Kartu Antrean, Panggil Suara Antrean, Data Pasien, dan Layar Antrean TV. |
| 5 | **Petugas Rekam Medis (`medical_record`)** | `rekammedis@praktek.test` | `password` | **Administrasi Medis:** Dashboard RM, Data Pasien, Input/Cetak Rekam Medis, Cetak Resep Luar (`R/`), dan Surat Sakit/Sehat/Rujukan. |

---

## 4. Struktur Tabel Database MySQL (`praktek_dokter`)

1. **`users`** — Menyimpan data akun petugas beserta kolom `role` (`admin`, `doctor`, `assistant`, `medical_record`, `pharmacist`).
2. **`settings`** — Menyimpan konfigurasi profil praktek (nama praktek, SIP dokter, alamat Semarang, kontak WhatsApp, embed Google Maps).
3. **`services`** — Menyimpan daftar layanan medis beserta ikon dan tarif dasar.
4. **`schedules`** — Menyimpan jadwal praktek mingguan (`day` 0–6, `start_time`, `end_time`, `quota`).
5. **`holidays`** — Menyimpan jadwal libur/tutup sementara praktek untuk ditampilkan pada banner pengumuman halaman utama.
6. **`patients`** — Menyimpan data master pasien dengan Nomor Rekam Medis otomatis (`RM-YYMM-XXXX`), NIK, tanggal lahir, golongan darah, dan riwayat alergi obat.
7. **`appointments`** — Menyimpan antrean kunjungan *on-site* harian dengan kode unik (`ANT...`), nomor urut antrean harian (`queue_number`), status, dan waktu panggilan (`called_at`).
8. **`medical_records`** — Menyimpan hasil pemeriksaan klinis (tekanan darah, berat badan, suhu, keluhan, diagnosis, tindakan, catatan resep, instruksi dokter ke apotek `pharmacy_note`, status penyerahan obat `pharmacy_status`, biaya jasa `fee`, biaya obat `medicine_fee`, metode bayar, dan nomor kuitansi `INV-YYMMDD-XXXX`).
9. **`medicines`** — Menyimpan master obat (`OBT-XXXX`), kategori, satuan, harga satuan, stok saat ini, batas stok minimum (`min_stock`), dan tanggal kedaluwarsa (`expired_date`).
10. **`medical_record_medicines`** — Menyimpan rincian item obat yang diresepkan pada setiap rekam medis beserta jumlah (`qty`), harga saat transaksi, dan aturan pakai (`dosage`).
11. **`medicine_stock_logs`** — Menyimpan riwayat mutasi stok obat masuk (`in`) dan keluar (`out`), sisa stok akhir (`stock_after`), keterangan, dan petugas pencatat.
12. **`medical_letters`** — Menyimpan dokumen surat medis: Surat Keterangan Sakit (`SKS/...`), Surat Keterangan Sehat (`SKH/...`), dan Surat Rujukan RS/Lab (`SRJ/...`).

---

## 5. Rangkuman Fitur Lengkap yang Telah Dibangun

1. **Landing Page & Pantau Antrean *Real-Time* (`/`):** Informasi lengkap Praktek dr. Eko Andrianto Semarang, banner pengumuman tutup sementara, dan kartu pantau nomor antrean yang sedang diperiksa hari ini.
2. **Layar TV Panggil Antrean Ruang Tunggu (`/antrean`):** Layar monitor ruang tunggu dengan jam lokal (`H:i WIB`) dan panggilan suara *Text-to-Speech* Bahasa Indonesia yang hanya menyebutkan nomor antrean demi menjaga privasi pasien.
3. **Pendaftaran Antrean *On-Site* & Cetak Kartu Antrean (`/admin/appointments`):** Mendukung pendaftaran pasien lama maupun input cepat pasien baru di tempat dalam satu form.
4. **Rekam Medis Elektronik & Grafik Tanda Vital (`/admin/records` & `/admin/patients/{id}`):** Pencatatan tanda vital dan visualisasi grafik garis perkembangan Tekanan Darah (Sistolik/Diastolik) serta Berat Badan pasien.
5. **Integrasi Instruksi Dokter ke Apotek (`pharmacy_note`):** Kolom pesan khusus dari dokter ke pengelola obat beserta status penyerahan obat (*Menunggu Obat* / *Sudah Diserahkan*).
6. **4 Jenis Cetakan Transaksi & Resep:**
   - Cetak Rekam Medis Pasien (`/admin/records/{id}`)
   - Cetak Struk / Kuitansi Pembayaran (`/admin/records/{id}/receipt`)
   - Cetak Etiket / Label Plastik Obat (`/admin/records/{id}/labels`)
   - Cetak Lembar Resep Luar `R/` (`/admin/records/{id}/prescription`)
7. **3 Jenis Surat Medis Resmi (`/admin/letters`):** Surat Keterangan Sakit (`SKS`), Surat Keterangan Sehat (`SKH`), dan Surat Rujukan RS/Spesialis/Lab (`SRJ`).
8. **Manajemen Stok Obat, *Expired Date* & Log Mutasi (`/admin/medicines`):** Peringatan stok menipis, peringatan obat mendekati/sudah kedaluwarsa (≤ 60 hari), dan pencatatan otomatis log stok masuk/keluar.
9. **Laporan Keuangan, 10 Penyakit Terbanyak & Ekspor CSV (`/admin/reports`):** Rekapitulasi pendapatan jasa vs obat, grafik pemasukan harian, tabel 10 diagnosis penyakit terbanyak, 5 obat terlaris, dan unduh file `.csv` (Excel).
10. **Backup Database `.sql` 1-Klik (`/admin/settings/backup`):** Fitur pencadangan seluruh tabel dan data MySQL ke file `.sql`.

