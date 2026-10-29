
# Website Praktek dr. Eko Andrianto (Laravel 13 + MySQL)

**Lokasi:** Jl. Beringin Raya, Tambakaji, Kec. Ngaliyan, Kota Semarang, Jawa Tengah 50244  
**Sistem Antrean:** Melayani pendaftaran & pengambilan antrean langsung di tempat (*On-Site*).

---

## Daftar Akun Default (Login di `/login`)

Semua akun di bawah ini menggunakan password: **`password`**

| No | Peran / Bagian | Email Login | Password | Menu & Hak Akses |
| :-: | :--- | :--- | :--- | :--- |
| 1 | **Super Admin (All Access)** | `admin@praktek.test` | `password` | **Akses Penuh Semua Menu:** Dashboard, Antrean, Pasien, Rekam Medis, Surat Sakit/Sehat, Stok Obat, Laporan Keuangan, Layanan & Tarif, Jadwal Praktek, Tanggal Libur, Kelola Akun Petugas, dan Pengaturan Praktek. |
| 2 | **Dokter Pemeriksa** | `dokter@praktek.test` | `password` | **Khusus Pemeriksaan Klinis:** Dashboard Pemeriksaan, Antrean Pasien (Panggil & Periksa), Data Pasien, Rekam Medis (+ isi *Keterangan Obat untuk Apotek*), Surat Sakit/Sehat, dan Laporan Keuangan. |
| 3 | **Petugas Obat / Apotek** | `apotek@praktek.test` | `password` | **Khusus Apotek & Kasir Obat:** Dashboard Obat, Resep & Keterangan Dokter (lihat instruksi obat dari dokter, klik *Serahkan Obat*, dan *Cetak Kuitansi*), serta Stok Obat. |
| 4 | **Petugas Pendaftaran** | `pendaftaran@praktek.test` | `password` | **Khusus Meja Depan (Front Office):** Dashboard Antrean, Antrean Pasien (ambil antrean *on-site* pasien lama/baru, cetak kartu antrean, panggil suara), Data Pasien, dan Layar Antrean TV. |
| 5 | **Petugas Rekam Medis** | `rekammedis@praktek.test` | `password` | **Khusus Administrasi Medis:** Dashboard RM, Data Pasien, Rekam Medis, dan Surat Keterangan Sakit / Sehat. |

---

## Fitur Utama

### 1. Halaman Publik & Ruang Tunggu
- **Landing Page (`/`):** Profil **dr. Eko Andrianto**, layanan & tarif, jadwal praktek mingguan, banner pengumuman tutup sementara/libur, lokasi Google Maps (Tambakaji, Ngaliyan, Semarang), serta **Pantau Nomor Antrean Hari Ini Secara Real-Time**.
- **Layar TV Panggil Antrean (`/antrean`):** Tampilan khusus layar monitor/TV ruang tunggu yang memperbarui nomor antrean secara *live* dan membunyikan **panggilan suara Bahasa Indonesia (Text-to-Speech)** otomatis hanya menyebutkan nomor antrean demi privasi pasien:
  > *"Nomor antrean 1, silakan masuk ke ruang periksa."*

### 2. Modul Operasional Praktek
- **Pendaftaran Antrean On-Site (`/admin/appointments`):** Daftar cepat untuk pasien lama maupun **input sekaligus pasien baru di tempat** + **Cetak Kartu Nomor Antrean** (`/admin/appointments/{id}/ticket`).
- **Rekam Medis, Resep & Keterangan Apotek (`/admin/records`):** Pencatatan tanda vital (tensi, BB, suhu), keluhan, diagnosis, tindakan, pilih obat langsung dari stok (validasi stok otomatis), serta kolom **Keterangan / Instruksi Obat untuk Pengelola Obat (Apotek)**.
- **Cetak Struk / Kuitansi (`/admin/records/{id}/receipt`):** Kuitansi resmi siap cetak (`INV-YYMMDD-XXXX`).
- **Cetak Etiket / Label Plastik Obat (`/admin/records/{id}/labels`):** Label kotak-kotak otomatis berisi Nama Praktek dr. Eko Andrianto, Nama Pasien, Nama Obat, Jumlah, Tanggal Expired, dan Aturan Pakai untuk ditempel di bungkus obat.
- **Surat Keterangan Sakit, Sehat & Rujukan RS/Lab (`/admin/letters`):** Buat dan cetak **Surat Keterangan Sakit** (`SKS/...`), **Surat Keterangan Sehat** (`SKH/...`), serta **Surat Rujukan Medis ke RS/Spesialis/Lab** (`SRJ/...`) ber-kop Praktek dr. Eko Andrianto.
- **Grafik Perkembangan Tanda Vital Pasien (`/admin/patients/{id}`):** Grafik garis interaktif riwayat **Tekanan Darah (Sistolik & Diastolik)** dan **Berat Badan** dari kunjungan ke kunjungan.
- **Stok Obat, Tanggal Kedaluwarsa & Riwayat Mutasi (`/admin/medicines` & `/admin/medicines/logs`):** Master obat, harga satuan, batas stok minimum, **peringatan obat mendekati/sudah kedaluwarsa (*Expired Date*)**, tambah/kurangi stok cepat, serta **Riwayat Mutasi Stok Masuk & Keluar**.
- **Laporan Keuangan Bulanan (`/admin/reports`):** Ringkasan pemasukan jasa vs obat, grafik pemasukan harian, obat terbanyak diresepkan, cetak laporan, dan **Ekspor CSV/Excel**.
- **Pengumuman Tanggal Libur (`/admin/holidays`):** Atur tanggal tutup sementara yang otomatis tampil di halaman depan.
- **Kelola Akun Petugas (`/admin/users`):** Tambah/hapus akun untuk ke-5 peran di atas.

---

## Cara Menjalankan
```bash
php artisan migrate:fresh --seed
php artisan serve
```
- **Website Utama:** http://localhost:8000
- **Layar TV Antrean Ruang Tunggu:** http://localhost:8000/antrean
- **Login Panel Admin & Petugas:** http://localhost:8000/login
=======
# systemdoctor
gatau dah, disuruh bikin yaudah aku8 bikin
>>>>>>> 213e4a353d3cb46c6b516d52b49f2fa541f19f58
