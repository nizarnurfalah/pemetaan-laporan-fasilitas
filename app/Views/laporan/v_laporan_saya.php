<!--
============================================================================
VIEW LAPORAN SAYA - HALAMAN UNTUK CEK LAPORAN DENGAN EMAIL
============================================================================

File ini adalah tampilan form untuk pengguna mengecek laporan yang
sudah mereka buat menggunakan email.

Alur penggunaan:
1. User memasukkan email yang digunakan saat membuat laporan
2. Form dikirim ke Laporan::verifikasiEmail()
3. Jika email ditemukan: redirect ke daftar laporan user
4. Jika email tidak ditemukan: kembali dengan pesan error

Controller: Laporan::laporanSaya()
URL: /laporan/laporan-saya

Author: [Nama Anda]
============================================================================
-->

<!DOCTYPE html>
<html lang="id">
<head>
    <!-- Meta tag untuk encoding dan responsif -->
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Saya - WebGIS Fasilitas Umum</title>
    
    <!-- Bootstrap CSS: Framework tampilan -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" />
    
    <!-- Custom CSS -->
    <style>
        /* Styling body dengan gradient background */
        body {
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            min-height: 100vh;
            padding: 20px 0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            animation: fadeIn 0.5s ease-in-out;
        }
        
        /* Animasi fade in saat halaman dimuat */
        @keyframes fadeIn {
            from {
                opacity: 0;
            }
            to {
                opacity: 1;
            }
        }
        
        /* Styling navbar */
        .navbar {
            background: #333;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            margin-bottom: 30px;
        }
        
        .navbar-brand {
            font-weight: bold;
            color: white !important;
        }
        
        /* Tombol kembali ke peta */
        .btn-kembali {
            background: #666;
            color: white;
            border: none;
            padding: 8px 16px;
            border-radius: 4px;
            font-weight: 600;
            text-decoration: none;
        }
        
        .btn-kembali:hover {
            background: #555;
            color: white;
        }
        
        /* Styling card */
        .card {
            border: none;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
            border-radius: 12px;
            overflow: hidden;
        }
        
        /* Header card */
        .card-header {
            background: #555;
            color: white;
            padding: 20px;
            border-bottom: none;
        }
        
        .card-header h4 {
            margin: 0;
            font-weight: bold;
        }
        
        /* Input form */
        .form-control {
            border: 2px solid #ddd;
            border-radius: 8px;
            padding: 12px 15px;
            transition: all 0.3s ease;
        }
        
        /* Efek focus pada input */
        .form-control:focus {
            border-color: #666;
            box-shadow: 0 0 0 0.2rem rgba(100, 100, 100, 0.15);
        }
        
        /* Tombol submit */
        .btn-submit {
            background: #666;
            border: none;
            color: white;
            font-weight: bold;
            padding: 12px 30px;
            border-radius: 8px;
            transition: all 0.3s ease;
        }
        
        .btn-submit:hover {
            background: #555;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
        }
        
        /* ================================================
           RESPONSIVE - Tampilan untuk layar kecil (mobile)
           ================================================ */
        @media (max-width: 768px) {
            body {
                padding: 10px 0;
            }
            
            .navbar-brand {
                font-size: 1rem;
            }
            
            .card {
                margin: 10px;
            }
            
            .card-header h4 {
                font-size: 1.2rem;
            }
            
            .card-body {
                padding: 20px 15px;
            }
            
            .btn-submit,
            .btn-kembali {
                padding: 10px 20px;
                font-size: 14px;
                width: 100%;
                margin: 5px 0;
            }
        }
    </style>
</head>
<body>
    <!-- ================================================
         NAVBAR - Header navigasi
         ================================================ -->
    <nav class="navbar navbar-expand-lg">
        <div class="container-fluid">
            <!-- Logo dan nama aplikasi -->
            <a class="navbar-brand" href="<?= base_url('/') ?>">📊 WebGIS Fasilitas Umum</a>
            <!-- Tombol kembali ke peta -->
            <div class="ms-auto">
                <a href="<?= base_url('laporan') ?>" class="btn-kembali">← Kembali ke Peta</a>
            </div>
        </div>
    </nav>

    <!-- ================================================
         MAIN CONTAINER
         Card untuk form input email
         ================================================ -->
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card">
                    <!-- Header card -->
                    <div class="card-header text-center">
                        <h4>📋 Cek Laporan Saya</h4>
                    </div>
                    <div class="card-body p-4">
                        <!-- ================================================
                             FLASH MESSAGE ERROR
                             Ditampilkan jika email tidak ditemukan
                             ================================================ -->
                        <?php if (session()->getFlashdata('error')): ?>
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                ✗ <?= session()->getFlashdata('error') ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        <?php endif; ?>

                        <!-- Instruksi untuk pengguna -->
                        <p class="text-center text-muted mb-4">
                            Masukkan email yang Anda gunakan saat melaporkan kerusakan untuk melihat status laporan Anda.
                        </p>
                        
                        <!-- ================================================
                             FORM INPUT EMAIL
                             Mengirim email ke Laporan::verifikasiEmail()
                             untuk dicek di database
                             ================================================ -->
                        <form method="POST" action="<?= base_url('laporan/verifikasi-email') ?>">
                            <?= csrf_field() ?> <!-- Token CSRF untuk keamanan -->
                            
                            <!-- Input email -->
                            <div class="mb-4">
                                <label class="form-label fw-bold">📧 Email Anda</label>
                                <input type="email" class="form-control" name="email" placeholder="contoh@email.com" required>
                                <small class="form-text text-muted">
                                    Email yang digunakan saat membuat laporan
                                </small>
                            </div>
                            
                            <!-- Tombol submit -->
                            <div class="d-grid">
                                <button type="submit" class="btn btn-submit">
                                    🔍 Cek Laporan Saya
                                </button>
                            </div>
                        </form>
                        
                        <hr class="my-4">
                        
                        <!-- Tips untuk pengguna -->
                        <div class="text-center">
                            <small class="text-muted">
                                💡 <strong>Tips:</strong> Gunakan email yang sama dengan saat Anda membuat laporan.
                            </small>
                        </div>
                    </div>
                </div>
                
                <!-- ================================================
                     LINK UNTUK BUAT LAPORAN BARU
                     Untuk user yang belum pernah membuat laporan
                     ================================================ -->
                <div class="text-center mt-3">
                    <small class="text-muted">
                        Belum pernah melaporkan? 
                        <a href="<?= base_url('laporan/tambah') ?>" style="color: #555; font-weight: bold;">Buat Laporan Baru</a>
                    </small>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS: Untuk komponen interaktif -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
</body>
</html>
