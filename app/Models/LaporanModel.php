<?php
/**
 * ============================================================================
 * MODEL LAPORAN - MENGELOLA DATA LAPORAN DI DATABASE
 * ============================================================================
 * 
 * Model ini bertanggung jawab untuk:
 * 1. Berinteraksi dengan tabel 'laporan' di database
 * 2. Melakukan operasi CRUD (Create, Read, Update, Delete)
 * 3. Validasi data sebelum disimpan ke database
 * 
 * Tabel yang digunakan: laporan
 * Framework: CodeIgniter 4
 * Author: [Nama Anda]
 * ============================================================================
 */

// Namespace untuk menentukan lokasi file
namespace App\Models;

// Import base Model dari CodeIgniter
use CodeIgniter\Model;

/**
 * Class LaporanModel
 * Model untuk mengelola data laporan kerusakan fasilitas
 */
class LaporanModel extends Model
{
    // ========================================================================
    // KONFIGURASI TABEL DATABASE
    // ========================================================================
    
    // Nama tabel di database yang akan digunakan
    protected $table            = 'laporan';
    
    // Nama kolom primary key (ID unik untuk setiap baris)
    protected $primaryKey       = 'id';
    
    // Gunakan auto increment untuk primary key (ID bertambah otomatis)
    protected $useAutoIncrement = true;
    
    // Tipe data yang dikembalikan ('array' atau 'object')
    protected $returnType       = 'array';
    
    // Tidak menggunakan soft delete (data benar-benar dihapus, bukan ditandai)
    protected $useSoftDeletes   = false;
    
    // Aktifkan proteksi field (hanya field di allowedFields yang bisa diisi)
    protected $protectFields    = true;
    
    // ========================================================================
    // DAFTAR FIELD YANG BOLEH DIISI
    // ========================================================================
    // Field-field ini adalah kolom yang bisa diisi melalui insert/update
    // Field yang tidak ada di sini tidak bisa diisi (untuk keamanan)
    protected $allowedFields    = [
        'email_pelapor',               // Email pengguna yang melaporkan
        'jenis_kerusakan',             // Jenis kerusakan (Jalan Berlubang/Fasilitas Publik Rusak)
        'deskripsi',                   // Deskripsi detail kerusakan
        'foto_lokasi',                 // Nama file foto yang diupload
        'latitude',                    // Koordinat latitude lokasi
        'longitude',                   // Koordinat longitude lokasi
        'status',                      // Status laporan (Baru/Diproses/Dijadwalkan/Selesai)
        'prioritas',                   // Prioritas penanganan (tinggi/sedang/rendah)
        'tgl_lapor',                   // Tanggal laporan dibuat
        'tgl_perbaikan_dijadwalkan',   // Tanggal rencana perbaikan
        'catatan_admin',               // Catatan dari admin
        'is_verified',                 // Status verifikasi (0=belum, 1=disetujui, 2=ditolak)
        'verified_at',                 // Waktu verifikasi
        'verified_by',                 // Username admin yang memverifikasi
        'rejection_reason',            // Alasan penolakan (jika ditolak)
    ];

    // ========================================================================
    // KONFIGURASI TANGGAL/WAKTU
    // ========================================================================
    
    // Tidak menggunakan timestamp otomatis CodeIgniter
    protected $useTimestamps = false;
    
    // Nama field untuk created_at (dibuat manual di tabel sebagai tgl_lapor)
    protected $createdField  = 'tgl_lapor';
    
    // Tidak ada field updated_at
    protected $updatedField  = null;
    
    // Tidak ada field deleted_at (tidak menggunakan soft delete)
    protected $deletedField  = null;

    // ========================================================================
    // ATURAN VALIDASI
    // ========================================================================
    // Aturan validasi untuk memastikan data yang masuk valid
    protected $validationRules = [
        // Email harus diisi dan format valid
        'email_pelapor'             => 'required|valid_email',
        
        // Jenis kerusakan harus salah satu dari opsi yang ditentukan
        'jenis_kerusakan'           => 'required|in_list[Jalan Berlubang,Fasilitas Publik Rusak]',
        
        // Deskripsi wajib diisi dan berupa string
        'deskripsi'                 => 'required|string',
        
        // Foto wajib diisi (nama file)
        'foto_lokasi'               => 'required|string',
        
        // Latitude wajib diisi dan berupa angka
        'latitude'                  => 'required|numeric',
        
        // Longitude wajib diisi dan berupa angka
        'longitude'                 => 'required|numeric',
        
        // Status harus salah satu dari opsi yang ditentukan
        'status'                    => 'in_list[Baru,Diproses,Dijadwalkan,Selesai]',
        
        // Prioritas opsional, jika diisi harus salah satu dari opsi
        'prioritas'                 => 'permit_empty|in_list[tinggi,sedang,rendah]',
        
        // Tanggal perbaikan opsional, jika diisi harus format tanggal valid
        'tgl_perbaikan_dijadwalkan' => 'permit_empty|valid_date',
        
        // Catatan admin opsional
        'catatan_admin'             => 'permit_empty|string',
    ];

    // ========================================================================
    // PESAN ERROR VALIDASI KUSTOM
    // ========================================================================
    // Pesan error yang lebih informatif untuk pengguna
    protected $validationMessages = [
        'email_pelapor'   => 'Email pelapor harus valid',
        'jenis_kerusakan' => 'Jenis kerusakan harus Jalan Berlubang atau Fasilitas Publik Rusak',
        'deskripsi'       => 'Deskripsi tidak boleh kosong',
        'foto_lokasi'     => 'Foto lokasi tidak boleh kosong',
        'latitude'        => 'Latitude harus angka',
        'longitude'       => 'Longitude harus angka',
    ];

    // Jangan skip validasi (selalu validasi data)
    protected $skipValidation = false;
    
    // Bersihkan aturan validasi saat update (hanya validasi field yang diupdate)
    protected $cleanValidationRules = true;

    // ========================================================================
    // CALLBACKS (FUNGSI OTOMATIS)
    // ========================================================================
    // Callbacks adalah fungsi yang dipanggil otomatis saat event tertentu
    protected $allowCallbacks = true;     // Aktifkan callbacks
    protected $beforeInsert   = [];       // Dipanggil sebelum insert
    protected $afterInsert    = [];       // Dipanggil setelah insert
    protected $beforeUpdate   = [];       // Dipanggil sebelum update
    protected $afterUpdate    = [];       // Dipanggil setelah update
    protected $beforeFind     = [];       // Dipanggil sebelum find
    protected $afterFind      = [];       // Dipanggil setelah find
    protected $beforeDelete   = [];       // Dipanggil sebelum delete
    protected $afterDelete    = [];       // Dipanggil setelah delete
}
