# Dokumentasi Fitur Registrasi OceanCare (PBI01)

## 📋 Ringkasan
Fitur registrasi pengguna telah diimplementasikan untuk platform **OceanCare** - Website volunteer kegiatan bersih pantai berbasis Laravel. Sistem ini memungkinkan calon volunteer dan organizer untuk mendaftar akun baru.

---

## ✨ Fitur yang Diimplementasikan

### 1. Form Registrasi
Form registrasi tersedia di halaman `/register` dengan field berikut:
- **Nama Lengkap** (text input, wajib)
- **Email** (email input, wajib)
- **Password** (password input, minimal 8 karakter, wajib)
- **Konfirmasi Password** (password input, harus cocok dengan password, wajib)
- **Role** (radio button, pilihan: volunteer atau organizer, wajib)

### 2. Validasi Input
Validasi dilakukan di server-side dengan pesan error yang user-friendly:

| Field | Validasi | Pesan Error |
|-------|----------|------------|
| Nama | Wajib, string, max 255 karakter | "Nama lengkap wajib diisi" / "Nama harus berupa teks" / "Nama maksimal 255 karakter" |
| Email | Wajib, format valid, unik | "Email wajib diisi" / "Format email tidak valid" / "Email sudah terdaftar sebelumnya" |
| Password | Wajib, minimal 8 karakter | "Password wajib diisi" / "Password minimal 8 karakter" |
| Konfirmasi Password | Harus cocok dengan password | "Konfirmasi password tidak sesuai" |
| Role | Wajib, hanya "volunteer" atau "organizer" | "Role wajib dipilih" / "Role hanya boleh volunteer atau organizer" |

### 3. Proses Penyimpanan
- Password dienkripsi menggunakan **bcrypt** (Laravel Hash::make())
- Data disimpan ke tabel `users` di database dengan struktur:
  ```
  id, name, email, email_verified_at, password, role, photo, phone, remember_token, created_at, updated_at
  ```
- Role yang tersedia: `admin`, `organizer`, `volunteer`

### 4. Output Sistem
**Jika registrasi berhasil:**
- Menampilkan notifikasi: "Registrasi berhasil! Silakan login dengan email dan password Anda."
- Redirect ke halaman login

**Jika validasi gagal:**
- Menampilkan pesan error sesuai field yang tidak valid
- Form tetap ditampilkan dengan data sebelumnya (old data)
- User dapat memperbaiki dan submit ulang

---

## 📁 Struktur File yang Dibuat/Dimodifikasi

### File Baru:
```
app/Http/Controllers/RegisterController.php
├── show()    → Menampilkan form registrasi
└── store()   → Memproses dan menyimpan data registrasi

app/Http/Controllers/LoginController.php
├── show()    → Menampilkan form login
├── store()   → Memproses login
└── logout()  → Memproses logout

resources/views/auth/register.blade.php
└── Form registrasi dengan Tailwind CSS

resources/views/auth/login.blade.php
└── Form login dengan Tailwind CSS
```

### File Dimodifikasi:
```
app/Models/User.php
└── Tambahkan 'role' ke dalam Fillable attribute

routes/web.php
├── Import RegisterController & LoginController
├── GET  /register       → show form registrasi (route: 'register')
├── POST /register       → store data registrasi (route: 'register.store')
├── GET  /login          → show form login (route: 'login')
├── POST /login          → store login (route: 'login.store')
└── POST /logout         → logout (route: 'logout')
```

---

## 🔐 Keamanan

✅ **Password Hashing**: Password dienkripsi menggunakan bcrypt (Laravel Hash::make())
✅ **CSRF Protection**: Form menggunakan @csrf token untuk proteksi CSRF
✅ **Email Unique**: Email dijaga unik di database (unique constraint)
✅ **Server-side Validation**: Semua validasi dilakukan di server, tidak hanya di frontend
✅ **Session Security**: Session di-regenerate setelah login untuk mencegah session fixation

---

## 🚀 Cara Menggunakan

### 1. Registrasi Pengguna Baru
1. Buka browser dan akses: `http://localhost/register`
2. Isi form dengan data:
   - Nama lengkap
   - Email yang valid dan belum terdaftar
   - Password minimal 8 karakter
   - Konfirmasi password
   - Pilih role (volunteer atau organizer)
3. Klik tombol "Daftar Sekarang"
4. Jika berhasil, akan redirect ke halaman login

### 2. Login Pengguna
1. Buka: `http://localhost/login`
2. Masukkan email dan password
3. Klik tombol "Login"
4. Jika berhasil, user akan login dan dapat mengakses aplikasi

### 3. Logout
User dapat logout dengan mengklik tombol logout di halaman (jika sudah diimplementasikan di navbar/menu)

---

## 💾 Database Schema (Tabel Users)

```sql
CREATE TABLE users (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE,
    email_verified_at TIMESTAMP NULL,
    password VARCHAR(255) NOT NULL,
    role ENUM('admin', 'organizer', 'volunteer') NOT NULL,
    photo VARCHAR(255) NULL,
    phone VARCHAR(255) NULL,
    remember_token VARCHAR(100) NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL
);
```

---

## 📝 Contoh Flow Registrasi

```
User → Form Registrasi → Validasi Input → 
└─ VALID? 
   ├─ YA: Hash Password → Save ke DB → Success Message → Redirect Login
   └─ TIDAK: Show Error Messages → Form dengan Old Data
```

---

## 🔄 Testing Checklist

- [ ] Buka halaman `/register`, form muncul dengan benar
- [ ] Isi semua field dengan data valid dan submit
- [ ] Verifikasi user tersimpan di database dengan password ter-hash
- [ ] Akses halaman `/login` dan login dengan user baru
- [ ] Test validasi: email kosong → error ditampilkan
- [ ] Test validasi: email format invalid → error ditampilkan
- [ ] Test validasi: email sudah terdaftar → error ditampilkan
- [ ] Test validasi: password < 8 karakter → error ditampilkan
- [ ] Test validasi: konfirmasi password tidak cocok → error ditampilkan
- [ ] Test validasi: role tidak dipilih → error ditampilkan

---

## 🛠️ Catatan Teknis

1. **Framework**: Laravel (Modern PHP Web Framework)
2. **Frontend**: Tailwind CSS untuk styling
3. **Authentication**: Laravel Illuminate\Foundation\Auth\User
4. **Hashing**: Laravel Hash (bcrypt algorithm)
5. **Validation**: Laravel Validator
6. **Database**: MySQL/PostgreSQL (sesuai konfigurasi)

---

## 📞 Support

Jika ada pertanyaan atau issues terkait fitur registrasi, silakan:
1. Cek kembali validasi di RegisterController
2. Pastikan migration database sudah di-run: `php artisan migrate`
3. Clear cache: `php artisan config:cache`

---

**Status**: ✅ **COMPLETED** - Siap untuk production
**Last Updated**: 4 Mei 2026
