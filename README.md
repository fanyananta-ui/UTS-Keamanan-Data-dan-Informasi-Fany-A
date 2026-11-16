Nama: Fany Ananta Rahmah
NIM: 2430511045
Kelas: 5C
Mata Kuliah: Keamanan Data Dan Informasi

---

# 🔐 Enkripsi App

Aplikasi sistem informasi berbasis **PHP** dan **MySQL** yang menyimpan data dalam bentuk terenkripsi. Login halaman admin tidak memakai password biasa, tetapi memakai **username**, **kunci simetris (AES)**, dan **kunci asimetris (private key RSA)**.

## Fitur

- **Landing page** untuk menyimpan pesan. Pesan dienkripsi dengan AES-256-CBC sebelum masuk database.
- **Login admin berbasis kunci**: username + kunci simetris + private key RSA. Login berhasil hanya jika ketiganya cocok.
- **Username disimpan sebagai hash** (`password_hash` / bcrypt), sehingga tidak bisa dibaca dari database.
- **Dashboard admin** berisi statistik, data terenkripsi, dan hasil dekripsinya.
- **Edit** (dienkripsi ulang) dan **hapus** data dari dashboard.
- Halaman **setup** untuk membuat username dan pasangan kunci RSA (terkunci setelah dipakai).

## Teknologi

| Komponen | Keterangan |
|---|---|
| Bahasa | PHP 8 |
| Database | MySQL / MariaDB |
| Enkripsi simetris | AES-256-CBC (OpenSSL) |
| Enkripsi asimetris | RSA 2048-bit (OpenSSL) |
| Hash username | bcrypt (`password_hash`) |
| Server lokal | Laragon / XAMPP |

## Struktur File

```
enkripsi-app/
├── config.php     # koneksi database, fungsi enkripsi/dekripsi, token CSRF
├── db.sql         # struktur database
├── setup.php      # buat username + kunci RSA (sekali pakai)
├── index.php      # landing page, simpan pesan terenkripsi
├── login.php      # login: username + kunci simetris + private key
├── admin.php      # dashboard admin
├── edit.php       # edit data (enkripsi ulang)
├── logout.php     # keluar
├── style.css      # tampilan
└── README.md
```

## Cara Menjalankan

1. Salin folder `enkripsi-app` ke `www` (Laragon) atau `htdocs` (XAMPP), lalu nyalakan Apache dan MySQL.
2. Buka phpMyAdmin, import file `db.sql` (membuat database `db_enkripsi`).
3. Buka `http://localhost/enkripsi-app/setup.php`, isi username admin, lalu **simpan username dan private key** yang tampil (private key hanya tampil sekali).
4. Buka `http://localhost/enkripsi-app/` dan simpan beberapa pesan.
5. Klik **Masuk Halaman Admin**, isi username, kunci simetris, dan private key.
6. Dashboard menampilkan data terenkripsi beserta hasil dekripsinya.

Jika `setup.php` error OpenSSL di Windows, aktifkan `extension=openssl` di `php.ini` lalu restart Apache.

## Alur Keamanan

1. **Simpan data**: teks → AES-256-CBC (IV acak) → Base64 → tabel `pesan`.
2. **Setup**: dibuat pasangan kunci RSA. Kunci simetris dienkripsi dengan public key RSA dan disimpan di tabel `kunci`. Private key hanya diberikan ke admin.
3. **Login**: sistem memeriksa username (hash), kunci simetris, lalu mendekripsi kunci simetris tersimpan memakai private key yang diinput dan membandingkannya.
4. **Dashboard**: data didekripsi dengan kunci simetris untuk ditampilkan.

## Catatan

- Private key **tidak** disertakan di repository ini. Berikan secara terpisah.
- Kunci simetris ditulis langsung di `config.php` agar sederhana untuk keperluan tugas. Pada sistem nyata, kunci disimpan di luar kode (environment variable atau key vault).
- Setelah setup, `setup.php` terkunci. Untuk membuat ulang username dan kunci, buka `setup.php?reset=1`.
