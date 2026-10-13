# Catatan — Sistem Login/Register PHP Native

**Tugas Rutin 7 — Fundamental Server-Side PHP**
Mata Kuliah: Pemrograman Web
Nama: Tesa Aurelio Ginting
NIM: 4253250037
Program Studi: Ilmu Komputer, FMIPA

---

## 1. Struktur Folder

tugas-rutin-7/
├── index.php            ← gerbang masuk, redirect otomatis
├── register.php         ← form & proses registrasi
├── login.php            ← form & proses login + auto-login cookie
├── dashboard.php        ← halaman khusus yang sudah login
├── logout.php           ← proses logout
├── edit-profil.php      ← bonus: ubah nama & password
├── includes/
│   ├── functions.php    ← semua fungsi inti (baca/tulis JSON, sanitasi, dll)
│   ├── auth_check.php   ← penjaga akses halaman yang wajib login
│   └── flash_display.php← komponen tampilan pesan sukses/error
├── data/
│   └── users.json       ← "database" sederhana berbasis file JSON
├── assets/css/style.css
└── docs/CATATAN.md (berkas ini)

---

## 2. Cara Menjalankan

**PENTING:** ini PHP, jadi **tidak bisa** dibuka langsung dengan klik dua kali file (seperti HTML). Harus dijalankan lewat server PHP.

### Opsi A — PHP Built-in Server (paling simpel, tidak perlu install apa-apa selain PHP)

1. Pastikan PHP sudah terinstall di komputer (cek: buka terminal/CMD, ketik `php --version`)
2. Buka terminal, masuk ke folder `tugas-rutin-7`
3. Jalankan:
   ```bash
   php -S localhost:8000
   ```
4. Buka browser ke `http://localhost:8000`

### Opsi B — XAMPP/Laragon (kalau sudah terbiasa pakai ini)

1. Copy folder `tugas-rutin-7` ke folder `htdocs` (XAMPP) atau `www` (Laragon)
2. Jalankan Apache dari control panel XAMPP/Laragon
3. Buka browser ke `http://localhost/tugas-rutin-7`

### Penting soal folder `data/`

Folder ini harus **bisa ditulis** oleh PHP (karena di situlah `users.json` disimpan/diperbarui). Kalau muncul error permission, di Linux/Mac jalankan:
```bash
chmod -R 777 data
```

---

## 3. Pemenuhan Requirement

| No | Requirement | Status | Ada di file |
|----|-------------|--------|--------------|
| 1 | Form registrasi dengan validasi (nama, email, password) | ✅ | `register.php` |
| 2 | Password di-hash dengan `password_hash()` | ✅ | `register.php`, fungsi `password_hash($password, PASSWORD_DEFAULT)` |
| 3 | Cek duplikasi email saat register | ✅ | `register.php`, pakai `find_user_by_email()` |
| 4 | Dashboard yang dipersonalisasi (user_name) | ✅ | `dashboard.php`, menampilkan `$_SESSION['user_name']` |
| 5 | Session login menggunakan session PHP | ✅ | `login.php` set `$_SESSION`, `auth_check.php` memvalidasinya |
| 6 | Validasi input dan sanitasi | ✅ | `sanitize_text()`, `sanitize_email()` di `functions.php`, dipakai di semua form |
| 7 | Logout menghapus session (`session_destroy`) | ✅ | `logout.php` |
| 8 | Pesan error & sukses ditampilkan | ✅ | Sistem flash message (`set_flash()`/`get_flash()`), ditampilkan lewat `flash_display.php` |
| Bonus | Remember Me dengan cookies | ✅ | `login.php` (set cookie), `login.php` (cek cookie saat GET), `logout.php` (hapus cookie) |
| Bonus | Edit profil | ✅ | `edit-profil.php` |
| Bonus | CSS menarik | ✅ | `assets/css/style.css`, selaras tema hijau hutan dari TR2 |

---

## 4. Hasil Testing (sudah dijalankan otomatis sebelum diserahkan)

Semua skenario berikut sudah dites lewat PHP built-in server dan **lolos semua**:

1. ✅ Registrasi user baru → data tersimpan di `users.json`, password ter-hash (bukan plain text)
2. ✅ Registrasi dengan email yang sama → ditolak dengan pesan error
3. ✅ Login dengan password salah → ditolak, pesan error generik (tidak membocorkan email valid/tidak)
4. ✅ Login dengan data benar → sukses, redirect ke dashboard, `$_SESSION` terisi
5. ✅ Dashboard menampilkan nama sesuai user yang login
6. ✅ Akses dashboard **tanpa login** (session kosong) → otomatis ditendang ke `login.php`
7. ✅ Logout → session dihapus, akses dashboard setelahnya ditolak
8. ✅ Login dengan centang "Ingat Saya" → cookie tersimpan
9. ✅ Simulasi "buka browser baru" (session hilang, cookie masih ada) → **auto-login berhasil** tanpa perlu input ulang password

---

## 5. Keputusan Desain yang Perlu Dipahami (bukan cuma disalin)

- **Kenapa pesan error login digeneralisasi** ("Email atau password salah", bukan "Email tidak ditemukan" vs "Password salah")? Supaya orang lain tidak bisa menebak-nebak email mana saja yang terdaftar di sistem — ini praktik keamanan dasar.
- **Kenapa `flock()` dipakai saat menulis JSON?** Supaya kalau ada dua proses mencoba menulis file secara bersamaan (jarang terjadi di tugas kuliah, tapi penting dipahami), data tidak saling menimpa secara rusak.
- **Kenapa token Remember Me dihapus dari `users.json` saat logout** (bukan cuma cookie di browser yang dihapus)? Supaya kalau ada orang lain yang kebetulan masih punya salinan cookie lama, token itu sudah tidak valid lagi di sisi server.

---

## 6. Keterbatasan yang Disadari (jujur, bukan disembunyikan)

- Penyimpanan berbasis file JSON ini **tidak cocok untuk aplikasi nyata** dengan banyak pengguna sekaligus (idealnya pakai database). Ini dipakai di sini karena memang itu yang diminta instruksi tugas, untuk fokus belajar fundamental PHP tanpa kerumitan setup database.
- Tidak ada proteksi CSRF (Cross-Site Request Forgery) pada form — di luar cakupan materi "fundamental" yang diajarkan sejauh ini, tapi penting diketahui untuk pengembangan lebih lanjut.
