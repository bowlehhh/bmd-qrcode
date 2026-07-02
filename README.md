# BMD QR Asset

Sistem ini digunakan untuk mengelola data aset BMD, membuat QR untuk setiap aset, menampilkan hasil scan QR di HP, serta mengekspor QR aset ke dokumen Word beserta informasi barangnya.

Dokumentasi ini disusun sesuai alur web yang ada di project saat ini.

## 1. Ringkasan Fungsi

Fitur utama aplikasi:

- Login admin untuk masuk ke dashboard.
- Input data aset BMD.
- Generate QR otomatis saat aset dibuat.
- Download file QR per aset.
- Export QR ke Word untuk 1 aset atau beberapa aset sekaligus.
- Halaman publik hasil scan QR yang menampilkan data aset terbaru.
- Riwayat export Word yang terpisah dan jelas.
- Log aktivitas login, logout, tambah, ubah, hapus, dan export.

## 2. Alur Kerja Sistem

Alur penggunaan sistem:

1. Admin login ke sistem.
2. Admin menambahkan data aset dari form input.
3. Sistem otomatis membuat QR yang mengarah ke halaman publik aset.
4. QR bisa diunduh atau diekspor ke Word.
5. Saat QR discan, pengguna langsung melihat hasil scan aset dari data terbaru.
6. Jika data aset diperbarui, QR lama tetap bisa dipakai dan hasil scan akan mengikuti data terbaru.
7. Setiap export Word tercatat di halaman `Riwayat Export`.

## 3. Struktur Halaman Utama

Halaman yang tersedia di web:

- `Login`
  Untuk autentikasi admin.

- `Dashboard`
  Menampilkan ringkasan jumlah aset, kondisi aset, lokasi aset, aset terbaru, dan aktivitas umum.

- `Data Aset`
  Menampilkan daftar aset, tombol detail, export Word, ubah, hapus, dan export massal.

- `Detail Aset`
  Menampilkan informasi lengkap aset, QR aset, tombol kembali, tombol download QR, dan tombol export Word.

- `Tambah Aset` dan `Ubah Aset`
  Form input dan edit data aset.

- `Riwayat Export`
  Halaman khusus yang hanya menampilkan log export Word QR aset.

- `Halaman Publik Hasil Scan`
  Halaman yang tampil setelah QR discan dari HP atau perangkat lain.

## 4. Data Aset yang Digunakan

Field utama yang dipakai dalam sistem:

- `Nama Barang`
- `Kode Barang`
- `Nomor Register`
- `Merk / Type`
- `Tahun Perolehan`
- `Kondisi`
- `Penanggung Jawab`
- `Lokasi Barang`
- `Keterangan`

Catatan penting:

- Field `Kategori` tetap ada di database untuk kebutuhan internal, tetapi pada form sekarang nilainya disamakan otomatis dari `Nama Barang` bila tidak diisi manual.
- Field `Masih Digunakan` diisi otomatis ke nilai `Ya` secara sistem.
- QR tidak dibuat ulang saat aset diupdate. Yang berubah hanya isi data yang ditampilkan saat scan.

## 5. Aturan Input Data

Validasi utama input aset:

- `Kode Barang` wajib diisi dan harus unik.
- `Nama Barang` wajib diisi.
- `Lokasi Barang` wajib diisi.
- `Kondisi` wajib diisi dan nilainya hanya:
  - `baik`
  - `rusak`
  - `perlu perbaikan`
- `Tahun Perolehan` jika diisi harus 4 digit dan berada di antara `1900` sampai `2100`.
- `Foto` opsional dan maksimal `2 MB`.

## 6. Cara Kerja QR

Saat aset baru disimpan:

- Sistem membuat URL publik aset dengan format rute publik.
- Sistem membuat file QR SVG dan menyimpannya ke storage publik.
- Sistem menyimpan:
  - `qr_code_path`
  - `qr_target_url`

Saat QR discan:

- Pengguna diarahkan ke halaman publik aset.
- Halaman scan menampilkan data aset terbaru dari database.
- Jika admin mengubah nama, lokasi, kondisi, atau keterangan, QR lama tetap valid.

## 7. Export QR ke Word

Sistem sekarang tidak lagi fokus ke cetak label layout banyak ukuran seperti flow lama.

Flow yang dipakai sekarang:

- Export Word per aset dari halaman detail atau daftar aset.
- Export Word massal dari halaman data aset.
- Dokumen Word berisi:
  - Judul QR aset
  - Nama aset
  - Kode aset dan lokasi
  - Gambar QR
  - Tabel informasi barang

Isi tabel Word:

- Nama Barang
- Kode Barang
- Nomor Register
- Merk / Type
- Tahun Perolehan
- Kondisi
- Penanggung Jawab
- Lokasi Barang
- Keterangan

Nama file export:

- Single asset: `{kode-aset}-qr-asset.docx`
- Bulk asset: `qr-aset-bmd.docx`

## 8. Riwayat Export

Riwayat export dibuat di halaman sendiri agar lebih jelas dan tidak bercampur dengan log umum.

Halaman `Riwayat Export` menampilkan:

- Total export
- Total export single
- Total export massal
- Total aset yang pernah diexport
- Waktu export
- User yang melakukan export
- IP address
- Nama file Word
- Jumlah aset
- Ringkasan aset
- Tabel daftar aset dan `Penanggung Jawab` untuk export massal

Action log khusus export:

- `export_word_asset`
- `export_word_assets_bulk`

## 9. Hak Akses

Hak akses saat ini:

- `Admin`
  Bisa login, tambah aset, ubah aset, hapus aset, download QR, export Word, dan melihat riwayat export.

Catatan:

- Validasi request aset hanya mengizinkan user dengan `role = admin`.
- Method `isAdmin()` ada di model `User`.

## 10. Route Penting

Route utama aplikasi:

### Autentikasi

- `GET /login`
- `POST /login`
- `POST /logout`

### Dashboard

- `GET /dashboard`

### Aset publik

- `GET /aset/{asset_code}`
- `GET /aset/{asset_code}/lookup`

### Aset admin

- `GET /assets`
- `GET /assets/create`
- `POST /assets`
- `GET /assets/{asset_code}`
- `GET /assets/{asset_code}/edit`
- `PUT/PATCH /assets/{asset_code}`
- `DELETE /assets/{asset_code}`

### QR dan export

- `GET /assets/selection`
- `GET /assets/{asset_code}/download`
- `GET /assets/{asset_code}/export-word`
- `POST /assets/export/word`
- `GET /exports/history`

## 11. Struktur Teknologi

Teknologi utama yang dipakai:

- PHP `^8.3`
- Laravel `^13.8`
- Vite
- Tailwind CSS
- `simplesoftwareio/simple-qrcode`
- `chillerlan/php-qrcode`
- `phpoffice/phpword`

Kegunaan package:

- `simple-qrcode`
  Untuk membuat QR SVG utama yang disimpan ke storage publik.

- `chillerlan/php-qrcode`
  Untuk membuat QR PNG sementara yang dipakai saat export Word.

- `phpoffice/phpword`
  Untuk membuat file `.docx`.

## 12. Instalasi Project

Langkah instalasi lokal:

```bash
composer install
copy .env.example .env
php artisan key:generate
php artisan migrate
npm install
npm run build
php artisan storage:link
php artisan serve
```

Jika memakai script bawaan composer:

```bash
composer run setup
php artisan storage:link
php artisan serve
```

## 13. Menjalankan Mode Development

Untuk development lengkap:

```bash
composer run dev
```

Mode ini menjalankan:

- Laravel server
- Queue listener
- Laravel Pail
- Vite dev server

## 14. Penyimpanan File

File yang disimpan sistem:

- QR publik:
  `storage/app/public/assets/qrcodes`

- Foto aset:
  `storage/app/public/assets/photos`

- File Word sementara:
  `storage/app/private/exports`

Catatan:

- File Word dibuat sementara lalu dihapus setelah dikirim ke browser.
- File QR tetap disimpan dan bisa didownload ulang.

## 15. Penjelasan Halaman Scan

Saat QR discan, sistem menampilkan halaman hasil scan yang sudah disederhanakan untuk HP.

Tujuan desain halaman scan:

- Mudah dibaca di layar kecil
- Fokus pada informasi inti barang
- Ada tombol kembali
- Tampilan lebih rapi dengan nuansa biru Kominfo
- Tidak perlu halaman detail kedua setelah scan

Artinya:

- Setelah scan, pengguna cukup melihat 1 halaman hasil scan.
- Tombol `Tutup` / halaman detail tambahan sudah tidak dipakai lagi.

## 16. Aktivitas yang Dicatat

Contoh aktivitas yang masuk log:

- `login`
- `logout`
- `create_asset`
- `update_asset`
- `delete_asset`
- `export_word_asset`
- `export_word_assets_bulk`

Informasi log dapat berisi:

- user
- deskripsi
- subject aset
- IP address
- properti tambahan seperti filename dan jumlah aset

## 17. Pengujian Dasar yang Disarankan

Setelah update fitur, cek minimal:

1. Login admin berhasil.
2. Tambah aset baru berhasil.
3. QR otomatis terbentuk.
4. Download QR berhasil.
5. Scan QR menampilkan data aset yang benar.
6. Update aset tidak merusak QR lama.
7. Export Word single berhasil.
8. Export Word massal berhasil.
9. Riwayat export tampil di halaman khusus.
10. Hapus aset ikut menghapus file QR dan foto jika ada.

## 18. Troubleshooting

Jika QR tidak muncul:

- Pastikan `php artisan storage:link` sudah dijalankan.
- Pastikan folder `storage` bisa ditulis.

Jika export Word gagal:

- Pastikan dependency composer sudah terpasang lengkap.
- Pastikan folder `storage/app/private/exports` bisa dibuat dan ditulis.
- Pastikan ekstensi GD aktif karena QR PNG export Word memakai backend gambar PHP.

Jika hasil scan tidak berubah setelah edit aset:

- Pastikan data benar-benar tersimpan saat update.
- QR memang tidak dibuat ulang, jadi yang berubah adalah isi data dari halaman publiknya.

## 19. Catatan Pengembangan Lanjutan

Pengembangan berikutnya yang cocok untuk sistem ini:

- Filter riwayat export berdasarkan tanggal
- Filter riwayat export berdasarkan user
- Export riwayat ke Excel atau PDF
- Preview Word sebelum download
- Role tambahan selain admin
- Pencarian log export yang lebih detail

## 20. Kesimpulan

Web `BMD QR Asset` saat ini berfungsi sebagai sistem manajemen aset berbasis QR dengan alur yang sederhana:

- admin input data,
- sistem membuat QR,
- QR discan untuk melihat data aset terbaru,
- QR bisa diexport ke Word,
- dan semua riwayat export tercatat jelas di halaman khusus.

Dokumentasi ini sebaiknya diperbarui setiap kali ada perubahan alur, field input, atau format export Word.
