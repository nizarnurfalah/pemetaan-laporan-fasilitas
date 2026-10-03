<?php
/**
 * ============================================================================
 * CONTROLLER LAPORAN - UNTUK MENANGANI SEMUA FUNGSI PELAPORAN FASILITAS
 * ============================================================================
 * 
 * File ini adalah controller utama yang menangani:
 * 1. Menampilkan peta WebGIS dengan semua laporan
 * 2. Form tambah laporan untuk pengguna (guest)
 * 3. Menyimpan laporan baru ke database
 * 4. Menampilkan detail laporan
 * 5. Mengedit laporan dengan verifikasi email
 * 6. Fitur "Laporan Saya" untuk melihat laporan berdasarkan email
 * 7. Mengirim notifikasi email ke pengguna
 * 
 * Framework: CodeIgniter 4
 * Author: [Nama Anda]
 * ============================================================================
 */

// Namespace untuk menentukan lokasi file dalam struktur MVC
namespace App\Controllers;

// Import model LaporanModel untuk berinteraksi dengan tabel laporan di database
use App\Models\LaporanModel;

/**
 * Class Laporan
 * Controller untuk menangani semua operasi terkait laporan fasilitas
 * Extends BaseController untuk mendapatkan fungsi dasar CodeIgniter
 */
class Laporan extends BaseController
{
    // Variabel untuk menyimpan instance model laporan
    // Protected artinya hanya bisa diakses oleh class ini dan turunannya
    protected $laporanModel;
    
    // Variabel untuk menyimpan instance service email
    protected $email;

    /**
     * Constructor - Dijalankan pertama kali saat controller diakses
     * Fungsi: Menginisialisasi model dan service yang dibutuhkan
     */
    public function __construct()
    {
        // Membuat instance baru dari LaporanModel untuk operasi database
        $this->laporanModel = new LaporanModel();
        
        // Mengambil service email dari CodeIgniter untuk mengirim notifikasi
        $this->email = \Config\Services::email();
    }

    /**
     * ========================================================================
     * FUNGSI KIRIM EMAIL NOTIFIKASI
     * ========================================================================
     * Fungsi private (hanya bisa dipanggil dari dalam class ini)
     * untuk mengirim email notifikasi ke pengguna
     * 
     * @param string $to_email - Alamat email tujuan
     * @param string $subject - Subjek email
     * @param string $body - Isi pesan email
     * @return bool - True jika berhasil, false jika gagal
     */
    private function sendEmailNotification($to_email, $subject, $body)
    {
        try {
            // Set pengirim email (from)
            $this->email->setFrom('noreply@webgis-fasilitas.com', 'WebGIS Fasilitas Umum');
            
            // Set penerima email (to)
            $this->email->setTo($to_email);
            
            // Set subjek email
            $this->email->setSubject($subject);
            
            // Set isi pesan email
            $this->email->setMessage($body);
            
            // Kirim email dan kembalikan hasilnya (true/false)
            return $this->email->send();
        } catch (\Exception $e) {
            // Jika terjadi error, catat ke log file
            log_message('error', 'Email sending failed: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * ========================================================================
     * FUNGSI INDEX - HALAMAN UTAMA WEBGIS
     * ========================================================================
     * Fungsi ini menampilkan peta WebGIS dengan semua laporan yang ada
     * Ini adalah halaman utama website yang pertama kali dilihat pengunjung
     * 
     * URL: / atau /laporan
     * Method: GET
     * 
     * @return View - Menampilkan view v_webgis dengan data laporan
     */
    public function index()
    {
        // Ambil semua data laporan dari database menggunakan method findAll()
        $data['laporan'] = $this->laporanModel->findAll();
        
        // Set flag bahwa ini adalah halaman publik (bukan admin)
        $data['is_public_page'] = true;
        
        // Tampilkan view v_webgis dengan mengirimkan data laporan
        return view('laporan/v_webgis', $data);
    }

    /**
     * ========================================================================
     * FUNGSI TAMBAH - FORM TAMBAH LAPORAN BARU
     * ========================================================================
     * Fungsi ini menampilkan form untuk pengguna (guest) membuat laporan baru
     * Form ini berisi field: email, jenis kerusakan, deskripsi, foto, dan koordinat
     * 
     * URL: /laporan/tambah
     * Method: GET
     * 
     * @return View - Menampilkan form tambah laporan
     */
    public function tambah()
    {
        // Tampilkan view form tambah laporan
        return view('laporan/v_form_tambah');
    }

    /**
     * ========================================================================
     * FUNGSI SIMPAN - MENYIMPAN LAPORAN BARU KE DATABASE
     * ========================================================================
     * Fungsi ini memproses dan menyimpan data laporan baru yang dikirim dari form
     * Fitur keamanan yang diterapkan:
     * 1. Validasi reCAPTCHA untuk mencegah spam/bot
     * 2. Validasi file (hanya gambar, max 5MB)
     * 3. Validasi MIME type untuk keamanan file upload
     * 
     * URL: /laporan/simpan
     * Method: POST
     * 
     * @return Response - JSON untuk AJAX atau redirect untuk form biasa
     */
    public function simpan()
    {
        // Cek apakah request menggunakan method POST
        if ($this->request->getMethod() === 'post') {
            
            // ================================================================
            // CEK JENIS REQUEST (AJAX atau Form Biasa)
            // ================================================================
            // Mengecek apakah request berasal dari AJAX atau form biasa
            // Ini penting untuk menentukan format response (JSON atau redirect)
            $isAjax = $this->request->isAJAX() || 
                      $this->request->getHeaderLine('X-Requested-With') === 'XMLHttpRequest' ||
                      $this->request->getHeaderLine('Accept') === 'application/json';
            
            // ================================================================
            // VALIDASI reCAPTCHA - MENCEGAH SPAM DAN BOT
            // ================================================================
            // reCAPTCHA adalah layanan Google untuk memverifikasi bahwa
            // pengguna adalah manusia, bukan robot/bot
            $recaptchaResponse = $this->request->getPost('g-recaptcha-response');
            
            // Jika reCAPTCHA tidak diisi, kembalikan error
            if (empty($recaptchaResponse)) {
                if ($isAjax) {
                    return $this->response->setContentType('application/json')->setJSON(['success' => false, 'message' => 'Silakan centang kotak reCAPTCHA untuk verifikasi']);
                }
                return redirect()->back()->withInput()->with('error', 'Silakan centang kotak reCAPTCHA untuk verifikasi');
            }
            
            // ================================================================
            // VERIFIKASI reCAPTCHA KE SERVER GOOGLE
            // ================================================================
            // Secret key untuk verifikasi ke Google (ini adalah key testing)
            $secretKey = '6LeIxAcTAAAAAGG-vFI1TnRWxMZNFuojJ4WifJWe';
            $verifyURL = 'https://www.google.com/recaptcha/api/siteverify';
            
            // Kirim request ke Google untuk memverifikasi response reCAPTCHA
            $response = file_get_contents($verifyURL . '?secret=' . $secretKey . '&response=' . $recaptchaResponse);
            $responseData = json_decode($response);
            
            // Jika verifikasi gagal, kembalikan error
            if (!$responseData->success) {
                if ($isAjax) {
                    return $this->response->setContentType('application/json')->setJSON(['success' => false, 'message' => 'Verifikasi reCAPTCHA gagal. Silakan coba lagi.']);
                }
                return redirect()->back()->withInput()->with('error', 'Verifikasi reCAPTCHA gagal. Silakan coba lagi.');
            }
            
            // ================================================================
            // VALIDASI FILE UPLOAD
            // ================================================================
            // Ambil file yang diupload dari form
            $file = $this->request->getFile('foto_lokasi');

            // Cek apakah file valid (tidak corrupt atau error saat upload)
            if (!$file->isValid()) {
                if ($isAjax) {
                    return $this->response->setContentType('application/json')->setJSON(['success' => false, 'message' => 'File tidak valid']);
                }
                return redirect()->back()->with('error', 'File tidak valid');
            }

            // ================================================================
            // VALIDASI MIME TYPE - KEAMANAN FILE
            // ================================================================
            // MIME type menentukan jenis file yang sebenarnya
            // Ini mencegah user upload file berbahaya dengan ekstensi palsu
            $allowedMimes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
            if (!in_array($file->getMimeType(), $allowedMimes)) {
                if ($isAjax) {
                    return $this->response->setContentType('application/json')->setJSON(['success' => false, 'message' => 'File harus berupa gambar (JPG, PNG, GIF, atau WebP)']);
                }
                return redirect()->back()->with('error', 'File harus berupa gambar (JPG, PNG, GIF, atau WebP)');
            }

            // ================================================================
            // VALIDASI UKURAN FILE - MAKSIMAL 5MB
            // ================================================================
            $maxSize = 5 * 1024 * 1024; // 5MB dalam bytes (5 x 1024 KB x 1024 bytes)
            if ($file->getSize() > $maxSize) {
                if ($isAjax) {
                    return $this->response->setContentType('application/json')->setJSON(['success' => false, 'message' => 'Ukuran file maksimal 5MB']);
                }
                return redirect()->back()->with('error', 'Ukuran file maksimal 5MB');
            }

            // ================================================================
            // PINDAHKAN FILE KE FOLDER UPLOADS
            // ================================================================
            // Generate nama file acak untuk keamanan (mencegah overwrite)
            $nama_file = $file->getRandomName();
            // Pindahkan file ke folder public/uploads
            // FCPATH adalah konstanta untuk folder public/
            $file->move(FCPATH . 'uploads', $nama_file);

            // ================================================================
            // SIAPKAN DATA UNTUK DISIMPAN KE DATABASE
            // ================================================================
            $data = [
                'email_pelapor'   => $this->request->getPost('email_pelapor'),    // Email pelapor
                'jenis_kerusakan' => $this->request->getPost('jenis_kerusakan'),  // Jenis kerusakan
                'deskripsi'       => $this->request->getPost('deskripsi'),        // Deskripsi kerusakan
                'foto_lokasi'     => $nama_file,                                   // Nama file foto
                'latitude'        => $this->request->getPost('latitude'),         // Koordinat latitude
                'longitude'       => $this->request->getPost('longitude'),        // Koordinat longitude
                'status'          => 'Baru',                                       // Status awal = Baru
                'is_verified'     => 0,                                            // Belum diverifikasi admin
            ];

            // ================================================================
            // SIMPAN DATA KE DATABASE
            // ================================================================
            if ($this->laporanModel->save($data)) {
                // ============================================================
                // KIRIM EMAIL KONFIRMASI KE PENGGUNA
                // ============================================================
                // Ambil ID laporan yang baru disimpan
                $laporan_id = $this->laporanModel->getInsertID();
                
                // Buat subjek email
                $subject = '✓ Laporan Fasilitas Anda Telah Diterima';
                
                // Buat isi pesan email
                $body = "Halo,\n\n";
                $body .= "Terima kasih telah melaporkan fasilitas yang rusak. Laporan Anda telah kami terima dan akan segera kami tinjau.\n\n";
                $body .= "Detail Laporan:\n";
                $body .= "- ID Laporan: #" . $laporan_id . "\n";
                $body .= "- Jenis Kerusakan: " . $data['jenis_kerusakan'] . "\n";
                $body .= "- Status: " . $data['status'] . "\n";
                $body .= "- Tanggal Lapor: " . date('d/m/Y H:i') . "\n\n";
                $body .= "Laporan Anda akan ditampilkan di peta setelah diverifikasi oleh admin.\n\n";
                $body .= "Terima kasih,\nTim WebGIS Fasilitas Umum";
                
                // Kirim email notifikasi
                $this->sendEmailNotification($data['email_pelapor'], $subject, $body);
                
                // ============================================================
                // KEMBALIKAN RESPONSE BERDASARKAN JENIS REQUEST
                // ============================================================
                if ($isAjax) {
                    // Jika AJAX, kirim JSON response
                    return $this->response->setContentType('application/json')->setJSON([
                        'success' => true, 
                        'message' => 'Laporan berhasil dikirim! Laporan Anda sedang menunggu verifikasi admin.',
                        'laporan_id' => $laporan_id
                    ]);
                }
                // Jika form biasa, redirect dengan pesan sukses
                return redirect()->to(base_url('laporan/tambah'))->with('success', 'Data berhasil disimpan! Laporan Anda sedang menunggu verifikasi admin. Terima kasih telah melaporkan.');
            } else {
                // Jika gagal menyimpan, kembalikan error
                if ($isAjax) {
                    return $this->response->setContentType('application/json')->setJSON(['success' => false, 'message' => 'Gagal menyimpan laporan. Silakan coba lagi.']);
                }
                return redirect()->back()->withInput()->with('error', '❌ Gagal menyimpan laporan. Silakan coba lagi.');
            }
        }
    }

    /**
     * ========================================================================
     * FUNGSI DETAIL - MENAMPILKAN DETAIL LAPORAN
     * ========================================================================
     * Fungsi ini menampilkan halaman detail dari satu laporan tertentu
     * berdasarkan ID yang diberikan
     * 
     * URL: /laporan/detail/{id}
     * Method: GET
     * 
     * @param int $id - ID laporan yang ingin dilihat detailnya
     * @return View - Menampilkan view v_detail dengan data laporan
     * @throws PageNotFoundException - Jika laporan tidak ditemukan
     */
    public function detail($id)
    {
        // Cari laporan berdasarkan ID di database
        $laporan = $this->laporanModel->find($id);
        
        // Jika laporan tidak ditemukan, tampilkan halaman 404
        if (!$laporan) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Laporan tidak ditemukan');
        }

        // Kirim data laporan ke view
        $data['laporan'] = $laporan;
        
        // Cek apakah user adalah pelapor (sudah verifikasi email di session)
        // Ini untuk menentukan apakah user bisa edit laporan ini
        $data['is_reporter'] = session()->get('verified_email_' . $id) === $laporan['email_pelapor'];
        
        // Tampilkan view detail laporan
        return view('laporan/v_detail', $data);
    }

    /**
     * ========================================================================
     * FUNGSI EDIT - FORM EDIT LAPORAN
     * ========================================================================
     * Fungsi ini menampilkan form untuk mengedit laporan
     * User harus verifikasi email terlebih dahulu sebelum bisa edit
     * 
     * URL: /laporan/edit/{id}
     * Method: GET
     * 
     * @param int $id - ID laporan yang ingin diedit
     * @return View - Menampilkan form edit laporan
     * @throws PageNotFoundException - Jika laporan tidak ditemukan
     */
    public function edit($id)
    {
        // Cari laporan berdasarkan ID di database
        $laporan = $this->laporanModel->find($id);
        
        // Jika laporan tidak ditemukan, tampilkan halaman 404
        if (!$laporan) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Laporan tidak ditemukan');
        }

        // Kirim data laporan ke view
        $data['laporan'] = $laporan;
        
        // Cek apakah email sudah diverifikasi di session
        // show_form = true jika email di session sama dengan email pelapor
        $data['show_form'] = session()->get('verified_email_' . $id) === $laporan['email_pelapor'];
        
        // Tampilkan view form edit
        return view('laporan/v_form_edit', $data);
    }

    /**
     * ========================================================================
     * FUNGSI UPDATE - MEMPERBARUI DATA LAPORAN
     * ========================================================================
     * Fungsi ini memproses pembaruan data laporan yang dikirim dari form edit
     * Hanya bisa dilakukan setelah user verifikasi email
     * 
     * URL: /laporan/update/{id}
     * Method: POST
     * 
     * @param int $id - ID laporan yang ingin diupdate
     * @return Response - Redirect dengan pesan sukses/error
     * @throws PageNotFoundException - Jika laporan tidak ditemukan
     */
    public function update($id)
    {
        // Cek apakah request menggunakan method POST
        if ($this->request->getMethod() === 'post') {
            
            // Cari laporan berdasarkan ID di database
            $laporan = $this->laporanModel->find($id);
            
            // Jika laporan tidak ditemukan, tampilkan halaman 404
            if (!$laporan) {
                throw new \CodeIgniter\Exceptions\PageNotFoundException('Laporan tidak ditemukan');
            }

            // ================================================================
            // VERIFIKASI EMAIL SEBELUM UPDATE
            // ================================================================
            // Cek apakah email di session sama dengan email pelapor
            // Ini mencegah orang lain mengedit laporan yang bukan miliknya
            if (session()->get('verified_email_' . $id) !== $laporan['email_pelapor']) {
                return redirect()->to(base_url('laporan/edit/' . $id))->with('error', 'Silakan verifikasi email terlebih dahulu');
            }

            // ================================================================
            // SIAPKAN DATA UNTUK DIUPDATE
            // ================================================================
            $data = [
                'id'              => $id,                                          // ID laporan
                'email_pelapor'   => $this->request->getPost('email_pelapor'),     // Email pelapor
                'jenis_kerusakan' => $this->request->getPost('jenis_kerusakan'),   // Jenis kerusakan
                'deskripsi'       => $this->request->getPost('deskripsi'),         // Deskripsi
                'latitude'        => $this->request->getPost('latitude'),          // Koordinat latitude
                'longitude'       => $this->request->getPost('longitude'),         // Koordinat longitude
            ];

            // ================================================================
            // PROSES FILE UPLOAD BARU (JIKA ADA)
            // ================================================================
            $file = $this->request->getFile('foto_lokasi');
            
            // Jika ada file baru yang diupload
            if ($file && $file->isValid()) {
                
                // Validasi MIME type (hanya gambar yang diizinkan)
                $allowedMimes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
                if (!in_array($file->getMimeType(), $allowedMimes)) {
                    return redirect()->back()->with('error', 'File harus berupa gambar (JPG, PNG, GIF, atau WebP)');
                }

                // Validasi ukuran file (maksimal 5MB)
                $maxSize = 5 * 1024 * 1024; // 5MB dalam bytes
                if ($file->getSize() > $maxSize) {
                    return redirect()->back()->with('error', 'Ukuran file maksimal 5MB');
                }

                // Hapus file foto lama dari server
                $file_lama = FCPATH . 'uploads/' . $laporan['foto_lokasi'];
                if (file_exists($file_lama)) {
                    unlink($file_lama); // Hapus file
                }

                // Upload file baru dengan nama acak
                $nama_file = $file->getRandomName();
                $file->move(FCPATH . 'uploads', $nama_file);
                
                // Tambahkan nama file baru ke data yang akan diupdate
                $data['foto_lokasi'] = $nama_file;
            }

            // ================================================================
            // SIMPAN PERUBAHAN KE DATABASE
            // ================================================================
            if ($this->laporanModel->save($data)) {
                
                // ============================================================
                // KIRIM EMAIL NOTIFIKASI PERUBAHAN
                // ============================================================
                $subject = '✓ Laporan Anda #' . $id . ' Telah Diperbarui';
                $body = "Halo,\n\n";
                $body .= "Laporan fasilitas Anda telah berhasil diperbarui.\n\n";
                $body .= "Detail Laporan:\n";
                $body .= "- ID Laporan: #" . $id . "\n";
                $body .= "- Jenis Kerusakan: " . $data['jenis_kerusakan'] . "\n";
                $body .= "- Tanggal Update: " . date('d/m/Y H:i') . "\n\n";
                $body .= "Lihat detail laporan Anda di: " . base_url('laporan/detail/' . $id) . "\n\n";
                $body .= "Terima kasih,\nTim WebGIS Fasilitas Umum";
                
                // Kirim email
                $this->sendEmailNotification($data['email_pelapor'], $subject, $body);
                
                // Redirect ke halaman utama dengan pesan sukses
                return redirect()->to(base_url('laporan'))->with('success', 'Laporan berhasil diperbarui! Notifikasi telah dikirim ke email Anda.');
            } else {
                // Jika gagal, kembali ke form dengan pesan error
                return redirect()->back()->withInput()->with('error', 'Gagal memperbarui laporan');
            }
        }
    }

    /**
     * ========================================================================
     * FUNGSI VERIFY EMAIL - VERIFIKASI EMAIL SEBELUM EDIT
     * ========================================================================
     * Fungsi ini memverifikasi email user sebelum mengizinkan edit laporan
     * Ini adalah fitur keamanan agar hanya pelapor asli yang bisa edit
     * 
     * URL: /laporan/verify-email/{id}
     * Method: POST
     * 
     * @param int $id - ID laporan yang ingin diedit
     * @return Response - Redirect ke form edit jika berhasil, kembali jika gagal
     * @throws PageNotFoundException - Jika laporan tidak ditemukan
     */
    public function verifyEmail($id)
    {
        // Cek apakah request menggunakan method POST
        if ($this->request->getMethod() === 'post') {
            
            // Ambil email yang diinput user dari form
            $email = $this->request->getPost('email_pelapor');
            
            // Cari laporan berdasarkan ID di database
            $laporan = $this->laporanModel->find($id);

            // Jika laporan tidak ditemukan, tampilkan halaman 404
            if (!$laporan) {
                throw new \CodeIgniter\Exceptions\PageNotFoundException('Laporan tidak ditemukan');
            }

            // ================================================================
            // VERIFIKASI EMAIL
            // ================================================================
            // Bandingkan email yang diinput dengan email pelapor di database
            if ($email === $laporan['email_pelapor']) {
                // Email cocok, simpan ke session sebagai bukti verifikasi
                // Session key: 'verified_email_{id_laporan}'
                session()->set('verified_email_' . $id, $email);
                
                // Redirect ke form edit dengan pesan sukses
                return redirect()->to(base_url('laporan/edit/' . $id))->with('success', 'Email terverifikasi! Silakan edit laporan Anda.');
            } else {
                // Email tidak cocok, kembali dengan pesan error
                return redirect()->back()->with('error', 'Email tidak sesuai dengan laporan ini. Silakan gunakan email yang benar.');
            }
        }
    }

    /**
     * ========================================================================
     * FUNGSI LAPORAN SAYA - HALAMAN INPUT EMAIL
     * ========================================================================
     * Fungsi ini menampilkan form untuk user memasukkan email
     * agar bisa melihat semua laporan yang pernah dibuat dengan email tersebut
     * 
     * URL: /laporan/saya
     * Method: GET
     * 
     * @return View - Menampilkan form input email
     */
    public function laporanSaya()
    {
        // Tampilkan view form input email untuk melihat laporan
        return view('laporan/v_laporan_saya');
    }

    /**
     * ========================================================================
     * FUNGSI VERIFIKASI EMAIL - MENAMPILKAN DAFTAR LAPORAN USER
     * ========================================================================
     * Fungsi ini memverifikasi email dan menampilkan semua laporan
     * yang dibuat dengan email tersebut
     * 
     * URL: /laporan/verifikasi-email
     * Method: POST
     * 
     * @return View|Response - Menampilkan daftar laporan atau redirect jika tidak ada
     */
    public function verifikasiEmail()
    {
        // Cek apakah request menggunakan method POST
        if ($this->request->getMethod() === 'post') {
            
            // Ambil email dari form
            $email = $this->request->getPost('email');
            
            // ================================================================
            // CARI SEMUA LAPORAN DENGAN EMAIL TERSEBUT
            // ================================================================
            // Menggunakan where() untuk filter dan orderBy() untuk urutan
            $laporan = $this->laporanModel->where('email_pelapor', $email)
                                          ->orderBy('tgl_lapor', 'DESC') // Urutkan dari yang terbaru
                                          ->findAll();
            
            // Jika tidak ada laporan dengan email tersebut
            if (empty($laporan)) {
                return redirect()->back()->with('error', 'Tidak ditemukan laporan dengan email tersebut.');
            }
            
            // ================================================================
            // TAMPILKAN DAFTAR LAPORAN
            // ================================================================
            $data['email'] = $email;         // Email yang dicari
            $data['laporan'] = $laporan;     // Array laporan yang ditemukan
            
            // Tampilkan view daftar laporan user
            return view('laporan/v_list_laporan_saya', $data);
        }
        
        // Jika bukan POST, redirect ke halaman form
        return redirect()->to(base_url('laporan/saya'));
    }

    /**
     * ========================================================================
     * FUNGSI GET DATA JSON - API UNTUK PETA
     * ========================================================================
     * Fungsi ini mengembalikan data laporan dalam format JSON
     * Digunakan oleh JavaScript untuk menampilkan marker di peta
     * HANYA menampilkan laporan yang sudah diverifikasi admin (is_verified = 1)
     * 
     * URL: /laporan/getDataJson atau /api/laporan
     * Method: GET
     * 
     * @return JSON - Data laporan yang sudah diverifikasi
     */
    public function getDataJson()
    {
        // Ambil semua laporan yang sudah diverifikasi admin
        // is_verified = 1 berarti laporan sudah disetujui
        $laporan = $this->laporanModel->where('is_verified', 1)->findAll();
        
        // Kembalikan data dalam format JSON
        // setJSON() otomatis mengatur header Content-Type: application/json
        return $this->response->setJSON($laporan);
    }
}
