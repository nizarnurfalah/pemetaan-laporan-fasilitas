<!--
============================================================================
VIEW LOGIN ADMIN - HALAMAN LOGIN UNTUK ADMIN
============================================================================

File ini adalah tampilan form login untuk admin/pengelola sistem.
Admin perlu login terlebih dahulu sebelum bisa mengakses dashboard.

Fitur yang tersedia:
1. Form login dengan username dan password
2. Validasi input dengan CSRF protection
3. Flash message untuk error/sukses
4. Tombol kembali ke halaman peta publik

Alur autentikasi:
1. Admin masukkan username dan password
2. Form dikirim ke Auth::prosesLogin()
3. Jika berhasil: redirect ke dashboard
4. Jika gagal: kembali dengan pesan error

Controller: Auth::login()
URL: /login

Author: [Nama Anda]
============================================================================
-->

<!DOCTYPE html>
<html lang="id">
<head>
    <!-- Meta tag untuk encoding dan responsif -->
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - WebGIS Fasilitas Umum</title>
    
    <!-- Bootstrap CSS: Framework tampilan -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" />
    
    <!-- Custom CSS -->
    <style>
        /* Reset margin dan padding */
        * {
            margin: 0;
            padding: 0;
        }
        
        /* Styling body - halaman login dengan warna abu-abu */
        body {
            background: #555;
            min-height: 100vh;
            display: flex;
            align-items: center;       /* Vertikal center */
            justify-content: center;   /* Horizontal center */
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
        
        /* Container form login */
        .login-container {
            width: 100%;
            max-width: 400px;
            padding: 20px;
        }
        
        /* Styling card login */
        .card {
            border: none;
            box-shadow: 0 15px 40px rgba(0,0,0,0.3);
            border-radius: 15px;
            overflow: hidden;
        }
        
        /* Header card dengan ikon dan judul */
        .card-header {
            background: #666;
            color: white;
            padding: 30px;
            text-align: center;
            border-bottom: none;
        }
        
        .card-header h3 {
            margin: 0;
            font-weight: bold;
            margin-bottom: 5px;
        }
        
        .card-header p {
            margin: 0;
            font-size: 13px;
            opacity: 0.9;
        }
        
        /* Body card berisi form */
        .card-body {
            padding: 30px;
        }
        
        /* Styling form group */
        .form-group {
            margin-bottom: 20px;
        }
        
        /* Label form */
        .form-label {
            font-weight: bold;
            color: #333;
            margin-bottom: 8px;
            display: block;
        }
        
        /* Input form */
        .form-control {
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            padding: 12px 15px;
            font-size: 14px;
            transition: all 0.3s ease;
        }
        
        /* Efek focus pada input */
        .form-control:focus {
            border-color: #666;
            box-shadow: 0 0 0 0.2rem rgba(100, 100, 100, 0.15);
        }
        
        /* Tombol login */
        .btn-login {
            width: 100%;
            background: #666;
            border: none;
            color: white;
            font-weight: bold;
            padding: 12px;
            border-radius: 8px;
            transition: transform 0.3s ease, background 0.3s ease;
            margin-top: 10px;
        }
        
        .btn-login:hover {
            background: #555;
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
        }
        
        /* Alert message */
        .alert {
            border-radius: 8px;
            border: none;
            margin-bottom: 20px;
        }
        
        /* Garis pembatas "atau" */
        .divider {
            display: flex;
            align-items: center;
            margin: 20px 0;
        }
        
        .divider::before,
        .divider::after {
            content: '';
            flex: 1;
            height: 1px;
            background: #ddd;
        }
        
        .divider span {
            padding: 0 10px;
            color: #999;
            font-size: 13px;
        }
        
        /* Tombol kembali ke peta */
        .btn-back {
            width: 100%;
            background: #f0f0f0;
            border: none;
            color: #333;
            font-weight: bold;
            padding: 12px;
            border-radius: 8px;
            text-decoration: none;
            display: inline-block;
            text-align: center;
            transition: all 0.3s ease;
        }
        
        .btn-back:hover {
            background: #e0e0e0;
            color: #333;
            text-decoration: none;
        }
        
        /* Ikon admin di header */
        .icon-admin {
            font-size: 40px;
            margin-bottom: 10px;
        }
        
        /* ================================================
           RESPONSIVE - Tampilan untuk layar kecil (mobile)
           ================================================ */
        @media (max-width: 480px) {
            .login-container {
                padding: 10px;
            }
            
            .card-header {
                padding: 20px;
            }
            
            .card-body {
                padding: 20px;
            }
        }
    </style>
</head>
<body>
    <!-- ================================================
         CONTAINER LOGIN
         Menampilkan card form login di tengah halaman
         ================================================ -->
    <div class="login-container">
        <div class="card">
            <!-- ================================================
                 CARD HEADER
                 Berisi ikon, judul, dan subtitle
                 ================================================ -->
            <div class="card-header">
                <div class="icon-admin">🔐</div>
                <h3>Admin Login</h3>
                <p>Masuk ke Dashboard Admin</p>
            </div>
            
            <!-- ================================================
                 CARD BODY
                 Berisi form login dan tombol
                 ================================================ -->
            <div class="card-body">
                <!-- ================================================
                     FLASH MESSAGE ERROR
                     Ditampilkan jika login gagal (username/password salah)
                     ================================================ -->
                <?php if (session()->getFlashdata('error')): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <strong>Error!</strong> <?= session()->getFlashdata('error') ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <!-- ================================================
                     FLASH MESSAGE SUKSES
                     Ditampilkan setelah berhasil logout
                     ================================================ -->
                <?php if (session()->getFlashdata('success')): ?>
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <strong>Sukses!</strong> <?= session()->getFlashdata('success') ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <!-- ================================================
                     FORM LOGIN
                     Method POST ke Auth::prosesLogin()
                     csrf_field() = token keamanan CSRF
                     ================================================ -->
                <form method="POST" action="<?= base_url('login') ?>">
                    <?= csrf_field() ?> <!-- Token CSRF untuk keamanan -->
                    
                    <!-- Input Username -->
                    <div class="form-group">
                        <label class="form-label" for="username">Username</label>
                        <input type="text" class="form-control" id="username" name="username" 
                               placeholder="Masukkan username" required autofocus 
                               value="<?= old('username') ?>"> <!-- old() = nilai sebelumnya jika validasi gagal -->
                    </div>

                    <!-- Input Password -->
                    <div class="form-group">
                        <label class="form-label" for="password">Password</label>
                        <input type="password" class="form-control" id="password" name="password" 
                               placeholder="Masukkan password" required>
                    </div>

                    <!-- Tombol Submit Login -->
                    <button type="submit" class="btn btn-login">
                        🔓 Login
                    </button>
                </form>

                <!-- Pembatas dengan teks "atau" -->
                <div class="divider">
                    <span>atau</span>
                </div>

                <!-- ================================================
                     TOMBOL KEMBALI KE PETA
                     Link ke halaman utama untuk user biasa
                     ================================================ -->
                <a href="<?= base_url('/') ?>" class="btn-back">
                    ← Kembali ke Peta
                </a>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS: Untuk komponen interaktif (alert dismissible) -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
</body>
</html>
