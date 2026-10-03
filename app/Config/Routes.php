<?php
/**
 * ============================================================================
 * KONFIGURASI ROUTING - PEMETAAN URL KE CONTROLLER
 * ============================================================================
 * 
 * File ini mengatur bagaimana URL dipetakan ke controller dan method
 * Setiap route mendefinisikan: URL -> Controller::method
 * 
 * Jenis Route:
 * - GET: Mengambil/menampilkan data (read)
 * - POST: Mengirim/menyimpan data (create/update)
 * 
 * Framework: CodeIgniter 4
 * Author: [Nama Anda]
 * ============================================================================
 */

namespace Config;

// Buat instance RouteCollection untuk mendefinisikan routes
$routes = Services::routes();

/*
 * ============================================================================
 * KONFIGURASI ROUTER
 * ============================================================================
 */

// Namespace default untuk controller (semua controller ada di App\Controllers)
$routes->setDefaultNamespace('App\Controllers');

// Controller default yang dipanggil jika tidak ada URL spesifik
$routes->setDefaultController('Laporan');

// Method default yang dipanggil jika tidak ada method spesifik
$routes->setDefaultMethod('index');

// Tidak mengubah dash (-) menjadi underscore dalam URL
$routes->setTranslateURIDashes(false);

// Tidak ada override untuk halaman 404
$routes->set404Override();

// Matikan auto-routing (hanya route yang didefinisikan yang bisa diakses)
// Ini penting untuk keamanan!
$routes->setAutoRoute(false);

/*
 * ============================================================================
 * DEFINISI ROUTE - HALAMAN PUBLIK (GUEST)
 * ============================================================================
 */

// ------------------------------------------------------------------------
// HALAMAN UTAMA - WEBGIS (Peta Laporan)
// ------------------------------------------------------------------------
// Route: / atau /laporan
// Menampilkan peta WebGIS dengan semua laporan yang sudah diverifikasi
$routes->get('/', 'Laporan::index');
$routes->get('laporan', 'Laporan::index');

// ------------------------------------------------------------------------
// GUEST - FITUR PELAPORAN
// ------------------------------------------------------------------------
// Form tambah laporan baru - Menampilkan form untuk membuat laporan
$routes->get('laporan/tambah', 'Laporan::tambah');

// Proses simpan laporan - Menyimpan data laporan ke database
$routes->post('laporan/simpan', 'Laporan::simpan');

// Halaman "Laporan Saya" - Form input email untuk melihat laporan sendiri
$routes->get('laporan/saya', 'Laporan::laporanSaya');

// Proses verifikasi email - Untuk melihat daftar laporan berdasarkan email
$routes->post('laporan/verifikasi-email', 'Laporan::verifikasiEmail');

// Detail laporan - Menampilkan detail satu laporan berdasarkan ID
// (:num) berarti parameter harus berupa angka
$routes->get('laporan/detail/(:num)', 'Laporan::detail/$1');

// API JSON - Mengambil data laporan dalam format JSON untuk peta
$routes->get('laporan/getDataJson', 'Laporan::getDataJson');

// ------------------------------------------------------------------------
// API - ENDPOINT DATA JSON
// ------------------------------------------------------------------------
// Endpoint alternatif untuk mengambil data laporan (format JSON)
$routes->get('api/laporan', 'Laporan::getDataJson');

/*
 * ============================================================================
 * DEFINISI ROUTE - AUTENTIKASI (LOGIN/REGISTER)
 * ============================================================================
 */

// ------------------------------------------------------------------------
// LOGIN ADMIN
// ------------------------------------------------------------------------
// GET: Menampilkan form login
$routes->get('login', 'Auth::login');

// POST: Memproses data login (verifikasi username & password)
$routes->post('login', 'Auth::prosesLogin');

// ------------------------------------------------------------------------
// REGISTER ADMIN (Opsional - bisa dinonaktifkan untuk keamanan)
// ------------------------------------------------------------------------
// GET: Menampilkan form registrasi
$routes->get('register', 'Auth::register');

// POST: Memproses data registrasi (simpan admin baru)
$routes->post('register', 'Auth::prosesRegister');

// ------------------------------------------------------------------------
// LOGOUT
// ------------------------------------------------------------------------
// Keluar dari sistem dan hapus session
$routes->get('logout', 'Auth::logout');

/*
 * ============================================================================
 * DEFINISI ROUTE - PANEL ADMIN
 * ============================================================================
 * Semua route di bawah ini memerlukan login admin
 * Controller: Admin\Dashboard
 */

// ------------------------------------------------------------------------
// DASHBOARD ADMIN
// ------------------------------------------------------------------------
// Halaman utama dashboard - Menampilkan statistik dan ringkasan
$routes->get('admin', 'Admin\Dashboard::index');
$routes->get('admin/dashboard', 'Admin\Dashboard::index');

// ------------------------------------------------------------------------
// DAFTAR LAPORAN
// ------------------------------------------------------------------------
// Menampilkan semua laporan (dengan Hybrid Scheduling)
$routes->get('admin/dashboard/daftar', 'Admin\Dashboard::daftarLaporan');

// Verifikasi laporan (approve/reject)
// (:num)/(:num) = ID laporan / status (1=approve, 2=reject)
$routes->get('admin/dashboard/verify/(:num)/(:num)', 'Admin\Dashboard::verifyLaporan/$1/$2');

// Alias untuk daftar laporan
$routes->get('admin/laporan', 'Admin\Dashboard::daftarLaporan');

// ------------------------------------------------------------------------
// LAPORAN BARU (Belum Diverifikasi)
// ------------------------------------------------------------------------
// Menampilkan laporan yang menunggu verifikasi admin
$routes->get('admin/laporan/baru', 'Admin\Dashboard::laporanBaru');

// ------------------------------------------------------------------------
// DETAIL & UPDATE LAPORAN
// ------------------------------------------------------------------------
// Detail laporan - Melihat detail lengkap satu laporan
$routes->get('admin/laporan/detail/(:num)', 'Admin\Dashboard::detailLaporan/$1');

// Update status laporan - Mengubah status, prioritas, jadwal perbaikan
$routes->post('admin/laporan/status/(:num)', 'Admin\Dashboard::updateStatus/$1');

// Hapus laporan - Menghapus laporan dari sistem
$routes->get('admin/laporan/hapus/(:num)', 'Admin\Dashboard::hapusLaporan/$1');

// ------------------------------------------------------------------------
// EXPORT DATA KE PDF
// ------------------------------------------------------------------------
// Export laporan dalam format PDF (tabel)
$routes->get('admin/export/pdf', 'Admin\Dashboard::exportPDF');

// Export laporan dengan gambar (PDF detail)
$routes->get('admin/export/images', 'Admin\Dashboard::exportPDFWithImages');

// Export laporan sederhana (PDF simple)
$routes->get('admin/export/simple', 'Admin\Dashboard::exportPDFSimple');

/*
 * ============================================================================
 * ROUTE TAMBAHAN (ENVIRONMENT-SPECIFIC)
 * ============================================================================
 * Memuat route khusus berdasarkan environment (development/production)
 */
if (is_file(APPPATH . 'Config/' . ENVIRONMENT . '/Routes.php')) {
    require APPPATH . 'Config/' . ENVIRONMENT . '/Routes.php';
}
