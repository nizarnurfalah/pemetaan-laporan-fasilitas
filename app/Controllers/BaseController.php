<?php
/**
 * ============================================================================
 * BASE CONTROLLER - CONTROLLER DASAR UNTUK SEMUA CONTROLLER
 * ============================================================================
 * 
 * BaseController adalah kelas induk (parent class) yang diwarisi oleh semua
 * controller lain dalam aplikasi. Fungsi utamanya:
 * 1. Menyediakan fungsi-fungsi dasar yang dibutuhkan semua controller
 * 2. Memuat helper dan library yang digunakan secara global
 * 3. Tempat untuk menambahkan logika yang berlaku untuk seluruh aplikasi
 * 
 * Semua controller di aplikasi ini extends (mewarisi) BaseController
 * Contoh: class Laporan extends BaseController
 * 
 * Framework: CodeIgniter 4
 * Author: [Nama Anda]
 * ============================================================================
 */

// Namespace untuk menentukan lokasi file
namespace App\Controllers;

// Import class-class yang diperlukan dari CodeIgniter
use CodeIgniter\Controller;          // Base controller CodeIgniter
use CodeIgniter\HTTP\CLIRequest;     // Request dari Command Line Interface
use CodeIgniter\HTTP\IncomingRequest; // Request dari HTTP (browser)
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;         // Interface untuk logging

/**
 * Class BaseController
 * 
 * Kelas abstrak (abstract class) yang menyediakan tempat untuk memuat
 * komponen dan melakukan fungsi yang dibutuhkan oleh semua controller.
 * 
 * Abstract class artinya kelas ini tidak bisa dibuat instance-nya langsung,
 * hanya bisa diwarisi (extend) oleh kelas lain.
 */
abstract class BaseController extends Controller
{
    /**
     * Instance dari objek Request utama
     * Digunakan untuk mengakses data request seperti GET, POST, dll
     * 
     * @var CLIRequest|IncomingRequest
     */
    protected $request;

    /**
     * Array helper yang akan dimuat otomatis saat controller dibuat
     * Helper adalah kumpulan fungsi-fungsi bantuan
     * 'form' helper menyediakan fungsi-fungsi untuk membuat form HTML
     * 
     * @var array
     */
    protected $helpers = ['form'];

    /**
     * Deklarasikan properti untuk property fetch yang diinisialisasi
     * PHP 8.2 tidak mengizinkan dynamic property (properti yang dibuat on-the-fly)
     * Jadi semua properti harus dideklarasikan di sini
     */
    // protected $session;

    /**
     * Constructor - Inisialisasi Controller
     * 
     * Method ini dipanggil oleh CodeIgniter saat controller dibuat
     * Gunakan untuk memuat model, library, helper, dll yang dibutuhkan
     * oleh semua method dalam controller
     * 
     * @param RequestInterface $request - Object request HTTP
     * @param ResponseInterface $response - Object response HTTP
     * @param LoggerInterface $logger - Object untuk logging
     */
    public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger)
    {
        // JANGAN EDIT BARIS INI - Memanggil parent constructor
        // Ini penting untuk inisialisasi controller dasar CodeIgniter
        parent::initController($request, $response, $logger);

        // ================================================================
        // PRELOAD MODEL, LIBRARY, DLL DI SINI
        // ================================================================
        // Tambahkan kode untuk memuat model atau library yang dibutuhkan
        // oleh semua controller di sini
        
        // Contoh: Memuat session service
        // $this->session = \Config\Services::session();
        
        // Contoh: Memuat model yang sering digunakan
        // $this->userModel = new \App\Models\UserModel();
    }
}
