# USE CASE DOCUMENT
## Sistem WebGIS Pemetaan Laporan Fasilitas Umum

---

## 1. Deskripsi Sistem

Sistem WebGIS Pemetaan Laporan Fasilitas Umum adalah aplikasi berbasis web yang memungkinkan masyarakat untuk melaporkan kerusakan fasilitas umum (seperti jalan berlubang atau fasilitas publik rusak) secara online dengan menampilkan lokasi kerusakan pada peta digital (WebGIS). Sistem ini menggunakan metode **Hybrid Scheduling** untuk mengelola prioritas perbaikan berdasarkan tingkat urgensi dan waktu pelaporan.

---

## 2. Aktor (Actors)

| No | Aktor | Deskripsi |
|----|-------|-----------|
| 1 | **Guest (Masyarakat/Pelapor)** | Pengguna umum yang dapat melihat peta, membuat laporan kerusakan fasilitas, dan melihat status laporan mereka |
| 2 | **Admin** | Pengelola sistem yang dapat memverifikasi, mengelola, dan memproses laporan kerusakan |

---

## 3. Diagram Use Case

```
                                    ┌─────────────────────────────────────────┐
                                    │     SISTEM WEBGIS PEMETAAN LAPORAN      │
                                    │           FASILITAS UMUM                │
                                    └─────────────────────────────────────────┘
                                                        │
        ┌───────────────────────────────────────────────┼───────────────────────────────────────────────┐
        │                                               │                                               │
        ▼                                               ▼                                               ▼
┌───────────────────┐                       ┌───────────────────┐                       ┌───────────────────┐
│  GUEST/PELAPOR    │                       │      UMUM         │                       │      ADMIN        │
└───────────────────┘                       └───────────────────┘                       └───────────────────┘
        │                                               │                                               │
        ├── UC-01: Melihat Peta WebGIS ◄────────────────┤                                               │
        │                                               │                                               │
        ├── UC-02: Membuat Laporan Baru                 │                                               │
        │                                                                                               │
        ├── UC-03: Melihat Detail Laporan                                                               │
        │                                                                                               │
        ├── UC-04: Mengedit Laporan Sendiri                                                             │
        │                                                                                               │
        ├── UC-05: Melihat Daftar Laporan Saya                                                          │
        │                                                                                               │
        ├── UC-06: Verifikasi Email                                                                     │
        │                                                                                               │
        │                                               ├── UC-07: Login ◄──────────────────────────────┤
        │                                               │                                               │
        │                                               ├── UC-08: Logout ◄─────────────────────────────┤
        │                                               │                                               │
        │                                               ├── UC-09: Register ◄───────────────────────────┤
        │                                                                                               │
        │                                                                       ┌───────────────────────┤
        │                                                                       │                       │
        │                                                       UC-10: Melihat Dashboard ◄─────────────┤
        │                                                                                               │
        │                                                       UC-11: Melihat Daftar Laporan ◄────────┤
        │                                                                                               │
        │                                                       UC-12: Verifikasi Laporan ◄────────────┤
        │                                                                                               │
        │                                                       UC-13: Update Status Laporan ◄─────────┤
        │                                                                                               │
        │                                                       UC-14: Hapus Laporan ◄─────────────────┤
        │                                                                                               │
        │                                                       UC-15: Export Laporan ke PDF ◄─────────┤
        │                                                                                               │
        └───────────────────────────────────────────────────────────────────────────────────────────────┘
```

---

## 4. Daftar Use Case

### 4.1 Use Case untuk Guest (Masyarakat/Pelapor)

| ID | Nama Use Case | Deskripsi |
|----|---------------|-----------|
| UC-01 | Melihat Peta WebGIS | Guest dapat melihat peta yang menampilkan lokasi kerusakan fasilitas yang sudah diverifikasi |
| UC-02 | Membuat Laporan Baru | Guest dapat membuat laporan kerusakan fasilitas dengan mengisi form dan menandai lokasi di peta |
| UC-03 | Melihat Detail Laporan | Guest dapat melihat detail informasi laporan tertentu |
| UC-04 | Mengedit Laporan Sendiri | Guest dapat mengedit laporan yang sudah dibuat setelah verifikasi email |
| UC-05 | Melihat Daftar Laporan Saya | Guest dapat melihat daftar laporan yang pernah dibuat berdasarkan email |
| UC-06 | Verifikasi Email | Guest harus memverifikasi email untuk mengakses/mengedit laporan |

### 4.2 Use Case untuk Admin

| ID | Nama Use Case | Deskripsi |
|----|---------------|-----------|
| UC-07 | Login | Admin melakukan login ke sistem |
| UC-08 | Logout | Admin keluar dari sistem |
| UC-09 | Register | Membuat akun admin baru |
| UC-10 | Melihat Dashboard | Admin melihat ringkasan statistik laporan |
| UC-11 | Melihat Daftar Laporan | Admin melihat semua laporan yang masuk |
| UC-12 | Verifikasi Laporan | Admin menyetujui atau menolak laporan baru |
| UC-13 | Update Status Laporan | Admin memperbarui status, prioritas, dan jadwal perbaikan |
| UC-14 | Hapus Laporan | Admin menghapus laporan dari sistem |
| UC-15 | Export Laporan ke PDF | Admin mengekspor data laporan dalam format PDF |

---

## 5. Detail Use Case

---

### UC-01: Melihat Peta WebGIS

| Atribut | Deskripsi |
|---------|-----------|
| **ID** | UC-01 |
| **Nama** | Melihat Peta WebGIS |
| **Aktor** | Guest, Admin |
| **Deskripsi** | Pengguna dapat melihat peta interaktif yang menampilkan marker lokasi kerusakan fasilitas yang sudah diverifikasi |
| **Precondition** | Pengguna mengakses halaman utama sistem |
| **Postcondition** | Peta WebGIS ditampilkan dengan marker lokasi laporan |

**Alur Utama (Main Flow):**
1. Pengguna membuka halaman utama sistem
2. Sistem menampilkan peta WebGIS
3. Sistem mengambil data laporan yang sudah diverifikasi (is_verified = 1)
4. Sistem menampilkan marker pada peta sesuai koordinat laporan
5. Pengguna dapat mengklik marker untuk melihat informasi singkat

**Alur Alternatif:**
- 3a. Jika tidak ada laporan terverifikasi, peta ditampilkan tanpa marker

---

### UC-02: Membuat Laporan Baru

| Atribut | Deskripsi |
|---------|-----------|
| **ID** | UC-02 |
| **Nama** | Membuat Laporan Baru |
| **Aktor** | Guest |
| **Deskripsi** | Guest dapat membuat laporan kerusakan fasilitas umum dengan mengisi formulir dan menandai lokasi di peta |
| **Precondition** | Guest mengakses halaman tambah laporan |
| **Postcondition** | Laporan tersimpan dengan status "Baru" dan is_verified = 0 |

**Alur Utama (Main Flow):**
1. Guest mengakses halaman "Tambah Laporan"
2. Sistem menampilkan formulir laporan dan peta
3. Guest mengisi data laporan:
   - Email pelapor (wajib, valid email)
   - Jenis kerusakan (Jalan Berlubang / Fasilitas Publik Rusak)
   - Deskripsi kerusakan
   - Foto lokasi (JPG, PNG, GIF, WebP - max 5MB)
   - Lokasi di peta (latitude & longitude)
4. Guest mencentang reCAPTCHA untuk verifikasi
5. Guest menekan tombol "Kirim Laporan"
6. Sistem memvalidasi data dan reCAPTCHA
7. Sistem menyimpan foto ke folder uploads
8. Sistem menyimpan data laporan ke database
9. Sistem mengirim email konfirmasi ke pelapor
10. Sistem menampilkan pesan sukses

**Alur Alternatif:**
- 6a. Jika validasi gagal, sistem menampilkan pesan error
- 6b. Jika reCAPTCHA tidak valid, sistem menolak pengiriman
- 7a. Jika file tidak valid/terlalu besar, sistem menampilkan error

---

### UC-03: Melihat Detail Laporan

| Atribut | Deskripsi |
|---------|-----------|
| **ID** | UC-03 |
| **Nama** | Melihat Detail Laporan |
| **Aktor** | Guest, Admin |
| **Deskripsi** | Pengguna dapat melihat informasi lengkap dari sebuah laporan |
| **Precondition** | Laporan dengan ID yang diminta ada di database |
| **Postcondition** | Detail laporan ditampilkan |

**Alur Utama (Main Flow):**
1. Pengguna mengakses halaman detail laporan dengan ID tertentu
2. Sistem mencari laporan berdasarkan ID
3. Sistem menampilkan detail laporan:
   - ID Laporan
   - Email pelapor
   - Jenis kerusakan
   - Deskripsi
   - Foto lokasi
   - Koordinat (latitude, longitude)
   - Status laporan
   - Tanggal lapor
   - Jadwal perbaikan (jika ada)
   - Catatan admin (jika ada)

**Alur Alternatif:**
- 2a. Jika laporan tidak ditemukan, sistem menampilkan halaman 404

---

### UC-04: Mengedit Laporan Sendiri

| Atribut | Deskripsi |
|---------|-----------|
| **ID** | UC-04 |
| **Nama** | Mengedit Laporan Sendiri |
| **Aktor** | Guest |
| **Deskripsi** | Guest dapat mengedit laporan yang sudah dibuat setelah verifikasi email |
| **Precondition** | - Laporan ada di database<br>- Email guest sudah terverifikasi untuk laporan tersebut |
| **Postcondition** | Data laporan diperbarui |

**Alur Utama (Main Flow):**
1. Guest mengakses halaman edit laporan
2. Sistem memeriksa apakah email sudah terverifikasi
3. Jika belum, sistem menampilkan form verifikasi email
4. Guest memasukkan email pelapor
5. Sistem memvalidasi email dengan data laporan
6. Jika cocok, sistem menyimpan session dan menampilkan form edit
7. Guest mengubah data yang diperlukan
8. Guest menekan tombol "Simpan"
9. Sistem memvalidasi dan menyimpan perubahan
10. Sistem mengirim email notifikasi perubahan
11. Sistem menampilkan pesan sukses

**Alur Alternatif:**
- 5a. Jika email tidak cocok, sistem menampilkan pesan error

---

### UC-05: Melihat Daftar Laporan Saya

| Atribut | Deskripsi |
|---------|-----------|
| **ID** | UC-05 |
| **Nama** | Melihat Daftar Laporan Saya |
| **Aktor** | Guest |
| **Deskripsi** | Guest dapat melihat semua laporan yang pernah dibuat berdasarkan email |
| **Precondition** | Guest memiliki laporan yang pernah dibuat |
| **Postcondition** | Daftar laporan milik guest ditampilkan |

**Alur Utama (Main Flow):**
1. Guest mengakses halaman "Laporan Saya"
2. Sistem menampilkan form input email
3. Guest memasukkan email
4. Sistem mencari semua laporan dengan email tersebut
5. Sistem menampilkan daftar laporan dengan informasi:
   - ID Laporan
   - Jenis kerusakan
   - Status
   - Tanggal lapor

**Alur Alternatif:**
- 4a. Jika tidak ada laporan dengan email tersebut, sistem menampilkan pesan "Tidak ditemukan"

---

### UC-06: Verifikasi Email

| Atribut | Deskripsi |
|---------|-----------|
| **ID** | UC-06 |
| **Nama** | Verifikasi Email |
| **Aktor** | Guest |
| **Deskripsi** | Guest memverifikasi kepemilikan laporan dengan memasukkan email yang sesuai |
| **Precondition** | Guest ingin mengakses/mengedit laporan tertentu |
| **Postcondition** | Session verifikasi email tersimpan |

**Alur Utama (Main Flow):**
1. Guest mengakses halaman yang memerlukan verifikasi email
2. Sistem menampilkan form input email
3. Guest memasukkan email pelapor
4. Sistem memvalidasi email dengan data laporan
5. Jika cocok, sistem menyimpan session verifikasi
6. Guest dapat melanjutkan aksi yang diinginkan

**Alur Alternatif:**
- 4a. Jika email tidak cocok, sistem menampilkan pesan error

---

### UC-07: Login

| Atribut | Deskripsi |
|---------|-----------|
| **ID** | UC-07 |
| **Nama** | Login |
| **Aktor** | Admin |
| **Deskripsi** | Admin melakukan autentikasi untuk masuk ke sistem |
| **Precondition** | Admin memiliki akun yang terdaftar |
| **Postcondition** | Session admin tersimpan, admin diarahkan ke dashboard |

**Alur Utama (Main Flow):**
1. Admin mengakses halaman login
2. Sistem menampilkan form login
3. Admin memasukkan username dan password
4. Sistem memvalidasi kredensial
5. Jika valid, sistem menyimpan session (admin_id, username, is_logged_in)
6. Sistem mengarahkan admin ke dashboard

**Alur Alternatif:**
- 4a. Jika kredensial salah, sistem menampilkan pesan error
- 1a. Jika sudah login, sistem langsung mengarahkan ke dashboard

---

### UC-08: Logout

| Atribut | Deskripsi |
|---------|-----------|
| **ID** | UC-08 |
| **Nama** | Logout |
| **Aktor** | Admin |
| **Deskripsi** | Admin keluar dari sistem |
| **Precondition** | Admin sudah login |
| **Postcondition** | Session dihapus, admin diarahkan ke halaman utama |

**Alur Utama (Main Flow):**
1. Admin mengklik tombol/link logout
2. Sistem menghapus session
3. Sistem mengarahkan ke halaman utama dengan pesan sukses

---

### UC-09: Register

| Atribut | Deskripsi |
|---------|-----------|
| **ID** | UC-09 |
| **Nama** | Register |
| **Aktor** | Admin (Calon) |
| **Deskripsi** | Membuat akun admin baru |
| **Precondition** | - |
| **Postcondition** | Akun admin baru tersimpan di database |

**Alur Utama (Main Flow):**
1. Pengguna mengakses halaman register
2. Sistem menampilkan form registrasi
3. Pengguna mengisi username dan password
4. Pengguna menekan tombol register
5. Sistem memvalidasi data
6. Sistem menyimpan data admin (password di-hash)
7. Sistem mengarahkan ke halaman login dengan pesan sukses

**Alur Alternatif:**
- 5a. Jika validasi gagal, sistem menampilkan pesan error

---

### UC-10: Melihat Dashboard

| Atribut | Deskripsi |
|---------|-----------|
| **ID** | UC-10 |
| **Nama** | Melihat Dashboard |
| **Aktor** | Admin |
| **Deskripsi** | Admin melihat ringkasan statistik laporan |
| **Precondition** | Admin sudah login |
| **Postcondition** | Dashboard dengan statistik ditampilkan |

**Alur Utama (Main Flow):**
1. Admin mengakses halaman dashboard
2. Sistem memeriksa session admin
3. Sistem menghitung statistik:
   - Total laporan
   - Laporan baru
   - Laporan diproses
   - Laporan dijadwalkan
   - Laporan selesai
   - Statistik prioritas (tinggi, sedang, rendah)
4. Sistem mengambil daftar laporan dengan Hybrid Scheduling (prioritas + tanggal)
5. Sistem menampilkan dashboard dengan statistik dan daftar laporan

**Alur Alternatif:**
- 2a. Jika belum login, sistem mengarahkan ke halaman login

---

### UC-11: Melihat Daftar Laporan

| Atribut | Deskripsi |
|---------|-----------|
| **ID** | UC-11 |
| **Nama** | Melihat Daftar Laporan |
| **Aktor** | Admin |
| **Deskripsi** | Admin melihat semua laporan yang masuk ke sistem |
| **Precondition** | Admin sudah login |
| **Postcondition** | Daftar laporan ditampilkan |

**Alur Utama (Main Flow):**
1. Admin mengakses halaman daftar laporan
2. Sistem memeriksa session admin
3. Sistem mengambil semua laporan dengan Hybrid Scheduling
4. Sistem menampilkan daftar laporan dengan informasi:
   - ID, Email, Jenis Kerusakan, Status, Prioritas, Tanggal

**Filter Tambahan:**
- Laporan Baru (belum diverifikasi)
- Filter berdasarkan status, jenis, bulan, tahun

---

### UC-12: Verifikasi Laporan

| Atribut | Deskripsi |
|---------|-----------|
| **ID** | UC-12 |
| **Nama** | Verifikasi Laporan |
| **Aktor** | Admin |
| **Deskripsi** | Admin menyetujui atau menolak laporan yang baru masuk |
| **Precondition** | - Admin sudah login<br>- Laporan dengan status belum diverifikasi |
| **Postcondition** | Status verifikasi laporan diperbarui |

**Alur Utama (Main Flow):**
1. Admin melihat daftar laporan baru
2. Admin memilih laporan untuk diverifikasi
3. Admin memilih aksi: Setujui (1) atau Tolak (2)
4. Jika tolak, admin dapat memberikan alasan
5. Sistem memperbarui data verifikasi:
   - is_verified (1=approved, 2=rejected)
   - verified_at
   - verified_by
   - rejection_reason (jika ditolak)
6. Sistem mengirim email notifikasi ke pelapor
7. Sistem menampilkan pesan sukses

**Alur Alternatif:**
- 6a. Jika pengiriman email gagal, sistem tetap melanjutkan

---

### UC-13: Update Status Laporan

| Atribut | Deskripsi |
|---------|-----------|
| **ID** | UC-13 |
| **Nama** | Update Status Laporan |
| **Aktor** | Admin |
| **Deskripsi** | Admin memperbarui status, prioritas, dan jadwal perbaikan laporan |
| **Precondition** | - Admin sudah login<br>- Laporan ada di database |
| **Postcondition** | Data laporan diperbarui |

**Alur Utama (Main Flow):**
1. Admin mengakses detail laporan
2. Admin mengubah data:
   - Status: Baru / Diproses / Dijadwalkan / Selesai
   - Prioritas: Tinggi / Sedang / Rendah
   - Tanggal perbaikan dijadwalkan
   - Catatan admin
3. Admin menekan tombol "Simpan"
4. Sistem menyimpan perubahan
5. Sistem mengirim email notifikasi ke pelapor
6. Sistem menampilkan pesan sukses

---

### UC-14: Hapus Laporan

| Atribut | Deskripsi |
|---------|-----------|
| **ID** | UC-14 |
| **Nama** | Hapus Laporan |
| **Aktor** | Admin |
| **Deskripsi** | Admin menghapus laporan dari sistem |
| **Precondition** | - Admin sudah login<br>- Laporan ada di database |
| **Postcondition** | Laporan dan file foto terkait dihapus |

**Alur Utama (Main Flow):**
1. Admin mengakses daftar laporan
2. Admin memilih laporan yang akan dihapus
3. Sistem menampilkan konfirmasi
4. Admin mengkonfirmasi penghapusan
5. Sistem menghapus file foto dari folder uploads
6. Sistem menghapus data laporan dari database
7. Sistem menampilkan pesan sukses

**Alur Alternatif:**
- 5a. Jika file tidak ditemukan, sistem tetap melanjutkan penghapusan data

---

### UC-15: Export Laporan ke PDF

| Atribut | Deskripsi |
|---------|-----------|
| **ID** | UC-15 |
| **Nama** | Export Laporan ke PDF |
| **Aktor** | Admin |
| **Deskripsi** | Admin mengekspor data laporan dalam format PDF |
| **Precondition** | - Admin sudah login<br>- TCPDF library tersedia |
| **Postcondition** | File PDF terunduh |

**Alur Utama (Main Flow):**
1. Admin mengakses fitur export PDF
2. Admin memilih filter (opsional):
   - Status
   - Jenis kerusakan
   - Bulan
   - Tahun
3. Admin menekan tombol "Export"
4. Sistem mengambil data sesuai filter
5. Sistem membuat dokumen PDF dengan TCPDF
6. Sistem menyertakan informasi laporan dalam tabel
7. File PDF terunduh ke komputer admin

**Variasi:**
- Export Simple: Data terbatas tanpa gambar
- Export dengan Images: Menyertakan foto lokasi

---

## 6. Matriks Kebutuhan Fungsional

| Kebutuhan | Use Case Terkait |
|-----------|------------------|
| Sistem dapat menampilkan peta interaktif | UC-01 |
| Sistem dapat menerima laporan kerusakan | UC-02 |
| Sistem dapat mengirim email notifikasi | UC-02, UC-04, UC-12, UC-13 |
| Sistem dapat memverifikasi kepemilikan laporan | UC-04, UC-05, UC-06 |
| Sistem dapat mengautentikasi admin | UC-07, UC-08 |
| Sistem dapat menampilkan statistik dashboard | UC-10 |
| Sistem dapat memverifikasi laporan | UC-12 |
| Sistem dapat mengubah status laporan | UC-13 |
| Sistem dapat menghapus laporan | UC-14 |
| Sistem dapat mengekspor laporan ke PDF | UC-15 |

---

## 7. Hybrid Scheduling

Sistem mengimplementasikan **Hybrid Scheduling** untuk mengelola prioritas penanganan laporan:

### Algoritma:
1. **Prioritas Utama**: Laporan diurutkan berdasarkan tingkat prioritas
   - Tinggi (High Priority)
   - Sedang (Medium Priority)
   - Rendah (Low Priority)

2. **Prioritas Sekunder**: Dalam prioritas yang sama, laporan diurutkan berdasarkan tanggal lapor (FIFO - First In First Out)

### Query SQL:
```sql
ORDER BY FIELD(prioritas, 'tinggi', 'sedang', 'rendah') ASC, tgl_lapor ASC
```

---

## 8. Status Laporan

| Status | Deskripsi |
|--------|-----------|
| **Baru** | Laporan baru masuk, belum ditangani |
| **Diproses** | Laporan sedang dalam proses penanganan |
| **Dijadwalkan** | Perbaikan sudah dijadwalkan |
| **Selesai** | Perbaikan telah selesai dilakukan |

---

## 9. Status Verifikasi

| Nilai | Status | Deskripsi |
|-------|--------|-----------|
| 0 | Pending | Menunggu verifikasi admin |
| 1 | Approved | Disetujui, tampil di peta publik |
| 2 | Rejected | Ditolak, tidak tampil di peta |

---

## 10. Teknologi yang Digunakan

| Komponen | Teknologi |
|----------|-----------|
| Backend Framework | CodeIgniter 4 |
| Database | MySQL |
| Frontend | HTML, CSS, JavaScript |
| Peta | Leaflet.js / OpenStreetMap |
| PDF Generator | TCPDF |
| Security | reCAPTCHA, Password Hashing |
| Email | CodeIgniter Email Library |

---

## Dokumen Dibuat:
- **Tanggal**: Januari 2026
- **Project**: Sistem WebGIS Pemetaan Laporan Fasilitas Umum
- **Framework**: CodeIgniter 4
