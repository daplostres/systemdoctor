
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
- **Cetak Lembar Resep Dokter `R/` (`/admin/records/{id}/prescription`):** Lembar resep resmi ber-kop dr. Eko Andrianto & SIP untuk ditebus pasien di apotek luar.
- **Cetak Etiket / Label Plastik Obat (`/admin/records/{id}/labels`):** Label kotak-kotak otomatis berisi Nama Praktek dr. Eko Andrianto, Nama Pasien, Nama Obat, Jumlah, Tanggal Expired, dan Aturan Pakai untuk ditempel di bungkus obat.
- **Surat Keterangan Sakit, Sehat & Rujukan RS/Lab (`/admin/letters`):** Buat dan cetak **Surat Keterangan Sakit** (`SKS/...`), **Surat Keterangan Sehat** (`SKH/...`), serta **Surat Rujukan Medis ke RS/Spesialis/Lab** (`SRJ/...`) ber-kop Praktek dr. Eko Andrianto.
- **Grafik Perkembangan Tanda Vital Pasien (`/admin/patients/{id}`):** Grafik garis interaktif riwayat **Tekanan Darah (Sistolik & Diastolik)** dan **Berat Badan** dari kunjungan ke kunjungan.
- **Stok Obat, Tanggal Kedaluwarsa & Riwayat Mutasi (`/admin/medicines` & `/admin/medicines/logs`):** Master obat, harga satuan, batas stok minimum, **peringatan obat mendekati/sudah kedaluwarsa (*Expired Date*)**, tambah/kurangi stok cepat, serta **Riwayat Mutasi Stok Masuk & Keluar**.
- **Laporan Keuangan & 10 Penyakit Terbanyak (`/admin/reports`):** Ringkasan pemasukan jasa vs obat, grafik pemasukan harian, **rekap 10 diagnosis penyakit terbanyak**, 5 obat terbanyak diresepkan, cetak laporan, dan **Ekspor CSV/Excel**.
- **Backup Database `.sql` 1-Klik (`/admin/settings`):** Unduh cadangan seluruh database MySQL praktek dalam format `.sql` langsung dari menu Pengaturan Admin.
- **Pengumuman Tanggal Libur (`/admin/holidays`):** Atur tanggal tutup sementara yang otomatis tampil di halaman depan.
- **Kelola Akun Petugas (`/admin/users`):** Tambah/hapus akun untuk ke-5 peran di atas.

---

## Cara Menjalankan (Lokal / 1 Komputer)
```bash
php artisan migrate:fresh --seed
php artisan serve
```
- **Website Utama:** `http://localhost:8000`
- **Layar TV Antrean Ruang Tunggu:** `http://localhost:8000/antrean`
- **Login Panel Admin & Petugas:** `http://localhost:8000/login`

---

## Cara Menjalankan & Akses via Jaringan Lokal (LAN / Wi-Fi Tanpa Internet)

Aplikasi ini dapat diakses bersama-sama oleh **banyak perangkat sekaligus** (Komputer Pendaftaran, Laptop/Tablet Dokter, Komputer Apotek, Rekam Medis, hingga Smart TV Ruang Tunggu) menggunakan **1 Router Wi-Fi atau Switch LAN yang sama meskipun tanpa koneksi internet (Offline)**.

### 1. Jalankan Server Laravel dengan Host `0.0.0.0`
Pada komputer utama yang dijadikan server (yang terinstal Laragon/XAMPP & database MySQL), buka terminal/CMD di folder project lalu jalankan:

```bash
php artisan serve --host=0.0.0.0 --port=8000
```
> **Keterangan:** Parameter `--host=0.0.0.0` memerintahkan server Laravel untuk menerima koneksi dari semua perangkat lain yang berada di jaringan lokal (LAN/Wi-Fi), bukan hanya dari `localhost` komputer itu sendiri.

---

### 2. Cek Alamat IP (IPv4) Komputer Server
Buka **Command Prompt (CMD)** atau **PowerShell** di komputer server, lalu ketik:
```bash
ipconfig
```
Cari bagian **`Wireless LAN adapter Wi-Fi`** (jika pakai Wi-Fi) atau **`Ethernet adapter`** (jika pakai kabel LAN), lalu catat angka pada baris **`IPv4 Address`**, misalnya:
- Contoh IP Server: **`192.168.1.10`**

---

### 3. Cara Mengakses dari Perangkat Lain (HP / Laptop / PC / Smart TV)
Pastikan perangkat lain sudah tersambung ke **nama Wi-Fi / kabel LAN yang sama** dengan komputer server, lalu buka browser (Chrome/Edge/Safari) dan ketik alamat IP komputer server diikuti `:8000`:

| Kebutuhan Perangkat | URL yang Dibuka di Browser Perangkat Lain |
| :--- | :--- |
| **Halaman Utama Praktek** | `http://192.168.1.10:8000` |
| **Login Dokter / Pendaftaran / Apotek / RM** | `http://192.168.1.10:8000/login` |
| **Layar TV Antrean Ruang Tunggu** | `http://192.168.1.10:8000/antrean` |

*(Ganti `192.168.1.10` sesuai dengan IPv4 Address komputer server Anda).*

---

### 4. Catatan Penting & Persiapan Agar Lancar Diakses di Lokal LAN

1. **Buka Izin Port `8000` di Windows Firewall (Wajib jika tidak bisa dibuka dari perangkat lain):**
   Sering kali *Windows Defender Firewall* memblokir akses masuk dari perangkat lain ke port `8000`. Untuk membukanya, buka **PowerShell sebagai Administrator (*Run as Administrator*)** di komputer server lalu jalankan perintah ini satu kali:
   ```powershell
   New-NetFirewallRule -DisplayName "Server Praktek dr Eko LAN 8000" -Direction Inbound -Protocol TCP -LocalPort 8000 -Action Allow
   ```
   *(Atau buka **Windows Defender Firewall with Advanced Security** → **Inbound Rules** → **New Rule** → **Port** → **TCP `8000`** → **Allow the connection**).*

2. **Gunakan IP Statis (*Static IP*) pada Komputer Server:**
   Agar alamat IP komputer server tidak berubah-ubah setiap kali router Wi-Fi atau komputer dimatikan/dinyalakan ulang, atur **Static IP** pada pengaturan Wi-Fi/Ethernet Windows komputer server (atau atur fitur *DHCP Address Reservation* di pengaturan router Wi-Fi).

3. **Pastikan Fitur *AP Isolation / Client Isolation* di Router Wi-Fi Nonaktif:**
   Jika menggunakan router Wi-Fi, pastikan fitur *AP Isolation* / *Guest Network Isolation* dalam keadaan **Off/Disabled** agar antar perangkat di Wi-Fi yang sama diizinkan saling terhubung.

4. **Pengaturan File `.env` (Sesi Login & Database):**
   - Biarkan `DB_HOST=127.0.0.1` karena database MySQL berjalan di komputer server itu sendiri.
   - Pastikan `SESSION_DOMAIN=null` (default) agar login bisa berfungsi ketika diakses menggunakan alamat IP (`http://192.168.1.x:8000`).

5. **Catatan untuk Kondisi 100% Tanpa Internet (Offline Murni):**
   - **Suara Panggilan Antrean (`/antrean`):** Menggunakan fitur *Web Speech API (`window.speechSynthesis`)* bawaan browser & sistem operasi (Windows/Android/Smart TV). Pastikan paket bahasa **Bahasa Indonesia (Indonesian - `id-ID`)** sudah terpasang di pengaturan *Text-to-Speech* Windows/perangkat agar suara panggilan tetap berbunyi meskipun tanpa internet.
   - **Aset Tampilan (Bootstrap CSS/JS, Bootstrap Icons & Chart.js):** Secara bawaan halaman memanggil file CSS/ikon/grafik melalui CDN. Jika router Wi-Fi benar-benar **tidak memiliki kabel internet sama sekali**, disarankan menyimpan file `bootstrap.min.css`, `bootstrap.bundle.min.js`, `bootstrap-icons`, dan `chart.umd.min.js` ke dalam folder `public/vendor/` agar tampilan tabel, warna, ikon, dan grafik tetap muncul 100% tanpa internet.
   - **Peta Google Maps:** Hanya bagian kotak peta *embed* Google Maps di halaman depan publik (`/`) yang membutuhkan internet; seluruh fitur inti klinik (pendaftaran, antrean, panggilan suara, rekam medis, apotek, cetak surat/struk/resep/etiket, dan laporan) berjalan penuh di jaringan lokal.

