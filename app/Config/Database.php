<?php
/**
 * ============================================================================
 * KONFIGURASI DATABASE - PENGATURAN KONEKSI KE DATABASE
 * ============================================================================
 * 
 * File ini mengatur koneksi ke database MySQL.
 * Pastikan pengaturan ini sesuai dengan konfigurasi server Anda:
 * - hostname: Alamat server database (biasanya localhost)
 * - username: Username untuk akses database (default: root)
 * - password: Password database (kosong untuk Laragon default)
 * - database: Nama database yang digunakan
 * 
 * Framework: CodeIgniter 4
 * Author: [Nama Anda]
 * ============================================================================
 */

namespace Config;

use CodeIgniter\Database\Config;

/**
 * Class Database
 * Konfigurasi koneksi database
 */
class Database extends Config
{
    /**
     * Direktori yang menyimpan file Migrations dan Seeds
     * Migrations: untuk membuat/mengubah struktur tabel
     * Seeds: untuk mengisi data awal
     */
    public string $filesPath = APPPATH . 'Database' . DIRECTORY_SEPARATOR;

    /**
     * Grup koneksi default yang digunakan jika tidak ada yang ditentukan
     */
    public string $defaultGroup = 'default';

    /**
     * ========================================================================
     * KONFIGURASI KONEKSI DATABASE UTAMA
     * ========================================================================
     * Ini adalah konfigurasi utama untuk koneksi database
     * Sesuaikan dengan pengaturan server database Anda
     */
    public array $default = [
        // DSN (Data Source Name) - kosongkan jika menggunakan konfigurasi individual
        'DSN'      => '',
        
        // Hostname server database (localhost untuk server lokal)
        'hostname' => 'localhost',
        
        // Username database (default Laragon: root)
        'username' => 'root',
        
        // Password database (kosong untuk Laragon default)
        'password' => '',
        
        // Nama database yang digunakan untuk aplikasi ini
        'database' => 'db_laporan_fasilitas',
        
        // Driver database yang digunakan (MySQLi untuk MySQL)
        'DBDriver' => 'MySQLi',
        
        // Prefix tabel (kosong = tidak menggunakan prefix)
        'DBPrefix' => '',
        
        // Persistent connection (false = koneksi baru setiap request)
        'pConnect' => false,
        
        // Debug mode (true = tampilkan error database)
        'DBDebug'  => true,
        
        // Character set untuk encoding data
        'charset'  => 'utf8',
        
        // Collation untuk pengurutan data
        'DBCollat' => 'utf8_general_ci',
        
        // Swap prefix (untuk mengganti prefix secara otomatis)
        'swapPre'  => '',
        
        // Enkripsi koneksi (false = tidak menggunakan SSL)
        'encrypt'  => false,
        
        // Kompresi data (false = tidak dikompresi)
        'compress' => false,
        
        // Strict mode MySQL (false = lebih fleksibel)
        'strictOn' => false,
        
        // Konfigurasi failover (koneksi cadangan jika utama gagal)
        'failover' => [],
        
        // Port MySQL (default: 3306)
        'port'     => 3306,
    ];

    /**
     * ========================================================================
     * KONFIGURASI DATABASE UNTUK TESTING
     * ========================================================================
     * Koneksi ini digunakan saat menjalankan PHPUnit tests
     * Menggunakan SQLite in-memory untuk testing cepat
     */
    public array $tests = [
        'DSN'         => '',
        'hostname'    => '127.0.0.1',
        'username'    => '',
        'password'    => '',
        'database'    => ':memory:', // Database di memori (sementara)
        'DBDriver'    => 'SQLite3',  // Driver SQLite untuk testing
        'DBPrefix'    => 'db_',
        'pConnect'    => false,
        'DBDebug'     => true,
        'charset'     => 'utf8',
        'DBCollat'    => 'utf8_general_ci',
        'swapPre'     => '',
        'encrypt'     => false,
        'compress'    => false,
        'strictOn'    => false,
        'failover'    => [],
        'port'        => 3306,
        'foreignKeys' => true,
        'busyTimeout' => 1000, // Timeout untuk SQLite
    ];

    /**
     * Constructor
     * Otomatis menggunakan database 'tests' jika dalam environment testing
     */
    public function __construct()
    {
        parent::__construct();

        // Jika environment adalah 'testing', gunakan database tests
        // Ini untuk mencegah data produksi tertimpa saat testing
        if (ENVIRONMENT === 'testing') {
            $this->defaultGroup = 'tests';
        }
    }
}
