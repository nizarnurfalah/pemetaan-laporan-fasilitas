<?php
/**
 * ============================================================================
 * CONTROLLER AUTH - MENANGANI AUTENTIKASI ADMIN
 * ============================================================================
 * 
 * File ini adalah controller untuk menangani proses autentikasi admin:
 * 1. Login admin - Memverifikasi username dan password
 * 2. Logout admin - Menghapus session dan keluar dari sistem
 * 3. Register admin - Mendaftarkan admin baru (opsional)
 * 
 * Framework: CodeIgniter 4
 * Author: [Nama Anda]
 * ============================================================================
 */

// Namespace untuk menentukan lokasi file dalam struktur MVC
namespace App\Controllers;

// Import model AdminModel untuk berinteraksi dengan tabel admin di database
use App\Models\AdminModel;

/**
 * Class Auth
 * Controller untuk menangani autentikasi (login, logout, register)
 */
class Auth extends BaseController
{
    // Variabel untuk menyimpan instance model admin
    protected $adminModel;

    /**
     * Constructor - Dijalankan pertama kali saat controller diakses
     * Fungsi: Menginisialisasi AdminModel
     */
    public function __construct()
    {
        // Membuat instance baru dari AdminModel untuk operasi database
        $this->adminModel = new AdminModel();
    }

    /**
     * ========================================================================
     * FUNGSI LOGIN - MENAMPILKAN FORM LOGIN
     * ========================================================================
     * Fungsi ini menampilkan halaman form login untuk admin
     * Jika admin sudah login, akan diarahkan ke dashboard
     * 
     * URL: /login
     * Method: GET
     * 
     * @return View|Response - Form login atau redirect ke dashboard
     */
    public function login()
    {
        // Cek apakah admin sudah login dengan melihat session
        // Jika ada session 'admin_id', berarti sudah login
        if (session()->get('admin_id')) {
            // Redirect ke halaman dashboard admin
            return redirect()->to(base_url('admin'));
        }

        // Jika belum login, tampilkan halaman form login
        return view('auth/v_login');
    }

    /**
     * ========================================================================
     * FUNGSI PROSES LOGIN - MEMVERIFIKASI KREDENSIAL
     * ========================================================================
     * Fungsi ini memproses data login yang dikirim dari form
     * Melakukan verifikasi username dan password dengan database
     * 
     * URL: /login
     * Method: POST
     * 
     * @return Response - Redirect ke dashboard jika berhasil atau kembali dengan error
     */
    public function prosesLogin()
    {
        // Cek apakah request menggunakan method POST
        if ($this->request->getMethod() === 'post') {
            
            // Ambil data dari form
            $username = $this->request->getPost('username');  // Username yang diinput
            $password = $this->request->getPost('password');  // Password yang diinput

            // Cari admin di database berdasarkan username
            // Method where() untuk filter, first() untuk ambil 1 data
            $admin = $this->adminModel->where('username', $username)->first();

            // Verifikasi password menggunakan password_verify()
            // password_verify() membandingkan password plain dengan hash di database
            if ($admin && password_verify($password, $admin['password'])) {
                
                // ============================================================
                // LOGIN BERHASIL - BUAT SESSION
                // ============================================================
                // Simpan data admin ke session
                session()->set([
                    'admin_id' => $admin['id'],           // ID admin untuk identifikasi
                    'username' => $admin['username'],     // Username untuk ditampilkan
                    'is_logged_in' => true,               // Flag status login
                ]);

                // Redirect ke dashboard dengan pesan sukses
                return redirect()->to(base_url('admin'))->with('success', 'Login berhasil!');
            } else {
                
                // ============================================================
                // LOGIN GAGAL - USERNAME ATAU PASSWORD SALAH
                // ============================================================
                return redirect()->back()->with('error', 'Username atau password salah');
            }
        }
    }

    /**
     * ========================================================================
     * FUNGSI LOGOUT - KELUAR DARI SISTEM
     * ========================================================================
     * Fungsi ini menghapus semua session dan mengeluarkan admin dari sistem
     * 
     * URL: /logout
     * Method: GET
     * 
     * @return Response - Redirect ke halaman utama
     */
    public function logout()
    {
        // Hapus semua session (destroy session)
        session()->destroy();
        
        // Redirect ke halaman utama dengan pesan sukses
        return redirect()->to(base_url())->with('success', 'Logout berhasil');
    }

    /**
     * ========================================================================
     * FUNGSI REGISTER - FORM REGISTRASI ADMIN BARU
     * ========================================================================
     * Fungsi ini menampilkan form untuk mendaftarkan admin baru
     * CATATAN: Sebaiknya dibatasi hanya bisa diakses oleh admin yang sudah ada
     * 
     * URL: /register
     * Method: GET
     * 
     * @return View - Form registrasi admin
     */
    public function register()
    {
        // Tampilkan halaman form registrasi
        return view('auth/v_register');
    }

    /**
     * ========================================================================
     * FUNGSI PROSES REGISTER - MENYIMPAN ADMIN BARU
     * ========================================================================
     * Fungsi ini memproses data registrasi admin baru
     * Password akan di-hash secara otomatis oleh AdminModel
     * 
     * URL: /register
     * Method: POST
     * 
     * @return Response - Redirect ke login jika berhasil atau kembali dengan error
     */
    public function prosesRegister()
    {
        // Cek apakah request menggunakan method POST
        if ($this->request->getMethod() === 'post') {
            
            // Siapkan data admin baru dari form
            $data = [
                'username' => $this->request->getPost('username'),  // Username baru
                'password' => $this->request->getPost('password'),  // Password (akan di-hash oleh model)
            ];

            // Simpan data ke database
            // Model akan otomatis hash password sebelum disimpan
            if ($this->adminModel->save($data)) {
                // Berhasil, redirect ke halaman login
                return redirect()->to(base_url('login'))->with('success', 'Akun berhasil dibuat! Silakan login');
            } else {
                // Gagal (mungkin karena validasi), kembali dengan pesan error
                return redirect()->back()->withInput()->with('error', $this->adminModel->errors());
            }
        }
    }
}
