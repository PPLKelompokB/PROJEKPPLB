# Spesifikasi Pengujian (Test Specification) - Admin Dashboard & Verification

Dokumen ini merinci prasyarat (*pre-conditions*), kondisi akhir (*post-conditions*), serta skenario kasus uji (*test cases*) untuk fitur **Admin Dashboard** pada platform OceanCare. Pengujian dirancang untuk memverifikasi aspek fungsionalitas utama, keamanan hak akses, dan otomatisasi logika bisnis (seperti alokasi poin dan pengiriman notifikasi).

---

## 1. Peran Pengguna (User Roles)
* **Admin**: Pengguna dengan hak akses tertinggi yang mengelola dan memverifikasi jalannya event.
* **Organizer (Penyelenggara)**: Pembuat event yang menyerahkan berkas dokumentasi.
* **Volunteer (Relawan)**: Peserta event yang berhak menerima poin setelah dokumentasi disetujui.
* **Guest (Pengunjung Umum)**: Pengguna yang belum terautentikasi (belum login).

---

## 2. Prasyarat Umum & Kondisi Akhir (General Pre-conditions & Post-conditions)

### A. Tampilan Utama Dashboard & Manajemen Event
* **Prasyarat (Pre-conditions)**:
  * Pengguna terautentikasi sebagai **Admin**.
  * Database memiliki data statistik (pengguna, event, event selesai) serta daftar event aktif/non-aktif untuk ditampilkan.
* **Kondisi Akhir (Post-conditions)**:
  * Admin dapat melihat metrik agregat yang valid (Total Users, Total Events, Total Finished Events).
  * Tabel manajemen menampilkan event dengan format data yang benar dan mendukung pagination (10 data per halaman).

### B. Proses Verifikasi Dokumentasi Event (PBI-14 & PBI-15)
* **Prasyarat (Pre-conditions)**:
  * Akun Organizer telah mengunggah minimal satu berkas dokumentasi dengan status `pending` ke suatu event.
  * Terdapat beberapa Volunteer yang terdaftar secara sah pada event terkait.
* **Kondisi Akhir (Post-conditions)**:
  * **Jika Disetujui (Approved)**:
    * Status dokumentasi berubah menjadi `approved`.
    * Poin dihitung secara otomatis berdasarkan formula: `durasi_event (jam) * 10`.
    * Poin ditambahkan ke tabel `points` dan kolom `points` pada tabel `users` untuk seluruh volunteer yang terdaftar.
    * Mencegah duplikasi poin jika ada request verifikasi ulang.
    * Notifikasi bertipe `success` dikirimkan ke Organizer.
  * **Jika Ditolak (Rejected)**:
    * Status dokumentasi berubah menjadi `rejected`.
    * Tidak ada poin yang didistribusikan ke volunteer.
    * Notifikasi bertipe `error` dikirimkan ke Organizer.

---

## 3. Matriks Kasus Uji (Test Cases Matrix)

| TC ID | Test Case | Langkah Pengujian | Expected Results |
| :--- | :--- | :--- | :--- |
| **TC-ADM-01** | Akses Admin Dashboard sebagai Admin | 1. Buka halaman `/login`<br>2. Masukkan credentials Admin yang valid (email & password)<br>3. Klik submit<br>4. Akses URL `/admin/dashboard` | - Login berhasil dan dialihkan secara otomatis ke `/admin/dashboard`. <br>- Menampilkan header "Admin Dashboard", 3 kartu statistik, dan tabel manajemen event. |
| **TC-ADM-02** | Blokir Akses Tamu (Guest) ke Dashboard | 1. Akses langsung URL `/admin/dashboard` tanpa login | - Sistem menolak akses.<br>- Pengguna dialihkan ke halaman login (`/login`) dengan status HTTP 302/401. |
| **TC-ADM-03** | Blokir Akses Volunteer ke Dashboard | 1. Login sebagai pengguna dengan role `volunteer`<br>2. Coba akses URL `/admin/dashboard` | - Sistem menolak akses.<br>- Halaman menampilkan pesan error 403 Forbidden atau dialihkan kembali ke `/volunteer/dashboard`. |
| **TC-ADM-04** | Blokir Akses Organizer ke Dashboard | 1. Login sebagai pengguna dengan role `organizer`<br>2. Coba akses URL `/admin/dashboard` | - Sistem menolak akses.<br>- Halaman menampilkan pesan error 403 Forbidden atau dialihkan kembali ke `/organizer/dashboard`. |
| **TC-ADM-05** | Akurasi Agregasi Statistik Dashboard | 1. Login sebagai Admin<br>2. Masuk ke halaman `/admin/dashboard`<br>3. Amati nilai pada kartu statistik "Total Users", "Total Events", dan "Total Finished Events" | - Kartu "Total Users" menampilkan jumlah seluruh user terdaftar.<br>- Kartu "Total Events" menampilkan jumlah seluruh event di database.<br>- Kartu "Total Finished Events" menampilkan jumlah event yang tanggalnya sudah lewat. |
| **TC-ADM-06** | Pagination Tabel Manajemen Event | 1. Login sebagai Admin<br>2. Masuk ke `/admin/dashboard` dengan database memiliki lebih dari 10 event<br>3. Scroll ke bagian bawah tabel manajemen event | - Tabel hanya menampilkan maksimal 10 baris data event.<br>- Navigasi halaman (Page 1, Page 2, Next) muncul di bawah tabel.<br>- Tombol angka halaman dan "Next" aktif untuk memuat sisa data berikutnya. |
| **TC-ADM-07** | Aksi "View Event" dari Tabel | 1. Login sebagai Admin<br>2. Masuk ke `/admin/dashboard`<br>3. Klik tombol aksi detail event (ikon mata) di kolom Actions pada salah satu event | - Admin berhasil diarahkan ke halaman detail publik event tersebut (`/events/{id}`). |
| **TC-ADM-08** | Pencarian Event di Dashboard | 1. Login sebagai Admin<br>2. Pada kolom input "Search events...", ketik keyword nama/judul event tertentu (misal: "Kuta") | - Baris tabel secara dinamis tersaring.<br>- Hanya menampilkan event yang memiliki nama/judul sesuai kata kunci "Kuta". |
| **TC-ADM-09** | Akses Menu Verifikasi Dokumentasi | 1. Login sebagai Admin<br>2. Akses halaman `/admin/documentation` | - Admin diarahkan ke halaman "Manage Event Documentation" (`/admin/documentation`).<br>- Menampilkan daftar card event beserta jumlah berkas dokumentasi yang telah diunggah oleh organizer. |
| **TC-ADM-10** | Tampilan Detail Review Dokumentasi | 1. Login sebagai Admin<br>2. Akses halaman `/admin/documentation`<br>3. Klik tombol "Review Documentation" pada salah satu event | - Admin diarahkan ke halaman detail review (`/admin/documentation/{eventId}`).<br>- Halaman memuat judul event, detail foto berkas dokumentasi yang diunggah, catatan (note) deskripsi, tanggal unggah, serta status awal `pending`. |
| **TC-ADM-11** | Approve Dokumentasi Tunggal & Alokasi Poin | 1. Buka detail review dokumentasi suatu event berstatus `pending`<br>2. Klik tombol "Approve" pada salah satu berkas dokumentasi<br>3. Klik tombol "Approve!" pada modal konfirmasi | - Status dokumentasi terupdate menjadi `approved` (badge hitam).<br>- Seluruh volunteer yang terdaftar pada event tersebut secara otomatis mendapatkan tambahan poin (durasi event jam * 10).<br>- Organizer menerima notifikasi sukses ("Documentation Approved"). |
| **TC-ADM-12** | Pencegahan Duplikasi Poin (Idempotensi) | 1. Jalankan request POST ke `/documentation/{id}/verify` dengan parameter `status = approved` untuk dokumentasi yang sudah disetujui sebelumnya | - Server menolak tindakan dengan mengembalikan kode status HTTP 400 Bad Request.<br>- Menampilkan pesan kesalahan: "Status dokumentasi sudah tidak bisa diubah karena sudah diapproved."<br>- Poin volunteer tidak bertambah lagi. |
| **TC-ADM-13** | Reject Dokumentasi Tunggal | 1. Buka detail review dokumentasi suatu event berstatus `pending`<br>2. Klik tombol "Reject" pada salah satu berkas dokumentasi<br>3. Klik tombol "Reject!" pada modal konfirmasi | - Status dokumentasi terupdate menjadi `rejected` (badge abu-abu).<br>- Tidak ada alokasi poin yang didistribusikan ke volunteer.<br>- Organizer menerima notifikasi peringatan/error ("Documentation Rejected"). |
| **TC-ADM-14** | Bulk Approve Semua Dokumentasi Pending | 1. Buka detail review dokumentasi event yang memiliki beberapa berkas berstatus `pending`<br>2. Klik tombol "Approve All Pending" di bar header halaman<br>3. Klik tombol "Approve!" pada modal konfirmasi | - Semua berkas dokumentasi pending pada event tersebut berubah status menjadi `approved`.<br>- Poin dibagikan sekali ke setiap volunteer yang terdaftar pada event tersebut (tanpa terjadi akumulasi ganda). |
| **TC-ADM-15** | Bulk Reject Semua Dokumentasi Pending | 1. Buka detail review dokumentasi event yang memiliki beberapa berkas berstatus `pending`<br>2. Klik tombol "Reject All Pending" di bar header halaman<br>3. Klik tombol "Reject!" pada modal konfirmasi | - Semua berkas dokumentasi pending pada event tersebut berubah status menjadi `rejected`.<br>- Tidak ada alokasi poin yang diberikan ke volunteer.<br>- Organizer menerima notifikasi peringatan/error. |
| **TC-ADM-16** | Validasi Input Status pada Verifikasi | 1. Jalankan POST request ke `/documentation/{id}/verify` dengan body kosong atau nilai status tidak sesuai aturan (misalnya: `pending`) | - Server mengembalikan kode status HTTP 422 Unprocessable Entity.<br>- Menampilkan detail pesan error validasi input parameter. |
| **TC-ADM-17** | Verifikasi ID Dokumentasi Tidak Valid | 1. Jalankan POST request ke `/documentation/9999/verify` (ID yang tidak eksis) dengan parameter `status = approved` | - Server mengembalikan kode status HTTP 404 Not Found karena data dokumentasi tidak ditemukan. |
