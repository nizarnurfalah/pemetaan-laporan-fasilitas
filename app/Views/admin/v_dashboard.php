<!--
============================================================================
VIEW DASHBOARD ADMIN - HALAMAN UTAMA ADMIN
============================================================================

File ini adalah tampilan dashboard utama untuk admin/pengelola sistem.
Menampilkan ringkasan statistik dan tools untuk mengelola laporan.

Fitur yang tersedia:
1. Sidebar navigasi dengan menu:
   - Dashboard (halaman ini)
   - Daftar Laporan (dengan Hybrid Scheduling)
   - Laporan Baru (butuh verifikasi)
   - Logout

2. Statistik Cards:
   - Total Laporan (keseluruhan)
   - Laporan Baru (status = 'Baru')
   - Sedang Diproses (status = 'Diproses')
   - Selesai (status = 'Selesai')

3. Export PDF:
   - Filter berdasarkan tanggal (dari - sampai)
   - Filter berdasarkan status
   - 3 jenis export: Simple, Dengan Gambar, Gambar Saja

4. Peta WebGIS:
   - Menampilkan semua lokasi laporan
   - Marker dengan warna berbeda sesuai prioritas/status
   - Popup info laporan saat marker diklik

5. Grafik Statistik:
   - Pie chart berdasarkan jenis kerusakan
   - Pie chart berdasarkan status

Library yang digunakan:
- Bootstrap 5: Framework CSS
- Font Awesome 6: Ikon
- Leaflet.js: Peta interaktif
- Chart.js: Grafik statistik
- SweetAlert2: Popup konfirmasi

Data dari Controller:
- $statistik: Array statistik (total, baru, diproses, selesai)
- $laporan_terbaru: 5 laporan terbaru
- $semua_laporan: Semua laporan untuk peta
- $session: Data admin yang login

Controller: Admin\Dashboard::index()
URL: /admin/dashboard

Author: [Nama Anda]
============================================================================
-->

<!DOCTYPE html>
<html lang="id">
<head>
    <!-- Meta tag untuk encoding dan responsif -->
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - WebGIS Fasilitas Umum</title>
    
    <!-- CSS Libraries -->
    <!-- Bootstrap CSS: Framework tampilan -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" />
    <!-- Font Awesome: Ikon -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
    <!-- Leaflet CSS: Styling peta -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.9.4/leaflet.min.css" />
    
    <!-- Custom CSS -->
    <style>
        /* Reset margin dan padding */
        * {
            margin: 0;
            padding: 0;
        }
        
        /* Styling body dengan animasi fade in */
        body {
            background: #f5f5f5;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            animation: fadeIn 0.5s ease-in-out;
            margin: 0;
            padding: 0;
            padding-top: 60px;  /* Space untuk navbar fixed */
            overflow-x: hidden;
        }
        
        /* Animasi fade in */
        @keyframes fadeIn {
            from {
                opacity: 0;
            }
            to {
                opacity: 1;
            }
        }
        
        /* Container utama dengan flexbox */
        .wrapper-container {
            display: flex;
            min-height: 100vh;
        }
        
        /* ================================================
           NAVBAR - Header navigasi fixed di atas
           ================================================ */
        .navbar {
            background: #333;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 1030;
            height: 70px;
            padding: 12px 0;
        }
        
        .navbar-brand {
            font-weight: bold;
            font-size: 1.5rem;
            color: white !important;
            line-height: 1.2;
        }
        
        /* Navbar content spacing */
        .navbar .container-fluid {
            align-items: center;
            height: 100%;
        }
        
        /* User info styling */
        .navbar .ms-auto span {
            font-size: 1rem;
            margin-right: 20px;
        }
        
        /* ================================================
           MOBILE USER INFO - Info admin di sidebar mobile
           ================================================ */
        .sidebar-user-info {
            display: none;  /* Hidden di desktop */
            padding: 20px 25px;
            background: rgba(0, 0, 0, 0.2);
            border-bottom: 1px solid rgba(255, 255, 255, 0.2);
            margin-bottom: 10px;
        }
        
        .sidebar-user-info .admin-name {
            color: #fff;
            font-size: 14px;
            margin-bottom: 15px;
            padding-bottom: 10px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }
        
        /* Tombol logout di sidebar mobile */
        .sidebar-logout {
            background: #dc3545;
            color: white;
            border: none;
            padding: 10px 15px;
            border-radius: 6px;
            font-size: 13px;
            font-weight: 500;
            text-decoration: none;
            display: inline-block;
            transition: all 0.3s ease;
            width: 100%;
            text-align: center;
        }
        
        .sidebar-logout:hover {
            background: #c82333;
            color: white;
            text-decoration: none;
            transform: translateY(-1px);
        }
        
        /* Force visibility tombol hamburger */
        #sidebarToggle {
            visibility: visible !important;
            opacity: 1 !important;
            display: inline-flex !important;
        }
        
        /* ================================================
           TOMBOL LOGOUT DI NAVBAR
           ================================================ */
        .btn-logout {
            background: #dc3545;
            border: none;
            color: white;
            padding: 10px 20px;
            border-radius: 6px;
            font-size: 14px;
            font-weight: 500;
            text-decoration: none;
            transition: all 0.3s ease;
        }
        
        .btn-logout:hover {
            background: #c82333;
            color: white;
            text-decoration: none;
            transform: translateY(-1px);
            box-shadow: 0 4px 8px rgba(220, 53, 69, 0.3);
        }
        
        /* ================================================
           TOMBOL HAMBURGER - Toggle sidebar di mobile
           ================================================ */
        .hamburger-toggle {
            background: #444 !important;
            border: 2px solid #fff !important;
            color: white !important;
            font-size: 18px !important;
            cursor: pointer !important;
            padding: 10px !important;
            border-radius: 6px !important;
            transition: all 0.2s ease !important;
            margin-right: 15px !important;
            display: inline-flex !important;
            align-items: center !important;
            justify-content: center !important;
            min-width: 44px !important;
            min-height: 44px !important;
            position: relative !important;
            z-index: 9999 !important;
            opacity: 1 !important;
            visibility: visible !important;
            flex-shrink: 0 !important;
        }
        
        /* Force navbar layout */
        .navbar .container-fluid {
            display: flex !important;
            align-items: center !important;
        }
        
        .navbar {
            height: 70px !important;
            min-height: 70px !important;
        }
        
        .hamburger-toggle:hover {
            background: #555 !important;
            border-color: #fff !important;
            color: #fff !important;
            transform: scale(1.05) !important;
        }
        
        .hamburger-toggle:focus {
            outline: none !important;
            box-shadow: 0 0 0 2px rgba(255, 255, 255, 0.8) !important;
        }
        
        .hamburger-toggle:active {
            transform: scale(0.95) !important;
        }
        
        /* Fallback text styling */
        .hamburger-fallback {
            font-size: 10px !important;
            font-weight: bold !important;
            color: white !important;
            display: none !important;
        }
        
        .hamburger-toggle i {
            font-size: 1.2rem !important;
        }
        
        /* Show fallback if icon fails */
        .hamburger-toggle.no-icon .hamburger-fallback {
            display: inline !important;
        }
        
        .hamburger-toggle.no-icon i {
            display: none !important;
        }
        
        .sidebar {
            background: #555;
            color: white;
            padding: 0;
            width: 280px;
            box-shadow: 2px 0 10px rgba(0,0,0,0.1);
            flex-shrink: 0;
            transition: transform 0.3s ease;
            position: fixed;
            height: calc(100vh - 60px);
            top: 60px;
            left: 0;
            z-index: 1000;
            overflow-y: auto;
        }
        
        .sidebar.collapsed {
            transform: translateX(-100%);
        }
        
        .sidebar a {
            display: block;
            color: rgba(255,255,255,0.9);
            padding: 18px 25px;
            text-decoration: none;
            transition: all 0.3s ease;
            border-left: 4px solid transparent;
            font-size: 16px;
            font-weight: 500;
        }
        
        .sidebar a:hover,
        .sidebar a.active {
            background: rgba(255,255,255,0.15);
            color: white;
            border-left-color: #aaa;
            font-weight: 600;
        }
        
        /* Sidebar header styling */
        .sidebar-header {
            padding: 25px 25px 15px 25px;
            border-bottom: 1px solid rgba(255,255,255,0.2);
            margin-bottom: 10px;
            background: rgba(0,0,0,0.1);
        }
        
        .sidebar-header h6 {
            color: #ddd;
            margin: 0;
            text-transform: uppercase;
            font-size: 12px;
            letter-spacing: 2px;
            font-weight: 600;
        }
        
        .main-content {
            flex: 1;
            padding: 30px;
            background: #f5f5f5;
            margin-left: 280px;
            transition: margin-left 0.3s ease;
            min-height: calc(100vh - 70px);
        }
        
        .main-content.expanded {
            margin-left: 0;
        }
        
        /* Overlay untuk mobile */
        .sidebar-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            z-index: 999;
            opacity: 0;
            visibility: hidden;
            pointer-events: none;
            transition: all 0.3s ease;
        }
        
        .sidebar-overlay.active {
            opacity: 1;
            visibility: visible;
            pointer-events: auto;
        }
        
        .stat-card {
            background: white;
            border-radius: 12px;
            padding: 20px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            margin-bottom: 20px;
            transition: transform 0.3s ease;
        }
        
        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.15);
        }
        
        .stat-value {
            font-size: 2rem;
            font-weight: bold;
            color: #666;
            margin: 10px 0;
        }
        
        .stat-label {
            font-size: 14px;
            color: #999;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        
        .stat-icon {
            font-size: 2.5rem;
            opacity: 0.2;
            float: right;
        }
        
        .card {
            border: none;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            border-radius: 12px;
        }
        
        .card-header {
            background: #666;
            color: white;
            border-bottom: none;
            border-radius: 12px 12px 0 0;
            padding: 15px 20px;
        }
        
        .card-header h5 {
            margin: 0;
            font-weight: bold;
        }
        
        .table {
            margin-bottom: 0;
        }
        
        .table thead {
            background: #efefef;
        }
        
        .table thead th {
            border: none;
            color: #666;
            font-weight: bold;
            padding: 15px;
            text-transform: uppercase;
            font-size: 12px;
        }
        
        .table tbody td {
            padding: 15px;
            border: none;
            border-bottom: 1px solid #f0f0f0;
        }
        
        .status-badge {
            display: inline-block;
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: bold;
        }
        
        .status-baru {
            background: #888;
            color: #fff;
        }
        
        .status-diproses {
            background: #d32f2f;
            color: #fff;
        }
        
        .status-dijadwalkan {
            background: #fbc02d;
            color: #000;
        }
        
        .status-selesai {
            background: #388e3c;
            color: #fff;
        }
        
        .btn-action {
            padding: 6px 12px;
            font-size: 12px;
            border-radius: 6px;
            text-decoration: none;
            transition: all 0.3s ease;
        }
        
        .btn-detail {
            background: #666;
            color: white;
        }
        
        .btn-detail:hover {
            background: #555;
            color: white;
        }
        
        .btn-logout {
            background: #666;
            border: none;
            color: white;
            font-weight: bold;
            padding: 8px 15px;
            border-radius: 6px;
        }
        
        .btn-logout:hover {
            background: #555;
            color: white;
        }
        
        .export-section {
            margin: 25px 0;
        }
        
        .export-card {
            background: white;
            border-radius: 12px;
            padding: 20px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            border-left: 4px solid #dc3545;
        }
        
        .export-header h5 {
            color: #333;
            margin-bottom: 5px;
            font-weight: 600;
        }
        
        .export-header p {
            color: #666;
            font-size: 14px;
            margin-bottom: 15px;
        }
        
        .export-controls label {
            font-size: 12px;
            font-weight: 600;
            color: #555;
            margin-bottom: 4px;
        }
        
        .btn-export {
            background: linear-gradient(135deg, #dc3545 0%, #c82333 100%);
            color: white;
            border: none;
            padding: 8px 16px;
            font-weight: 600;
            letter-spacing: 0.5px;
            transition: all 0.3s ease;
            width: 100%;
            margin-top: 5px;
        }
        
        .btn-export:hover {
            background: linear-gradient(135deg, #c82333 0%, #bd2130 100%);
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(220, 53, 69, 0.3);
        }
        
        .btn-export i {
            margin-right: 6px;
        }
        
        .welcome-box {
            background: #666;
            color: white;
            padding: 20px;
            border-radius: 12px;
            margin-bottom: 20px;
        }
        
        .welcome-box h3 {
            margin: 0;
            font-weight: bold;
        }
        
        .welcome-box p {
            margin: 5px 0 0 0;
            opacity: 0.9;
        }
        
        @media (max-width: 992px) {
            .sidebar {
                transform: translateX(-100%);
            }
            
            .sidebar.active {
                transform: translateX(0);
            }
            
            .main-content {
                margin-left: 0;
            }
        }
        
        @media (max-width: 768px) {
            .navbar {
                height: 60px;
                padding: 8px 0;
            }
            
            .navbar-brand {
                font-size: 1.2rem;
            }
            
            /* Hide user info from navbar on mobile */
            .navbar .ms-auto {
                display: none;
            }
            
            /* Show user info in sidebar on mobile */
            .sidebar-user-info {
                display: block;
            }
            
            .hamburger-toggle {
                font-size: 1.4rem !important;
                min-width: 42px !important;
                min-height: 42px !important;
                padding: 8px 12px !important;
            }
            
            .sidebar {
                width: 300px;
                top: 0;
                height: 100vh;
            }
            
            .main-content {
                padding: 15px;
                margin-left: 0;
            }
            
            .sidebar a {
                font-size: 15px;
                padding: 15px 20px;
            }
            
            .sidebar-header {
                padding: 20px 20px 10px 20px;
            }
            
            body {
                padding-top: 60px;
            }
            
            .main-content {
                padding: 15px;
            }
            
            .stat-card {
                margin-bottom: 15px;
            }
            
            .table-responsive {
                font-size: 12px;
            }
            
            .table thead th,
            .table tbody td {
                padding: 10px 5px;
                font-size: 11px;
            }
            
            .btn-action {
                padding: 4px 8px;
                font-size: 10px;
                margin: 1px;
            }
            
            .card-header h5 {
                font-size: 1rem;
            }
        }

        #map {
            width: 100%;
            height: 400px;
            border-radius: 8px;
            margin-bottom: 20px;
        }

        .map-container {
            background: white;
            padding: 15px;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            margin-bottom: 20px;
        }

        .map-container h5 {
            color: #667eea;
            margin-bottom: 15px;
            font-weight: bold;
        }

        .popup-content {
            width: 280px;
            font-size: 13px;
        }

        .popup-content h6 {
            color: #667eea;
            margin-bottom: 8px;
            font-weight: bold;
        }

        .popup-content img {
            width: 100%;
            height: 120px;
            object-fit: cover;
            border-radius: 4px;
            margin-bottom: 8px;
        }

        .popup-content p {
            margin: 4px 0;
            line-height: 1.4;
        }

        .popup-actions {
            margin-top: 8px;
            display: flex;
            gap: 4px;
        }

        .popup-actions a {
            flex: 1;
            padding: 4px;
            text-align: center;
            border-radius: 4px;
            text-decoration: none;
            font-size: 11px;
            font-weight: bold;
            background-color: #667eea;
            color: white;
        }

        .popup-actions a:hover {
            background-color: #5569d8;
        }
    </style>
</head>
<body>
    <!-- Flash Message Notification -->
    <?php if (session()->getFlashdata('success')) : ?>
    <div class="alert alert-success alert-dismissible fade show position-fixed" 
         style="top: 80px; right: 20px; z-index: 9999; min-width: 300px; box-shadow: 0 4px 12px rgba(0,0,0,0.15);" 
         role="alert" id="flashAlert">
        <i class="fas fa-check-circle me-2"></i>
        <?= session()->getFlashdata('success') ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    <?php endif; ?>
    
    <?php if (session()->getFlashdata('error')) : ?>
    <div class="alert alert-danger alert-dismissible fade show position-fixed" 
         style="top: 80px; right: 20px; z-index: 9999; min-width: 300px; box-shadow: 0 4px 12px rgba(0,0,0,0.15);" 
         role="alert" id="flashAlert">
        <i class="fas fa-exclamation-circle me-2"></i>
        <?= session()->getFlashdata('error') ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    <?php endif; ?>

    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark" style="background: #333; height: 60px; position: fixed; top: 0; left: 0; right: 0; z-index: 1030;">
        <div class="container-fluid">
            <button class="btn me-3" id="sidebarToggle" type="button" style="background: #555; border: 2px solid #fff; color: #fff; padding: 8px 12px; font-size: 16px;">
                <i class="fas fa-bars"></i>
            </button>
            <a class="navbar-brand" href="<?= base_url('admin') ?>">📊 Dashboard Admin</a>
            <div class="ms-auto d-none d-md-flex align-items-center">
                <span style="color: white; margin-right: 15px;">👤 <?= session()->get('username') ?></span>
                <a href="<?= base_url('logout') ?>" class="btn btn-danger btn-sm">Logout</a>
            </div>
        </div>
    </nav>

    <!-- Sidebar Overlay -->
    <div class="sidebar-overlay" id="sidebarOverlay"></div>
    
    <!-- Sidebar -->
    <div class="sidebar" id="sidebar">
        <!-- Mobile User Info -->
        <div class="sidebar-user-info">
            <div style="font-size: 14px; color: #ccc; margin-bottom: 5px;">Selamat Datang,</div>
            <div style="font-size: 18px; font-weight: bold; color: #fff; margin-bottom: 15px;">👤 Admin</div>
            <a href="<?= base_url('logout') ?>" style="background: #dc3545; color: #fff; padding: 8px 20px; border-radius: 5px; text-decoration: none; font-size: 14px; display: inline-block;">Logout</a>
        </div>
        
        <div class="sidebar-header">
            <h6>Menu Navigasi</h6>
        </div>
        <a href="<?= base_url('admin') ?>" class="active"><i class="fas fa-chart-line"></i> Dashboard</a>
        <a href="<?= base_url('admin/laporan/baru') ?>"><i class="fas fa-bell"></i> Laporan Baru</a>
        <a href="<?= base_url('admin/laporan') ?>"><i class="fas fa-clipboard-list"></i> Daftar Laporan</a>
    </div>

    <!-- Main Content -->
    <div class="main-content" id="mainContent">
        <!-- Welcome Box -->
        <div class="welcome-box">
            <h3>Selamat datang, Admin!</h3>
            <p>Kelola dan pantau laporan kerusakan fasilitas umum</p>
        </div>

        <!-- Statistics Cards -->
        <div class="row">
            <div class="col-md-3 col-sm-6">
                <div class="stat-card">
                    <div class="stat-icon">📊</div>
                    <div class="stat-label">Total Laporan</div>
                    <div class="stat-value"><?= $total_laporan ?></div>
                </div>
            </div>

            <div class="col-md-3 col-sm-6">
                <div class="stat-card">
                    <div class="stat-icon">🟡</div>
                    <div class="stat-label">Baru</div>
                    <div class="stat-value"><?= $laporan_baru ?></div>
                </div>
            </div>

            <div class="col-md-3 col-sm-6">
                <div class="stat-card">
                    <div class="stat-icon">🔵</div>
                    <div class="stat-label">Diproses</div>
                    <div class="stat-value"><?= $laporan_diproses ?></div>
                </div>
            </div>

            <div class="col-md-3 col-sm-6">
                <div class="stat-card">
                    <div class="stat-icon">🟢</div>
                    <div class="stat-label">Selesai</div>
                    <div class="stat-value"><?= $laporan_selesai ?></div>
                </div>
            </div>
        </div>

        <!-- Statistik Prioritas (Hybrid Scheduling) -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-header" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                        <h5><i class="fas fa-sort-amount-up"></i> Statistik Prioritas (Hybrid Scheduling)</h5>
                    </div>
                    <div class="card-body">
                        <div class="row text-center">
                            <div class="col-md-4 col-sm-4">
                                <div class="p-3" style="background: #ffebee; border-radius: 10px; border-left: 4px solid #dc3545;">
                                    <div style="font-size: 2rem; font-weight: bold; color: #dc3545;"><?= $prioritas_tinggi ?? 0 ?></div>
                                    <div style="font-size: 12px; color: #666; text-transform: uppercase; letter-spacing: 1px;">🔴 Prioritas Tinggi</div>
                                    <small style="color: #999;">Berbahaya / Mendesak</small>
                                </div>
                            </div>
                            <div class="col-md-4 col-sm-4">
                                <div class="p-3" style="background: #fff8e1; border-radius: 10px; border-left: 4px solid #ffc107;">
                                    <div style="font-size: 2rem; font-weight: bold; color: #ffc107;"><?= $prioritas_sedang ?? 0 ?></div>
                                    <div style="font-size: 12px; color: #666; text-transform: uppercase; letter-spacing: 1px;">🟡 Prioritas Sedang</div>
                                    <small style="color: #999;">Perlu Diperbaiki</small>
                                </div>
                            </div>
                            <div class="col-md-4 col-sm-4">
                                <div class="p-3" style="background: #e8f5e9; border-radius: 10px; border-left: 4px solid #28a745;">
                                    <div style="font-size: 2rem; font-weight: bold; color: #28a745;"><?= $prioritas_rendah ?? 0 ?></div>
                                    <div style="font-size: 12px; color: #666; text-transform: uppercase; letter-spacing: 1px;">🟢 Prioritas Rendah</div>
                                    <small style="color: #999;">Bisa Ditunda</small>
                                </div>
                            </div>
                        </div>
                        <div class="mt-3 p-2" style="background: #f8f9fa; border-radius: 6px; font-size: 12px; color: #666;">
                            <i class="fas fa-info-circle"></i> <strong>Hybrid Scheduling:</strong> Laporan diurutkan berdasarkan prioritas (tinggi → rendah) kemudian waktu lapor (lama → baru).
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Export Section -->
        <div class="export-section">
            <div class="export-card">
                <div class="export-header">
                    <h5>📄 Export Laporan</h5>
                    <p>Download laporan dalam format PDF dengan filter yang dapat disesuaikan</p>
                </div>
                <div class="export-controls">
                    <div class="row">
                        <div class="col-md-2">
                            <label>Status:</label>
                            <select class="form-control form-control-sm" id="exportStatus">
                                <option value="">Semua Status</option>
                                <option value="Baru">Baru</option>
                                <option value="Diproses">Diproses</option>
                                <option value="Dijadwalkan">Dijadwalkan</option>
                                <option value="Selesai">Selesai</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label>Jenis Kerusakan:</label>
                            <select class="form-control form-control-sm" id="exportJenis">
                                <option value="">Semua Jenis</option>
                                <option value="Jalan Berlubang">Jalan Berlubang</option>
                                <option value="Fasilitas Publik Rusak">Fasilitas Publik Rusak</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label>Bulan:</label>
                            <select class="form-control form-control-sm" id="exportBulan">
                                <option value="">Semua Bulan</option>
                                <option value="1">Januari</option>
                                <option value="2">Februari</option>
                                <option value="3">Maret</option>
                                <option value="4">April</option>
                                <option value="5">Mei</option>
                                <option value="6">Juni</option>
                                <option value="7">Juli</option>
                                <option value="8">Agustus</option>
                                <option value="9">September</option>
                                <option value="10">Oktober</option>
                                <option value="11">November</option>
                                <option value="12">Desember</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label>Tahun:</label>
                            <select class="form-control form-control-sm" id="exportTahun">
                                <?php for($year = date('Y'); $year >= 2020; $year--): ?>
                                <option value="<?= $year ?>" <?= $year == date('Y') ? 'selected' : '' ?>><?= $year ?></option>
                                <?php endfor; ?>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label>&nbsp;</label>
                            <button class="btn btn-success btn-sm" onclick="exportToPDFWithImages()" style="width: 100%; margin-bottom: 5px;">
                                <i class="fas fa-file-pdf"></i> Export PDF
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Map Section -->
        <div class="map-container">
            <h5>🗺️ Peta Persebaran Laporan</h5>
            <div id="map"></div>
        </div>

        <!-- Recent Reports -->
        <div class="card">
            <div class="card-header">
                <h5>📋 Laporan Terbaru (Diurutkan berdasarkan Prioritas)</h5>
            </div>
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Email Pelapor</th>
                            <th>Jenis</th>
                            <th>Prioritas</th>
                            <th>Status</th>
                            <th>Tgl Lapor</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($laporan)): ?>
                            <?php foreach ($laporan as $l): ?>
                                <tr>
                                    <td><strong>#<?= $l['id'] ?></strong></td>
                                    <td><?= $l['email_pelapor'] ?></td>
                                    <td><?= ucfirst($l['jenis_kerusakan']) ?></td>
                                    <td>
                                        <?php 
                                        $prioritas = $l['prioritas'] ?? 'sedang';
                                        $prioritasClass = [
                                            'tinggi' => 'background: #dc3545; color: white;',
                                            'sedang' => 'background: #ffc107; color: #000;',
                                            'rendah' => 'background: #28a745; color: white;'
                                        ];
                                        $prioritasIcon = [
                                            'tinggi' => '🔴',
                                            'sedang' => '🟡',
                                            'rendah' => '🟢'
                                        ];
                                        ?>
                                        <span class="status-badge" style="<?= $prioritasClass[$prioritas] ?? $prioritasClass['sedang'] ?>">
                                            <?= $prioritasIcon[$prioritas] ?? '🟡' ?> <?= ucfirst($prioritas) ?>
                                        </span>
                                    </td>
                                    <td>
                                        <span class="status-badge status-<?= strtolower(str_replace(' ', '-', $l['status'])) ?>">
                                            <?= $l['status'] ?>
                                        </span>
                                    </td>
                                    <td><?= date('d/m/Y', strtotime($l['tgl_lapor'])) ?></td>
                                    <td>
                                        <a href="<?= base_url('admin/laporan/detail/' . $l['id']) ?>" class="btn-action btn-detail">
                                            Lihat
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="7" class="text-center text-muted py-4">
                                    Belum ada laporan
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.9.4/leaflet.min.js"></script>
    
    <script>
        // Base URL untuk API
        const baseUrl = '<?= base_url() ?>';
        
        // Inisialisasi Map
        const map = L.map('map').setView([-6.9175, 107.6062], 12); // Center di Bandung
        
        // Tambah Tile Layer
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '© OpenStreetMap contributors',
            maxZoom: 19,
        }).addTo(map);
        
        // Icon marker dengan warna berdasarkan status
        function getMarkerColor(status) {
            const colorMap = {
                'Baru': '#888',
                'Diproses': '#d32f2f',      // Merah
                'Dijadwalkan': '#fbc02d',   // Kuning
                'Selesai': '#388e3c'        // Hijau
            };
            return colorMap[status] || '#888';
        }
        
        function getMarkerIcon(status) {
            const color = getMarkerColor(status);
            return L.circleMarker([0, 0], {
                radius: 8,
                fillColor: color,
                color: 'white',
                weight: 2,
                opacity: 1,
                fillOpacity: 0.9
            }).options;
        }
        
        // Muat data laporan
        function loadLaporan() {
            fetch(baseUrl + 'api/laporan')
                .then(response => response.json())
                .then(data => {
                    displayMarkers(data);
                })
                .catch(error => console.error('Error:', error));
        }
        
        // Tampilkan marker di peta
        function displayMarkers(laporan) {
            laporan.forEach(item => {
                const statusClass = 'status-' + item.status.toLowerCase().replace(' ', '-');
                const markerColor = getMarkerColor(item.status);
                
                const popup = `
                    <div class="popup-content">
                        <h6>${item.jenis_kerusakan.toUpperCase()}</h6>
                        <img src="${baseUrl}uploads/${item.foto_lokasi}" alt="Foto Lokasi">
                        <p><strong>Email:</strong> ${item.email_pelapor}</p>
                        <p><strong>Deskripsi:</strong> ${item.deskripsi}</p>
                        <div class="status-badge ${statusClass}">${item.status}</div>
                        <p><strong>Tanggal Lapor:</strong> ${new Date(item.tgl_lapor).toLocaleDateString('id-ID')}</p>
                        ${item.tgl_perbaikan_dijadwalkan ? `<p><strong>Tgl Perbaikan:</strong> ${new Date(item.tgl_perbaikan_dijadwalkan).toLocaleDateString('id-ID')}</p>` : ''}
                        ${item.catatan_admin ? `<p><strong>Catatan:</strong> ${item.catatan_admin}</p>` : ''}
                        <div class="popup-actions">
                            <a href="${baseUrl}admin/laporan/detail/${item.id}">Detail</a>
                        </div>
                    </div>
                `;
                
                L.circleMarker([item.latitude, item.longitude], {
                    radius: 8,
                    fillColor: markerColor,
                    color: 'white',
                    weight: 2,
                    opacity: 1,
                    fillOpacity: 0.9
                })
                .bindPopup(popup)
                .addTo(map);
            });
        }
        
        
        // Load data saat page loaded
        loadLaporan();
        
        // Refresh setiap 30 detik
        setInterval(loadLaporan, 30000);

        // 📄 Export PDF Function  
        function exportToPDFWithImages() {
            // Get filter values
            const status = document.getElementById('exportStatus').value;
            const jenis = document.getElementById('exportJenis').value;
            const bulan = document.getElementById('exportBulan').value;
            const tahun = document.getElementById('exportTahun').value;

            // Build URL with parameters
            let exportUrl = baseUrl + 'admin/export/images?';
            const params = [];
            
            if (status) params.push('status=' + encodeURIComponent(status));
            if (jenis) params.push('jenis=' + encodeURIComponent(jenis));
            if (bulan) params.push('bulan=' + encodeURIComponent(bulan));
            if (tahun) params.push('tahun=' + encodeURIComponent(tahun));
            
            exportUrl += params.join('&');
            
            // Show loading state
            const btn = event.target;
            const originalText = btn.innerHTML;
            btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Generating PDF...';
            btn.disabled = true;
            
            // Direct window.open approach
            window.open(exportUrl, '_blank');
            
            // Reset button
            setTimeout(() => {
                btn.innerHTML = originalText;
                btn.disabled = false;
            }, 5000);
        }
        
        // Sidebar Toggle Functionality
        document.addEventListener('DOMContentLoaded', function() {
            const sidebarToggle = document.getElementById('sidebarToggle');
            const sidebar = document.getElementById('sidebar');
            const mainContent = document.getElementById('mainContent');
            const sidebarOverlay = document.getElementById('sidebarOverlay');
            
            // Toggle function
            function toggleSidebar() {
                if (window.innerWidth <= 992) {
                    // Mobile: toggle active class
                    sidebar.classList.toggle('active');
                    sidebarOverlay.classList.toggle('active');
                } else {
                    // Desktop: toggle collapsed class
                    sidebar.classList.toggle('collapsed');
                    mainContent.classList.toggle('expanded');
                }
            }
            
            // Toggle button click
            sidebarToggle.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                toggleSidebar();
            });
            
            // Overlay click to close sidebar
            sidebarOverlay.addEventListener('click', function() {
                sidebar.classList.remove('active');
                sidebarOverlay.classList.remove('active');
            });
            
            // Handle window resize
            window.addEventListener('resize', function() {
                if (window.innerWidth > 992) {
                    sidebar.classList.remove('active');
                    sidebarOverlay.classList.remove('active');
                }
            });
            
            // Auto-hide flash message after 3 seconds
            const flashAlert = document.getElementById('flashAlert');
            if (flashAlert) {
                setTimeout(function() {
                    flashAlert.classList.remove('show');
                    setTimeout(function() {
                        flashAlert.remove();
                    }, 150);
                }, 3000);
            }
        });
    </script>
</body>
</html>