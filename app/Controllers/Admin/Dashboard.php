<?php
/**
 * ============================================================================
 * CONTROLLER DASHBOARD ADMIN - MENGELOLA PANEL ADMINISTRASI
 * ============================================================================
 * 
 * File ini adalah controller untuk menangani semua fungsi panel admin:
 * 1. Dashboard - Menampilkan statistik dan ringkasan laporan
 * 2. Daftar Laporan - Melihat semua laporan dengan Hybrid Scheduling
 * 3. Laporan Baru - Melihat laporan yang menunggu verifikasi
 * 4. Detail Laporan - Melihat detail lengkap satu laporan
 * 5. Update Status - Mengubah status, prioritas, jadwal perbaikan
 * 6. Verifikasi - Menyetujui atau menolak laporan
 * 7. Hapus Laporan - Menghapus laporan dari sistem
 * 8. Export PDF - Mengekspor data ke format PDF
 * 
 * HYBRID SCHEDULING:
 * - Mengurutkan laporan berdasarkan prioritas (tinggi -> sedang -> rendah)
 * - Kemudian berdasarkan tanggal (yang lebih lama diproses dulu)
 * 
 * Framework: CodeIgniter 4
 * Author: [Nama Anda]
 * ============================================================================
 */

// Namespace dalam subfolder Admin
namespace App\Controllers\Admin;

// Import BaseController dan Model yang diperlukan
use App\Controllers\BaseController;
use App\Models\LaporanModel;
use App\Models\AdminModel;

/**
 * Class Dashboard
 * Controller untuk mengelola panel administrasi
 */
class Dashboard extends BaseController
{
    // Variabel untuk menyimpan instance model
    protected $laporanModel;
    protected $adminModel;

    /**
     * Constructor - Menginisialisasi model yang diperlukan
     */
    public function __construct()
    {
        // Inisialisasi model untuk operasi database
        $this->laporanModel = new LaporanModel();
        $this->adminModel = new AdminModel();
    }

    /**
     * ========================================================================
     * FUNGSI INDEX - HALAMAN DASHBOARD UTAMA
     * ========================================================================
     * Menampilkan dashboard admin dengan statistik laporan
     * Statistik meliputi: total laporan, per status, dan per prioritas
     * 
     * URL: /admin atau /admin/dashboard
     * Method: GET
     * Akses: Hanya admin yang sudah login
     */
    public function index()
    {
        // ================================================================
        // CEK SESSION ADMIN
        // ================================================================
        // Pastikan admin sudah login, jika tidak redirect ke halaman login
        if (!session()->get('admin_id')) {
            return redirect()->to(base_url('login'));
        }

        // ================================================================
        // HITUNG STATISTIK LAPORAN BERDASARKAN STATUS
        // ================================================================
        // Total semua laporan
        $data['total_laporan'] = $this->laporanModel->countAll();
        
        // Jumlah laporan dengan status "Baru"
        $data['laporan_baru'] = $this->laporanModel->where('status', 'Baru')->countAllResults();
        
        // Jumlah laporan dengan status "Diproses"
        $data['laporan_diproses'] = $this->laporanModel->where('status', 'Diproses')->countAllResults();
        
        // Jumlah laporan dengan status "Dijadwalkan"
        $data['laporan_dijadwalkan'] = $this->laporanModel->where('status', 'Dijadwalkan')->countAllResults();
        
        // Jumlah laporan dengan status "Selesai"
        $data['laporan_selesai'] = $this->laporanModel->where('status', 'Selesai')->countAllResults();
        
        // ================================================================
        // HITUNG STATISTIK LAPORAN BERDASARKAN PRIORITAS
        // ================================================================
        // Digunakan untuk Hybrid Scheduling Algorithm
        $data['prioritas_tinggi'] = $this->laporanModel->where('prioritas', 'tinggi')->countAllResults();
        $data['prioritas_sedang'] = $this->laporanModel->where('prioritas', 'sedang')->countAllResults();
        $data['prioritas_rendah'] = $this->laporanModel->where('prioritas', 'rendah')->countAllResults();
        
        // ================================================================
        // AMBIL DATA LAPORAN DENGAN HYBRID SCHEDULING
        // ================================================================
        // Hybrid Scheduling: Urutkan berdasarkan:
        // 1. Prioritas (tinggi dulu, lalu sedang, lalu rendah)
        // 2. Tanggal lapor (yang lebih lama diproses lebih dulu - FIFO)
        $data['laporan'] = $this->laporanModel
            ->orderBy("FIELD(prioritas, 'tinggi', 'sedang', 'rendah')", '', false) // Urutan prioritas custom
            ->orderBy('tgl_lapor', 'ASC') // Ascending = yang lama dulu
            ->findAll();

        // Tampilkan view dashboard
        return view('admin/v_dashboard', $data);
    }

    /**
     * ========================================================================
     * FUNGSI DAFTAR LAPORAN - MENAMPILKAN SEMUA LAPORAN
     * ========================================================================
     * Menampilkan semua laporan dengan pengurutan Hybrid Scheduling
     * 
     * URL: /admin/laporan atau /admin/dashboard/daftar
     * Method: GET
     * Akses: Hanya admin yang sudah login
     */
    public function daftarLaporan()
    {
        // Cek session admin - jika tidak ada, redirect ke login
        if (!session()->get('admin_id')) {
            session()->setFlashdata('error', 'Silakan login terlebih dahulu untuk mengakses halaman admin.');
            return redirect()->to(base_url('login'));
        }

        try {
            // ============================================================
            // AMBIL DATA DENGAN HYBRID SCHEDULING ALGORITHM
            // ============================================================
            // Algoritma ini menggabungkan 2 kriteria pengurutan:
            // 1. Priority Scheduling: Prioritas tinggi diproses lebih dulu
            // 2. FIFO (First In First Out): Laporan yang lebih lama diproses dulu
            $data['laporan'] = $this->laporanModel
                ->orderBy("FIELD(prioritas, 'tinggi', 'sedang', 'rendah')", '', false) // Custom order
                ->orderBy('tgl_lapor', 'ASC') // Yang lama dulu
                ->findAll();
            
            // Jika tidak ada data, inisialisasi sebagai array kosong
            if (!$data['laporan']) {
                $data['laporan'] = [];
            }
            
            // Tampilkan view daftar laporan
            return view('admin/v_daftar_laporan', $data);
            
        } catch (\Exception $e) {
            // Jika terjadi error, catat ke log dan redirect dengan pesan error
            log_message('error', 'Error in daftarLaporan: ' . $e->getMessage());
            session()->setFlashdata('error', 'Terjadi kesalahan saat mengambil data laporan.');
            return redirect()->to(base_url('admin'));
        }
    }

    /**
     * ========================================================================
     * FUNGSI LAPORAN BARU - MENAMPILKAN LAPORAN YANG BELUM DIVERIFIKASI
     * ========================================================================
     * Menampilkan semua laporan yang menunggu verifikasi admin
     * (is_verified = 0)
     * 
     * URL: /admin/laporan/baru
     * Method: GET
     * Akses: Hanya admin yang sudah login
     */
    public function laporanBaru()
    {
        // Cek session admin
        if (!session()->get('admin_id')) {
            session()->setFlashdata('error', 'Silakan login terlebih dahulu untuk mengakses halaman admin.');
            return redirect()->to(base_url('login'));
        }

        try {
            // Ambil laporan yang belum diverifikasi (is_verified = 0)
            // Urutkan dari yang terbaru untuk prioritas review
            $data['laporan'] = $this->laporanModel
                ->where('is_verified', 0) // 0 = belum diverifikasi
                ->orderBy('tgl_lapor', 'DESC') // Terbaru dulu
                ->findAll();
                
            // Inisialisasi array kosong jika tidak ada data
            if (!$data['laporan']) {
                $data['laporan'] = [];
            }
            
            // Tampilkan view laporan baru
            return view('admin/v_laporan_baru', $data);
            
        } catch (\Exception $e) {
            log_message('error', 'Error in laporanBaru: ' . $e->getMessage());
            session()->setFlashdata('error', 'Terjadi kesalahan saat mengambil data laporan baru.');
            return redirect()->to(base_url('admin'));
        }
    }

    /**
     * ========================================================================
     * FUNGSI DETAIL LAPORAN - MENAMPILKAN DETAIL SATU LAPORAN
     * ========================================================================
     * Menampilkan informasi lengkap dari satu laporan berdasarkan ID
     * 
     * URL: /admin/laporan/detail/{id}
     * Method: GET
     * 
     * @param int $id - ID laporan yang ingin dilihat
     */
    public function detailLaporan($id)
    {
        // Cek session admin
        if (!session()->get('admin_id')) {
            return redirect()->to(base_url('login'));
        }

        // Cari laporan berdasarkan ID
        $laporan = $this->laporanModel->find($id);
        
        // Jika tidak ditemukan, tampilkan error 404
        if (!$laporan) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Laporan tidak ditemukan');
        }

        // Kirim data ke view
        $data['laporan'] = $laporan;
        return view('admin/v_detail_laporan', $data);
    }

    /**
     * ========================================================================
     * FUNGSI UPDATE STATUS - MEMPERBARUI STATUS LAPORAN
     * ========================================================================
     * Admin dapat mengubah:
     * - Status (Baru/Diproses/Dijadwalkan/Selesai)
     * - Prioritas (tinggi/sedang/rendah)
     * - Tanggal perbaikan dijadwalkan
     * - Catatan admin
     * 
     * URL: /admin/laporan/status/{id}
     * Method: POST
     * 
     * @param int $id - ID laporan yang akan diupdate
     */
    public function updateStatus($id)
    {
        // Cek session admin
        if (!session()->get('admin_id')) {
            return redirect()->to(base_url('login'));
        }

        // Proses jika method POST
        if ($this->request->getMethod() === 'post') {
            
            // Siapkan data yang akan diupdate
            $data = [
                'id'                        => $id,
                'status'                    => $this->request->getPost('status'),                    // Status baru
                'prioritas'                 => $this->request->getPost('prioritas'),                 // Prioritas baru
                'tgl_perbaikan_dijadwalkan' => $this->request->getPost('tgl_perbaikan_dijadwalkan'), // Jadwal perbaikan
                'catatan_admin'             => $this->request->getPost('catatan_admin'),             // Catatan dari admin
            ];

            // Simpan perubahan ke database
            if ($this->laporanModel->save($data)) {
                // Ambil data laporan lengkap untuk email
                $laporan = $this->laporanModel->find($id);
                
                // Kirim email notifikasi ke pelapor
                $this->sendEmailNotifikasi($laporan);

                return redirect()->to(base_url('admin/laporan'))->with('success', 'Status laporan berhasil diperbarui!');
            } else {
                return redirect()->back()->with('error', 'Gagal memperbarui status');
            }
        }
    }

    /**
     * ========================================================================
     * FUNGSI HAPUS LAPORAN - MENGHAPUS LAPORAN DARI SISTEM
     * ========================================================================
     * Menghapus laporan secara permanen beserta file fotonya
     * 
     * URL: /admin/laporan/hapus/{id}
     * Method: GET
     * 
     * @param int $id - ID laporan yang akan dihapus
     */
    public function hapusLaporan($id)
    {
        // Cek session admin
        if (!session()->get('admin_id')) {
            return redirect()->to(base_url('login'));
        }

        // Cari laporan berdasarkan ID
        $laporan = $this->laporanModel->find($id);
        if (!$laporan) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Laporan tidak ditemukan');
        }

        // Hapus file foto dari server
        $file = FCPATH . 'uploads/' . $laporan['foto_lokasi'];
        if (file_exists($file)) {
            unlink($file); // Hapus file
        }

        // Hapus data dari database
        if ($this->laporanModel->delete($id)) {
            return redirect()->to(base_url('admin/laporan'))->with('success', 'Laporan berhasil dihapus!');
        } else {
            return redirect()->back()->with('error', 'Gagal menghapus laporan');
        }
    }

    /**
     * ========================================================================
     * FUNGSI VERIFIKASI LAPORAN - MENYETUJUI ATAU MENOLAK LAPORAN
     * ========================================================================
     * Admin dapat menyetujui (approve) atau menolak (reject) laporan
     * Status:
     * - 1 = Disetujui (approved) - laporan tampil di peta
     * - 2 = Ditolak (rejected) - laporan tidak tampil
     * 
     * URL: /admin/dashboard/verify/{id}/{status}
     * Method: GET
     * 
     * @param int $id - ID laporan
     * @param int $status - 1 untuk approve, 2 untuk reject
     */
    public function verifyLaporan($id, $status)
    {
        // Cek session admin
        if (!session()->get('admin_id')) {
            return redirect()->to(base_url('login'));
        }

        // Cari laporan
        $laporan = $this->laporanModel->find($id);
        if (!$laporan) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Laporan tidak ditemukan');
        }

        // Siapkan data verifikasi
        $data = [
            'is_verified' => $status, // 1 = approved, 2 = rejected
            'verified_at' => date('Y-m-d H:i:s'), // Waktu verifikasi
            'verified_by' => session()->get('admin_username'), // Admin yang memverifikasi
        ];

        // Jika ditolak, tambahkan alasan penolakan
        if ($status == 2) {
            $reason = $this->request->getGet('reason'); // Ambil alasan dari query string
            $data['rejection_reason'] = $reason ?? 'Tidak memenuhi kriteria';
        }

        // Update data di database
        if ($this->laporanModel->update($id, $data)) {
            // Kirim email notifikasi ke pelapor
            $this->sendVerificationEmail($laporan, $status, $data['rejection_reason'] ?? null);
            
            $message = $status == 1 ? 'Laporan berhasil disetujui!' : 'Laporan berhasil ditolak!';
            return redirect()->to(base_url('admin/dashboard/daftar'))->with('success', $message);
        } else {
            return redirect()->back()->with('error', 'Gagal memverifikasi laporan');
        }
    }

    /**
     * ========================================================================
     * FUNGSI KIRIM EMAIL VERIFIKASI - NOTIFIKASI HASIL VERIFIKASI
     * ========================================================================
     * Mengirim email ke pelapor tentang hasil verifikasi laporan
     * (disetujui atau ditolak)
     * 
     * @param array $laporan - Data laporan
     * @param int $status - 1 = disetujui, 2 = ditolak
     * @param string|null $reason - Alasan penolakan (jika ditolak)
     */
    private function sendVerificationEmail($laporan, $status, $reason = null)
    {
        // Ambil service email dari CodeIgniter
        $email = \Config\Services::email();

        // Set pengirim email
        $email->setFrom('admin@fasilitas.com', 'Admin Pelaporan Fasilitas');
        
        // Set penerima (email pelapor)
        $email->setTo($laporan['email_pelapor']);
        
        // Buat isi email berdasarkan status verifikasi
        if ($status == 1) {
            // ============================================================
            // EMAIL UNTUK LAPORAN DISETUJUI
            // ============================================================
            $email->setSubject('Laporan Anda Disetujui');
            $message = "
                <h2>Laporan Disetujui</h2>
                <p>Laporan Anda telah diverifikasi dan disetujui oleh admin.</p>
                <ul>
                    <li><strong>ID Laporan:</strong> {$laporan['id']}</li>
                    <li><strong>Jenis Kerusakan:</strong> {$laporan['jenis_kerusakan']}</li>
                    <li><strong>Deskripsi:</strong> {$laporan['deskripsi']}</li>
                </ul>
                <p>Laporan Anda sekarang dapat dilihat oleh publik di peta.</p>
            ";
        } else {
            // ============================================================
            // EMAIL UNTUK LAPORAN DITOLAK
            // ============================================================
            $email->setSubject('Laporan Anda Ditolak');
            $message = "
                <h2>Laporan Ditolak</h2>
                <p>Mohon maaf, laporan Anda tidak dapat disetujui.</p>
                <ul>
                    <li><strong>ID Laporan:</strong> {$laporan['id']}</li>
                    <li><strong>Jenis Kerusakan:</strong> {$laporan['jenis_kerusakan']}</li>
                    <li><strong>Alasan:</strong> {$reason}</li>
                </ul>
                <p>Anda dapat mengirimkan laporan baru dengan informasi yang lebih lengkap.</p>
            ";
        }

        // Set isi pesan dan kirim
        $email->setMessage($message);
        $email->send();
    }

    /**
     * ========================================================================
     * FUNGSI KIRIM EMAIL NOTIFIKASI - UPDATE STATUS LAPORAN
     * ========================================================================
     * Mengirim email ke pelapor saat status laporan diperbarui
     * (misalnya: dari "Baru" ke "Diproses" atau "Dijadwalkan")
     * 
     * @param array $laporan - Data laporan lengkap
     */
    private function sendEmailNotifikasi($laporan)
    {
        // Ambil service email
        $email = \Config\Services::email();

        // Set pengirim dan penerima
        $email->setFrom('admin@fasilitas.com', 'Admin Pelaporan Fasilitas');
        $email->setTo($laporan['email_pelapor']);
        $email->setSubject('Update Status Laporan Fasilitas');

        // Buat isi email dengan informasi update
        $message = "
            <h2>Update Status Laporan Fasilitas</h2>
            <p>Status laporan Anda telah diperbarui:</p>
            <ul>
                <li><strong>ID Laporan:</strong> {$laporan['id']}</li>
                <li><strong>Jenis Kerusakan:</strong> {$laporan['jenis_kerusakan']}</li>
                <li><strong>Status:</strong> {$laporan['status']}</li>
        ";

        // Tambahkan tanggal perbaikan jika ada
        if ($laporan['tgl_perbaikan_dijadwalkan']) {
            $message .= "<li><strong>Tanggal Perbaikan Dijadwalkan:</strong> {$laporan['tgl_perbaikan_dijadwalkan']}</li>";
        }

        // Tambahkan catatan admin jika ada
        if ($laporan['catatan_admin']) {
            $message .= "<li><strong>Catatan:</strong> {$laporan['catatan_admin']}</li>";
        }

        $message .= "
            </ul>
            <p>Terima kasih atas laporan Anda.</p>
        ";

        // Set pesan dan kirim email
        $email->setMessage($message);

        // Kirim email dan log hasilnya
        if ($email->send()) {
            // Email berhasil terkirim
            log_message('info', 'Email notifikasi sent to ' . $laporan['email_pelapor']);
        } else {
            // Email gagal, catat error ke log
            log_message('error', $email->printDebugger(['headers']));
        }
    }

    /**
     * ========================================================================
     * FUNGSI EXPORT PDF SIMPLE - EXPORT DATA KE PDF (VERSI SEDERHANA)
     * ========================================================================
     * Mengekspor data laporan ke file PDF dengan format tabel sederhana
     * Dibatasi 10 data terakhir
     * 
     * URL: /admin/export/simple
     * Method: GET
     * Library: TCPDF
     */
    public function exportPDFSimple()
    {
        // Cek session admin
        if (!session()->get('admin_id')) {
            return $this->response->setJSON(['error' => 'Not authenticated']);
        }

        try {
            // ============================================================
            // AMBIL DATA DARI DATABASE
            // ============================================================
            // Menggunakan Query Builder untuk select kolom spesifik
            $builder = $this->laporanModel->builder();
            $laporan = $builder->select('id, email_pelapor, jenis_kerusakan, status, deskripsi, tgl_lapor, latitude, longitude')
                              ->orderBy('tgl_lapor', 'DESC') // Terbaru dulu
                              ->limit(10) // Batasi 10 data
                              ->get()
                              ->getResultArray();
            
            // Jika tidak ada data, lempar exception
            if (empty($laporan)) {
                throw new \Exception('No data found');
            }
            
            // ============================================================
            // LOAD LIBRARY TCPDF
            // ============================================================
            // TCPDF adalah library PHP untuk membuat file PDF
            require_once ROOTPATH . 'vendor/tecnickcom/tcpdf/tcpdf.php';
            
            // ============================================================
            // BUAT DOKUMEN PDF
            // ============================================================
            // Parameter: orientasi, unit, ukuran kertas, unicode, encoding, diskcache
            $pdf = new \TCPDF('P', 'mm', 'A4', true, 'UTF-8', false);
            
            // Hilangkan header dan footer default
            $pdf->setPrintHeader(false);
            $pdf->setPrintFooter(false);
            
            // Set margin (kiri, atas, kanan)
            $pdf->SetMargins(15, 15, 15);
            
            // Tambah halaman baru
            $pdf->AddPage();
            
            // ============================================================
            // BUAT KONTEN PDF
            // ============================================================
            $html = '<h1 style="color: #333;">Laporan Kerusakan Fasilitas</h1>';
            $html .= '<p>Total Data: ' . count($laporan) . '</p>';
            $html .= '<p>Generated: ' . date('d/m/Y H:i:s') . '</p>';
            
            // Buat tabel HTML
            $html .= '<table border="1" cellpadding="4" style="width: 100%;">';
            $html .= '<tr style="background-color: #f5f5f5;">';
            $html .= '<th width="8%">ID</th>';
            $html .= '<th width="25%">Email Pelapor</th>';
            $html .= '<th width="25%">Jenis Kerusakan</th>';
            $html .= '<th width="12%">Status</th>';
            $html .= '<th width="15%">Tanggal</th>';
            $html .= '<th width="15%">Koordinat</th>';
            $html .= '</tr>';
            
            // Loop setiap data laporan
            foreach ($laporan as $item) {
                // Ambil data dengan pengecekan keamanan
                $email = isset($item['email_pelapor']) ? htmlspecialchars($item['email_pelapor']) : 'N/A';
                $jenis = isset($item['jenis_kerusakan']) ? htmlspecialchars($item['jenis_kerusakan']) : 'N/A';
                $status = isset($item['status']) ? htmlspecialchars($item['status']) : 'N/A';
                $tanggal = isset($item['tgl_lapor']) ? date('d/m/Y', strtotime($item['tgl_lapor'])) : 'N/A';
                
                // Format koordinat
                $koordinat = '';
                if (isset($item['latitude']) && isset($item['longitude'])) {
                    $koordinat = number_format($item['latitude'], 4) . ', ' . number_format($item['longitude'], 4);
                } else {
                    $koordinat = 'N/A';
                }
                
                // Tambahkan baris ke tabel
                $html .= '<tr>';
                $html .= '<td>' . ($item['id'] ?? 'N/A') . '</td>';
                $html .= '<td>' . $email . '</td>';
                $html .= '<td>' . $jenis . '</td>';
                $html .= '<td>' . $status . '</td>';
                $html .= '<td>' . $tanggal . '</td>';
                $html .= '<td>' . $koordinat . '</td>';
                $html .= '</tr>';
            }
            
            $html .= '</table>';
            
            // Tulis HTML ke PDF
            $pdf->writeHTML($html, true, false, true, false, '');
            
            // ============================================================
            // OUTPUT PDF
            // ============================================================
            // Generate nama file dengan timestamp
            $filename = 'Laporan_Simple_' . date('Ymd_His') . '.pdf';
            
            // Bersihkan output buffer untuk menghindari error
            if (ob_get_level()) {
                ob_end_clean();
            }
            
            // Output PDF untuk download ('D' = download)
            $pdf->Output($filename, 'D');
            exit();
            
        } catch (\Exception $e) {
            // Return error dalam format JSON
            return $this->response->setJSON([
                'error' => 'PDF generation failed: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * ========================================================================
     * FUNGSI EXPORT PDF - EXPORT DATA KE PDF DENGAN FILTER
     * ========================================================================
     * Mengekspor data laporan ke file PDF dengan fitur filter:
     * - Filter berdasarkan status
     * - Filter berdasarkan jenis kerusakan
     * - Filter berdasarkan bulan dan tahun
     * 
     * URL: /admin/export/pdf?status=&jenis=&bulan=&tahun=
     * Method: GET
     * Library: TCPDF
     */
    public function exportPDF()
    {
        // Cek session admin
        if (!session()->get('admin_id')) {
            return redirect()->to(base_url('login'));
        }

        try {
            // ============================================================
            // AMBIL PARAMETER FILTER DARI URL
            // ============================================================
            $status = $this->request->getGet('status') ?? '';    // Filter status
            $jenis = $this->request->getGet('jenis') ?? '';      // Filter jenis kerusakan
            $bulan = $this->request->getGet('bulan') ?? '';      // Filter bulan
            $tahun = $this->request->getGet('tahun') ?? date('Y'); // Filter tahun (default: tahun ini)

            // ============================================================
            // BUILD QUERY DENGAN FILTER
            // ============================================================
            $builder = $this->laporanModel->builder();
            
            // Filter berdasarkan status (jika ada)
            if (!empty($status)) {
                $builder->where('status', $status);
            }
            
            // Filter berdasarkan jenis kerusakan (jika ada)
            if (!empty($jenis)) {
                $builder->where('jenis_kerusakan', $jenis);
            }
            
            // Filter berdasarkan bulan dan tahun
            if (!empty($bulan) && !empty($tahun)) {
                $builder->where('MONTH(tgl_lapor)', $bulan);
                $builder->where('YEAR(tgl_lapor)', $tahun);
            } elseif (!empty($tahun)) {
                $builder->where('YEAR(tgl_lapor)', $tahun);
            }

            // Eksekusi query
            $laporan = $builder->orderBy('tgl_lapor', 'DESC')->get()->getResultArray();

            // ============================================================
            // LOAD LIBRARY TCPDF
            // ============================================================
            $tcpdfPath = ROOTPATH . 'vendor/tecnickcom/tcpdf/tcpdf.php';
            if (!file_exists($tcpdfPath)) {
                throw new \Exception('TCPDF not found at: ' . $tcpdfPath);
            }

            require_once $tcpdfPath;

            // ============================================================
            // BUAT DOKUMEN PDF
            // ============================================================
            $pdf = new \TCPDF('P', 'mm', 'A4', true, 'UTF-8', false);

            // Set informasi dokumen
            $pdf->SetCreator('WebGIS Fasilitas Umum');
            $pdf->SetAuthor('Admin');
            $pdf->SetTitle('Laporan Kerusakan Fasilitas Umum');

            // Hilangkan header dan footer default
            $pdf->setPrintHeader(false);
            $pdf->setPrintFooter(false);

            // Set margin dan auto page break
            $pdf->SetMargins(15, 15, 15);
            $pdf->SetAutoPageBreak(TRUE, 15);

            // Set skala gambar
            $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

            // Aktifkan font subsetting untuk file size lebih kecil
            $pdf->setFontSubsetting(true);

            // Tambah halaman
            $pdf->AddPage();

            // ============================================================
            // GENERATE KONTEN PDF
            // ============================================================
            $html = $this->generatePDFContent($laporan, $status, $jenis, $bulan, $tahun);

            // Tulis HTML ke PDF
            $pdf->writeHTML($html, true, false, true, false, '');

            // ============================================================
            // OUTPUT PDF
            // ============================================================
            // Generate nama file dengan filter info
            $filterSuffix = '';
            if (!empty($status)) $filterSuffix .= '_' . $status;
            if (!empty($jenis)) $filterSuffix .= '_' . str_replace(' ', '', $jenis);
            if (!empty($bulan) && !empty($tahun)) $filterSuffix .= '_' . $bulan . '_' . $tahun;
            elseif (!empty($tahun)) $filterSuffix .= '_' . $tahun;
            
            $filename = 'Laporan_Fasilitas' . $filterSuffix . '_' . date('Ymd_His') . '.pdf';

            // Bersihkan output buffer
            if (ob_get_level()) {
                ob_end_clean();
            }

            // Output PDF untuk download
            $pdf->Output($filename, 'D'); // D = download
            exit();
        
        } catch (\Exception $e) {
            // Log error
            log_message('error', 'PDF Export Error: ' . $e->getMessage());
            
            // Return error response
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Error generating PDF: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * ========================================================================
     * FUNGSI GENERATE PDF CONTENT - MEMBUAT ISI PDF DENGAN LAYOUT CANTIK
     * ========================================================================
     * Fungsi helper untuk membuat konten HTML yang akan dirender ke PDF
     * Termasuk: statistik, tabel data, dan styling
     * 
     * @param array $laporan - Array data laporan
     * @param string $status - Filter status
     * @param string $jenis - Filter jenis kerusakan
     * @param string $bulan - Filter bulan
     * @param string $tahun - Filter tahun
     * @return string - HTML content untuk PDF
     */
    private function generatePDFContent($laporan, $status, $jenis, $bulan, $tahun)
    {
        $total = count($laporan);
        $filterDesc = $this->getFilterDescription($status, $jenis, $bulan, $tahun);

        // ================================================================
        // HITUNG STATISTIK BERDASARKAN STATUS
        // ================================================================
        $stats = [
            'Baru' => 0,
            'Diproses' => 0, 
            'Dijadwalkan' => 0,
            'Selesai' => 0
        ];
        
        // ================================================================
        // HITUNG STATISTIK BERDASARKAN PRIORITAS
        // ================================================================
        $prioritasStats = [
            'tinggi' => 0,
            'sedang' => 0,
            'rendah' => 0
        ];

        // Loop data untuk menghitung statistik
        foreach ($laporan as $item) {
            // Hitung per status
            if (isset($stats[$item['status']])) {
                $stats[$item['status']]++;
            }
            // Hitung per prioritas
            $prioritas = $item['prioritas'] ?? 'sedang';
            if (isset($prioritasStats[$prioritas])) {
                $prioritasStats[$prioritas]++;
            }
        }

        // ================================================================
        // BUILD HTML CONTENT
        // ================================================================
        $html = '
        <style>
            .header { text-align: center; margin-bottom: 20px; }
            .summary { background: #f8f9fa; padding: 15px; border: 1px solid #dee2e6; margin-bottom: 20px; }
            .stats-table { width: 100%; margin-bottom: 20px; }
            .stats-table td { padding: 8px; border: 1px solid #ddd; }
            .stats-header { background: #343a40; color: white; font-weight: bold; }
            .report-table { width: 100%; font-size: 10px; }
            .report-table th { background: #495057; color: white; padding: 8px; font-weight: bold; }
            .report-table td { padding: 6px; border: 1px solid #ddd; vertical-align: top; }
            .status-baru { background: #6c757d; color: white; padding: 2px 6px; border-radius: 3px; font-size: 8px; }
            .status-diproses { background: #dc3545; color: white; padding: 2px 6px; border-radius: 3px; font-size: 8px; }
            .status-dijadwalkan { background: #ffc107; color: black; padding: 2px 6px; border-radius: 3px; font-size: 8px; }
            .status-selesai { background: #28a745; color: white; padding: 2px 6px; border-radius: 3px; font-size: 8px; }
        </style>

        <div class="header">
            <h2>LAPORAN KERUSAKAN FASILITAS UMUM</h2>
            <p><strong>' . $filterDesc . '</strong></p>
            <p>Dicetak pada: ' . date('d F Y H:i:s') . '</p>
        </div>

        <div class="summary">
            <h3>📊 RINGKASAN STATISTIK</h3>
            <table class="stats-table" cellpadding="5" cellspacing="0">
                <tr class="stats-header">
                    <td width="20%">Total Laporan</td>
                    <td width="20%">Baru</td>
                    <td width="20%">Diproses</td>
                    <td width="20%">Dijadwalkan</td>
                    <td width="20%">Selesai</td>
                </tr>
                <tr>
                    <td style="text-align: center; font-size: 14px; font-weight: bold;">' . $total . '</td>
                    <td style="text-align: center;">' . $stats['Baru'] . '</td>
                    <td style="text-align: center;">' . $stats['Diproses'] . '</td>
                    <td style="text-align: center;">' . $stats['Dijadwalkan'] . '</td>
                    <td style="text-align: center;">' . $stats['Selesai'] . '</td>
                </tr>
            </table>
            
            <h4>📌 STATISTIK PRIORITAS</h4>
            <table class="stats-table" cellpadding="5" cellspacing="0">
                <tr class="stats-header">
                    <td width="33%">🔴 Prioritas Tinggi</td>
                    <td width="34%">🟡 Prioritas Sedang</td>
                    <td width="33%">🟢 Prioritas Rendah</td>
                </tr>
                <tr>
                    <td style="text-align: center; background: #ffebee;">' . $prioritasStats['tinggi'] . '</td>
                    <td style="text-align: center; background: #fff8e1;">' . $prioritasStats['sedang'] . '</td>
                    <td style="text-align: center; background: #e8f5e9;">' . $prioritasStats['rendah'] . '</td>
                </tr>
            </table>
        </div>

        <h3>📋 DETAIL LAPORAN</h3>
        <table class="report-table" cellpadding="0" cellspacing="0">
            <thead>
                <tr>
                    <th width="4%">No</th>
                    <th width="10%">Tanggal</th>
                    <th width="18%">Email Pelapor</th>
                    <th width="12%">Koordinat</th>
                    <th width="13%">Jenis Kerusakan</th>
                    <th width="15%">Deskripsi</th>
                    <th width="10%">Prioritas</th>
                    <th width="10%">Status</th>
                    <th width="8%">Foto</th>
                </tr>
            </thead>
            <tbody>';

        // ================================================================
        // LOOP SETIAP DATA LAPORAN UNTUK TABEL
        // ================================================================
        $no = 1;
        foreach ($laporan as $item) {
            // Tentukan class CSS berdasarkan status
            $statusClass = 'status-' . strtolower($item['status']);
            
            // Format tanggal
            $tanggal = date('d/m/Y', strtotime($item['tgl_lapor']));
            
            // Format koordinat
            $koordinat = '';
            if (!empty($item['latitude']) && !empty($item['longitude'])) {
                $koordinat = number_format($item['latitude'], 6) . ', ' . number_format($item['longitude'], 6);
            } else {
                $koordinat = 'N/A';
            }
            
            // ============================================================
            // PROSES GAMBAR UNTUK PDF
            // ============================================================
            $imageHtml = '';
            
            if (!empty($item['foto_lokasi'])) {
                // Path ke file gambar
                $imagePath = ROOTPATH . 'public/uploads/' . $item['foto_lokasi'];
                
                if (file_exists($imagePath)) {
                    // Encode gambar ke base64 untuk embed di HTML
                    $imageData = base64_encode(file_get_contents($imagePath));
                    $imageType = pathinfo($imagePath, PATHINFO_EXTENSION);
                    
                    // Tentukan MIME type yang benar
                    switch(strtolower($imageType)) {
                        case 'jpg':
                        case 'jpeg':
                            $mimeType = 'jpeg';
                            break;
                        case 'png':
                            $mimeType = 'png';
                            break;
                        case 'gif':
                            $mimeType = 'gif';
                            break;
                        case 'webp':
                            $mimeType = 'webp';
                            break;
                        default:
                            $mimeType = 'jpeg';
                    }
                    // Embed gambar sebagai data URI
                    $imageHtml = '<img src="data:image/' . $mimeType . ';base64,' . $imageData . '" style="max-width: 80px; max-height: 80px; border: 1px solid #ddd;">';
                } else {
                    $imageHtml = '<span style="color: #999; font-size: 8px;">File Not Found</span>';
                }
            } else {
                $imageHtml = '<span style="color: #999; font-size: 8px;">No Image</span>';
            }

            // Format prioritas dengan emoji
            $prioritas = $item['prioritas'] ?? 'sedang';
            $prioritasLabel = ucfirst($prioritas);
            $prioritasIcon = ['tinggi' => '🔴', 'sedang' => '🟡', 'rendah' => '🟢'];
            $prioritasDisplay = ($prioritasIcon[$prioritas] ?? '🟡') . ' ' . $prioritasLabel;
            
            // Tambahkan baris ke tabel
            $html .= '
                <tr>
                    <td style="text-align: center;">' . $no . '</td>
                    <td>' . $tanggal . '</td>
                    <td>' . htmlspecialchars($item['email_pelapor']) . '</td>
                    <td style="font-size: 8px;">' . $koordinat . '</td>
                    <td>' . htmlspecialchars($item['jenis_kerusakan']) . '</td>
                    <td>' . htmlspecialchars(substr($item['deskripsi'], 0, 80)) . (strlen($item['deskripsi']) > 80 ? '...' : '') . '</td>
                    <td style="text-align: center;">' . $prioritasDisplay . '</td>
                    <td><span class="' . $statusClass . '">' . $item['status'] . '</span></td>
                    <td style="text-align: center;">' . $imageHtml . '</td>
                </tr>';
            $no++;
        }

        $html .= '</tbody></table>';

        return $html;
    }

    /**
     * ========================================================================
     * FUNGSI GET FILTER DESCRIPTION - DESKRIPSI FILTER UNTUK DISPLAY
     * ========================================================================
     * Membuat teks deskripsi filter yang diterapkan pada data
     * 
     * @param string $status - Filter status
     * @param string $jenis - Filter jenis
     * @param string $bulan - Filter bulan
     * @param string $tahun - Filter tahun
     * @return string - Deskripsi filter
     */
    private function getFilterDescription($status, $jenis, $bulan, $tahun)
    {
        $desc = [];
        
        // Tambahkan deskripsi status jika ada
        if (!empty($status)) {
            $desc[] = "Status: $status";
        }
        
        // Tambahkan deskripsi jenis jika ada
        if (!empty($jenis)) {
            $desc[] = "Jenis: $jenis";
        }
        
        // Tambahkan deskripsi periode jika ada
        if (!empty($bulan) && !empty($tahun)) {
            // Array nama bulan dalam Bahasa Indonesia
            $bulanName = [
                '1' => 'Januari', '2' => 'Februari', '3' => 'Maret', '4' => 'April',
                '5' => 'Mei', '6' => 'Juni', '7' => 'Juli', '8' => 'Agustus',
                '9' => 'September', '10' => 'Oktober', '11' => 'November', '12' => 'Desember'
            ];
            $desc[] = "Periode: " . $bulanName[$bulan] . " $tahun";
        } elseif (!empty($tahun)) {
            $desc[] = "Tahun: $tahun";
        }
        
        // Gabungkan dengan separator |
        return !empty($desc) ? implode(' | ', $desc) : 'Semua Data';
    }

    /**
     * ========================================================================
     * FUNGSI EXPORT PDF WITH IMAGES - EXPORT DENGAN GAMBAR BESAR
     * ========================================================================
     * Mengekspor data laporan ke PDF dengan gambar ukuran besar
     * Format: 2 laporan per halaman dengan detail lengkap
     * Termasuk cover page dengan logo
     * 
     * URL: /admin/export/images
     * Method: GET
     * Library: TCPDF
     */
    public function exportPDFWithImages()
    {
        // Cek session admin
        if (!session()->get('admin_id')) {
            return redirect()->to(base_url('login'));
        }

        try {
            // ============================================================
            // AMBIL PARAMETER FILTER
            // ============================================================
            $status = $this->request->getGet('status') ?? '';
            $jenis = $this->request->getGet('jenis') ?? '';
            $bulan = $this->request->getGet('bulan') ?? '';
            $tahun = $this->request->getGet('tahun') ?? date('Y');

            // ============================================================
            // BUILD QUERY DENGAN FILTER
            // ============================================================
            $builder = $this->laporanModel->builder();
            
            if (!empty($status)) {
                $builder->where('status', $status);
            }
            
            if (!empty($jenis)) {
                $builder->where('jenis_kerusakan', $jenis);
            }
            
            if (!empty($bulan) && !empty($tahun)) {
                $builder->where('MONTH(tgl_lapor)', $bulan);
                $builder->where('YEAR(tgl_lapor)', $tahun);
            } elseif (!empty($tahun)) {
                $builder->where('YEAR(tgl_lapor)', $tahun);
            }

            $laporan = $builder->orderBy('tgl_lapor', 'DESC')->get()->getResultArray();

            // ============================================================
            // LOAD LIBRARY TCPDF
            // ============================================================
            require_once ROOTPATH . 'vendor/tecnickcom/tcpdf/tcpdf.php';

            // Buat dokumen PDF baru
            $pdf = new \TCPDF('P', 'mm', 'A4', true, 'UTF-8', false);

            // Set informasi dokumen
            $pdf->SetCreator('WebGIS Fasilitas Umum');
            $pdf->SetAuthor('Admin');
            $pdf->SetTitle('Laporan Kerusakan Fasilitas Umum');

            // Hilangkan header dan footer default
            $pdf->setPrintHeader(false);
            $pdf->setPrintFooter(false);

            // Set margin dan auto page break
            $pdf->SetMargins(15, 15, 15);
            $pdf->SetAutoPageBreak(TRUE, 15);

            // Set skala gambar
            $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

            // ============================================================
            // HALAMAN COVER
            // ============================================================
            $pdf->AddPage();
            
            // Tambahkan logo jika ada
            $logoPath = ROOTPATH . 'public/image/logo-diskominfo.png';
            if (file_exists($logoPath)) {
                // Posisikan logo di tengah (stretched)
                $pdf->Image($logoPath, 45, 25, 120, 60);
                $pdf->Ln(75); // Jarak setelah logo
            } else {
                // Placeholder jika logo tidak ada
                $pdf->SetFont('times', 'B', 12);
                $pdf->Cell(0, 15, '🏛️ LOGO DISKOMINFO', 0, 1, 'C');
                $pdf->Cell(0, 10, 'KOTA BANDUNG', 0, 1, 'C');
                $pdf->Ln(20);
            }

            // Judul utama
            $pdf->SetFont('times', 'B', 20);
            $pdf->Cell(0, 15, 'Pelaporan Fasilitas umum dan jalan', 0, 1, 'C');
            $pdf->Cell(0, 15, 'Di Kota Bandung', 0, 1, 'C');
            $pdf->Ln(10);
            
            // Subjudul instansi
            $pdf->SetFont('times', 'B', 14);
            $pdf->Cell(0, 10, 'DINAS KOMUNIKASI DAN INFORMATIKA', 0, 1, 'C');
            $pdf->Cell(0, 10, 'KOTA BANDUNG', 0, 1, 'C');
            $pdf->Ln(20);

            // Info filter
            $filterDesc = $this->getFilterDescription($status, $jenis, $bulan, $tahun);
            $pdf->SetFont('times', 'B', 12);
            $pdf->Cell(0, 8, 'FILTER DATA:', 0, 1, 'C');
            $pdf->SetFont('times', '', 10);
            $pdf->Cell(0, 8, $filterDesc, 0, 1, 'C');
            $pdf->Ln(15);
            
            // Tanggal cetak dan statistik
            $pdf->SetFont('times', '', 10);
            $pdf->Cell(0, 8, 'Dicetak pada: ' . date('d F Y H:i:s'), 0, 1, 'C');
            $pdf->Cell(0, 8, 'Total Laporan: ' . count($laporan) . ' data', 0, 1, 'C');
            $pdf->Ln(30);
            
            // Footer cover
            $pdf->SetFont('times', 'I', 9);
            $pdf->Cell(0, 8, 'Sistem WebGIS Pelaporan Fasilitas Umum', 0, 1, 'C');
            $pdf->Cell(0, 8, 'Dinas Komunikasi dan Informatika Kota Bandung', 0, 1, 'C');

            // ============================================================
            // HALAMAN DETAIL LAPORAN (2 laporan per halaman)
            // ============================================================
            foreach ($laporan as $index => $item) {
                // Tambah halaman baru setiap 2 laporan
                if ($index % 2 == 0) {
                    $pdf->AddPage();
                }

                // Header laporan
                $pdf->SetFont('times', 'B', 14);
                $pdf->Cell(0, 10, 'Laporan #' . ($index + 1), 0, 1, 'L');
                $pdf->Ln(3);

                // Detail laporan dalam 2 kolom
                $pdf->SetFont('times', '', 10);
                $leftColumn = 95;
                $rightColumn = 95;
                
                // Baris 1: Tanggal dan Status
                $pdf->Cell($leftColumn, 8, 'Tanggal: ' . date('d/m/Y', strtotime($item['tgl_lapor'])), 1, 0, 'L');
                $pdf->Cell($rightColumn, 8, 'Status: ' . $item['status'], 1, 1, 'L');
                
                // Baris 2: Email dan Prioritas
                $pdf->Cell($leftColumn, 8, 'Email: ' . $item['email_pelapor'], 1, 0, 'L');
                
                $prioritas = $item['prioritas'] ?? 'sedang';
                $prioritasLabel = ucfirst($prioritas);
                $prioritasIcon = ['tinggi' => '🔴', 'sedang' => '🟡', 'rendah' => '🟢'];
                $pdf->Cell($rightColumn, 8, 'Prioritas: ' . ($prioritasIcon[$prioritas] ?? '🟡') . ' ' . $prioritasLabel, 1, 1, 'L');
                
                // Baris 3: Koordinat dan Jenis Kerusakan
                $koordinat = !empty($item['latitude']) && !empty($item['longitude']) ? 
                    number_format($item['latitude'], 6) . ', ' . number_format($item['longitude'], 6) : 'N/A';
                $pdf->Cell($leftColumn, 8, 'Koordinat: ' . $koordinat, 1, 0, 'L');
                $pdf->Cell($rightColumn, 8, 'Jenis: ' . $item['jenis_kerusakan'], 1, 1, 'L');
                
                // Deskripsi (multi-line)
                $pdf->Cell($leftColumn + $rightColumn, 6, 'Deskripsi:', 1, 1, 'L');
                $pdf->SetFont('times', '', 9);
                $pdf->MultiCell($leftColumn + $rightColumn, 5, $item['deskripsi'], 1, 'L', false, 1);
                
                // ============================================================
                // GAMBAR LAPORAN (UKURAN BESAR)
                // ============================================================
                if (!empty($item['foto_lokasi'])) {
                    $imagePath = ROOTPATH . 'public/uploads/' . $item['foto_lokasi'];
                    
                    if (file_exists($imagePath)) {
                        $pdf->Ln(5);
                        $pdf->SetFont('times', 'B', 10);
                        $pdf->Cell(0, 8, 'Foto Lokasi:', 0, 1, 'L');
                        
                        // Ambil dimensi gambar
                        $imageInfo = getimagesize($imagePath);
                        if ($imageInfo) {
                            // Ukuran optimal untuk 2 laporan per halaman
                            $maxWidth = 140;
                            $maxHeight = 100;
                            
                            $originalWidth = $imageInfo[0];
                            $originalHeight = $imageInfo[1];
                            
                            // Hitung skala
                            $scaleW = $maxWidth / $originalWidth;
                            $scaleH = $maxHeight / $originalHeight;
                            $scale = min($scaleW, $scaleH);
                            
                            $newWidth = $originalWidth * $scale * 0.35;
                            $newHeight = $originalHeight * $scale * 0.35;
                            
                            // Tengahkan gambar di halaman
                            $imageX = (210 - $newWidth) / 2; // A4 width = 210mm
                            
                            // Tambahkan gambar ke PDF
                            $pdf->Image($imagePath, $imageX, $pdf->GetY(), $newWidth, $newHeight, '', '', '', false, 300, '', false, false, 1, false, false, false);
                            $pdf->Ln($newHeight + 10);
                        }
                    } else {
                        $pdf->SetFont('times', 'I', 9);
                        $pdf->Cell(0, 8, 'Foto tidak ditemukan: ' . $item['foto_lokasi'], 0, 1, 'L');
                        $pdf->Ln(5);
                    }
                } else {
                    $pdf->SetFont('times', 'I', 9);
                    $pdf->Cell(0, 8, 'Tidak ada foto', 0, 1, 'L');
                    $pdf->Ln(5);
                }

                // Garis pemisah antar laporan di halaman yang sama
                if ($index % 2 == 0 && isset($laporan[$index + 1])) {
                    $pdf->Ln(5);
                    $pdf->Line(15, $pdf->GetY(), 195, $pdf->GetY());
                    $pdf->Ln(10);
                }
            }

            // ============================================================
            // OUTPUT PDF
            // ============================================================
            // Generate nama file
            $filterSuffix = '';
            if (!empty($status)) $filterSuffix .= '_' . $status;
            if (!empty($jenis)) $filterSuffix .= '_' . str_replace(' ', '', $jenis);
            if (!empty($bulan) && !empty($tahun)) $filterSuffix .= '_' . $bulan . '_' . $tahun;
            elseif (!empty($tahun)) $filterSuffix .= '_' . $tahun;
            
            $filename = 'Laporan_Fasilitas_Images' . $filterSuffix . '_' . date('Ymd_His') . '.pdf';

            // Bersihkan output buffer
            if (ob_get_level()) {
                ob_end_clean();
            }

            // Output PDF untuk download
            $pdf->Output($filename, 'D');
            exit();
        
        } catch (\Exception $e) {
            // Log error
            log_message('error', 'PDF Export Error: ' . $e->getMessage());
            
            // Return error response
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Error generating PDF: ' . $e->getMessage()
            ]);
        }
    }
}
