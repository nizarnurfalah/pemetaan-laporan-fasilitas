<?php
/**
 * ============================================================================
 * MODEL ADMIN - MENGELOLA DATA ADMIN DI DATABASE
 * ============================================================================
 * 
 * Model ini bertanggung jawab untuk:
 * 1. Berinteraksi dengan tabel 'admin' di database
 * 2. Melakukan operasi CRUD untuk data admin
 * 3. Hash password secara otomatis sebelum disimpan (keamanan)
 * 4. Validasi data admin (username unik, password minimal 6 karakter)
 * 
 * Tabel yang digunakan: admin
 * Framework: CodeIgniter 4
 * Author: [Nama Anda]
 * ============================================================================
 */

// Namespace untuk menentukan lokasi file
namespace App\Models;

// Import base Model dari CodeIgniter
use CodeIgniter\Model;

/**
 * Class AdminModel
 * Model untuk mengelola data admin sistem
 */
class AdminModel extends Model
{
    // ========================================================================
    // KONFIGURASI TABEL DATABASE
    // ========================================================================
    
    // Nama tabel di database
    protected $table            = 'admin';
    
    // Nama kolom primary key
    protected $primaryKey       = 'id';
    
    // Gunakan auto increment untuk ID
    protected $useAutoIncrement = true;
    
    // Tipe data yang dikembalikan (array)
    protected $returnType       = 'array';
    
    // Tidak menggunakan soft delete
    protected $useSoftDeletes   = false;
    
    // Aktifkan proteksi field
    protected $protectFields    = true;
    
    // ========================================================================
    // DAFTAR FIELD YANG BOLEH DIISI
    // ========================================================================
    protected $allowedFields    = [
        'username',    // Username admin untuk login
        'password',    // Password admin (akan di-hash sebelum disimpan)
    ];

    // ========================================================================
    // KONFIGURASI TANGGAL/WAKTU
    // ========================================================================
    
    // Tidak menggunakan timestamp otomatis
    protected $useTimestamps = false;
    protected $createdField  = null;
    protected $updatedField  = null;
    protected $deletedField  = null;

    // ========================================================================
    // ATURAN VALIDASI
    // ========================================================================
    protected $validationRules = [
        // Username wajib, minimal 3 karakter, maksimal 100, harus unik
        'username' => 'required|string|min_length[3]|max_length[100]|is_unique[admin.username]',
        
        // Password wajib, minimal 6 karakter
        'password' => 'required|string|min_length[6]',
    ];

    // ========================================================================
    // PESAN ERROR VALIDASI KUSTOM (BAHASA INDONESIA)
    // ========================================================================
    protected $validationMessages = [
        'username' => [
            'required'    => 'Username tidak boleh kosong',
            'min_length'  => 'Username minimal 3 karakter',
            'max_length'  => 'Username maksimal 100 karakter',
            'is_unique'   => 'Username sudah terdaftar',
        ],
        'password' => [
            'required'   => 'Password tidak boleh kosong',
            'min_length' => 'Password minimal 6 karakter',
        ],
    ];

    // Jangan skip validasi
    protected $skipValidation = false;
    
    // Bersihkan aturan validasi saat update
    protected $cleanValidationRules = true;

    // ========================================================================
    // CALLBACKS - FUNGSI YANG DIPANGGIL OTOMATIS
    // ========================================================================
    protected $allowCallbacks = true;
    
    // Panggil hashPassword sebelum insert data baru
    protected $beforeInsert   = ['hashPassword'];
    protected $afterInsert    = [];
    
    // Panggil hashPassword sebelum update data (jika password diubah)
    protected $beforeUpdate   = ['hashPassword'];
    protected $afterUpdate    = [];
    
    protected $beforeFind     = [];
    protected $afterFind      = [];
    protected $beforeDelete   = [];
    protected $afterDelete    = [];

    /**
     * ========================================================================
     * FUNGSI HASH PASSWORD
     * ========================================================================
     * Fungsi callback yang dipanggil otomatis sebelum insert/update
     * Mengenkripsi password menggunakan algoritma BCRYPT
     * 
     * BCRYPT adalah algoritma hashing yang aman karena:
     * 1. One-way hash (tidak bisa di-decrypt)
     * 2. Menambahkan salt otomatis
     * 3. Lambat secara desain (mencegah brute force)
     * 
     * @param array $data - Data yang akan disimpan
     * @return array - Data dengan password yang sudah di-hash
     */
    protected function hashPassword(array $data)
    {
        // Cek apakah ada field password dalam data
        if (isset($data['data']['password'])) {
            // Hash password menggunakan PASSWORD_BCRYPT
            // password_hash() akan menghasilkan hash yang berbeda setiap kali
            // meskipun password sama (karena random salt)
            $data['data']['password'] = password_hash($data['data']['password'], PASSWORD_BCRYPT);
        }
        return $data;
    }
}
