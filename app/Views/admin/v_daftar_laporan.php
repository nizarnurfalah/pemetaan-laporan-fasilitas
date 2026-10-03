<!--
============================================================================
VIEW DAFTAR LAPORAN - HALAMAN DAFTAR SEMUA LAPORAN DENGAN HYBRID SCHEDULING
============================================================================

File ini menampilkan daftar semua laporan yang sudah terverifikasi
dengan algoritma Hybrid Scheduling (Prioritas + FIFO).

Fitur yang tersedia:
1. Sidebar navigasi (sama dengan dashboard)
2. Tabel daftar laporan dengan kolom:
   - ID, Jenis Kerusakan, Status, Prioritas
   - Tanggal Lapor, Tanggal Dijadwalkan
   - Tombol aksi (Detail, Update Status, Hapus)
3. Filter berdasarkan status
4. Search laporan
5. Pagination

HYBRID SCHEDULING ALGORITHM:
- Prioritas Tinggi diproses terlebih dahulu
- Dalam prioritas yang sama, FIFO (First In First Out)
- Urutan: Tinggi -> Sedang -> Rendah
- Dalam setiap prioritas: berdasarkan tgl_lapor ASC

Status Laporan:
- Baru: Laporan baru masuk (abu-abu)
- Diproses: Sedang dalam penanganan (merah)
- Dijadwalkan: Sudah ada jadwal perbaikan (kuning)
- Selesai: Perbaikan selesai (hijau)

Data dari Controller:
- $laporan: Array laporan yang sudah diurutkan dengan Hybrid Scheduling
- $session: Data admin yang login

Controller: Admin\Dashboard::daftarLaporan()
URL: /admin/daftar-laporan

Author: [Nama Anda]
============================================================================
-->

<!DOCTYPE html>
<html lang="id">
<head>
    <!-- Meta tag untuk encoding dan responsif -->
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Laporan - Admin Dashboard</title>
    
    <!-- CSS Libraries -->
    <!-- Bootstrap CSS: Framework tampilan -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" />
    <!-- Font Awesome: Ikon -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
    
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
            padding-top: 60px;  /* Space untuk navbar fixed */
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
        }
        
        .navbar-brand {
            font-weight: bold;
            font-size: 1.3rem;
            color: white !important;
        }
        
        /* ================================================
           TOMBOL HAMBURGER - Toggle sidebar
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
            border-color: #ddd !important;
            color: #fff !important;
            transform: scale(1.05) !important;
        }
        
        .hamburger-toggle:focus {
            outline: none;
            box-shadow: 0 0 0 2px rgba(255, 255, 255, 0.5);
        }
        
        .hamburger-toggle:active {
            transform: scale(0.95);
        }
        
        /* ================================================
           SIDEBAR - Menu navigasi kiri
           ================================================ */
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
        
        .card {
            border: none;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            border-radius: 12px;
            margin-bottom: 20px;
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
        
        .filter-section {
            background: white;
            padding: 20px;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            margin-bottom: 20px;
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
        
        .table tbody tr:hover {
            background: #f9f9f9;
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
            margin: 2px;
            display: inline-block;
        }
        
        .btn-detail {
            background: #666;
            color: white;
        }
        
        .btn-detail:hover {
            background: #555;
            color: white;
        }
        
        .btn-hapus {
            background: #dc3545;
            color: white;
        }
        
        .btn-hapus:hover {
            background: #c82333;
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
        
        .btn-logout:hover {
            background: linear-gradient(135deg, #f5576c 0%, #f093fb 100%);
            color: white;
        }
        
        /* Sidebar User Info Components */
        .admin-name {
            font-size: 16px;
            font-weight: bold;
            color: white;
            margin-bottom: 15px;
            text-shadow: 0 1px 3px rgba(0,0,0,0.3);
        }
        
        .sidebar-logout {
            background: rgba(255,255,255,0.2);
            color: white;
            padding: 8px 16px;
            border-radius: 20px;
            text-decoration: none;
            font-size: 14px;
            font-weight: 500;
            border: 2px solid rgba(255,255,255,0.3);
            transition: all 0.3s ease;
            display: inline-block;
        }
        
        .sidebar-logout:hover {
            background: rgba(255,255,255,0.3);
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 4px 10px rgba(0,0,0,0.2);
            text-decoration: none;
        }
        
        .form-control, .form-select {
            border: 2px solid #e0e0e0;
            border-radius: 6px;
            padding: 8px 12px;
        }
        
        .form-control:focus, .form-select:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.15);
        }
        
        .empty-state {
            text-align: center;
            padding: 40px 20px;
            color: #999;
        }
        
        .empty-state-icon {
            font-size: 3rem;
            margin-bottom: 10px;
        }
        
        /* Hamburger button styling */
        .hamburger-toggle {
            background: #666 !important;
            border: 2px solid white !important;
            color: white !important;
            padding: 10px !important;
            margin-right: 15px !important;
            border-radius: 5px !important;
            font-size: 18px !important;
            cursor: pointer !important;
            display: inline-block !important;
            z-index: 1000 !important;
        }
        
        .hamburger-toggle:hover {
            background: #555 !important;
            border-color: #ddd !important;
        }
        
        .hamburger-toggle i,
        .hamburger-toggle .hamburger-fallback {
            color: white !important;
            font-weight: bold !important;
        }
        
        /* Desktop: sidebar user info hidden */
        @media (min-width: 769px) {
            .sidebar-user-info {
                display: none !important;
            }
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
            .navbar-brand {
                font-size: 1.1rem !important;
            }
            
            .hamburger-toggle {
                min-width: 40px !important;
                min-height: 40px !important;
                padding: 8px !important;
                font-size: 16px !important;
            }
            
            /* Show user info in sidebar on mobile */
            .sidebar-user-info {
                display: block !important;
            }
            
            .filter-section {
                padding: 15px !important;
                margin: 10px !important;
            }
            
            .table-responsive {
                font-size: 12px !important;
            }
            
            .btn-action {
                padding: 4px 8px !important;
                font-size: 10px !important;
                margin: 1px !important;
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
            
            .filter-section {
                padding: 15px;
            }
            
            .filter-section .row {
                margin: 0;
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
            
            .status-badge {
                padding: 4px 8px;
                font-size: 10px;
            }
            
            .card-header h5 {
                font-size: 1rem;
            }
        }
    </style>
</head>
<body>
    <!-- ================================================
         NAVBAR - Header navigasi fixed di atas layar
         ================================================ -->
    <nav class="navbar navbar-expand-lg navbar-dark" style="background: #333; height: 60px; position: fixed; top: 0; left: 0; right: 0; z-index: 1030;">
        <div class="container-fluid">
            <!-- Tombol hamburger untuk toggle sidebar -->
            <button class="btn me-3" id="sidebarToggle" type="button" style="background: #555; border: 2px solid #fff; color: #fff; padding: 8px 12px; font-size: 16px;">
                <i class="fas fa-bars"></i>
            </button>
            
            <!-- Brand/Logo - link ke halaman admin -->
            <a class="navbar-brand" href="<?= base_url('admin') ?>">📋 Daftar Laporan</a>
            
            <!-- User info dan logout di kanan (hanya tampil di desktop/md+) -->
            <div class="ms-auto d-none d-md-flex align-items-center">
                <!-- Menampilkan username admin yang sedang login -->
                <span style="color: white; margin-right: 15px;">👤 <?= session()->get('username') ?></span>
                <!-- Tombol logout -->
                <a href="<?= base_url('logout') ?>" class="btn btn-danger btn-sm">Logout</a>
            </div>
        </div>
    </nav>

    <!-- ================================================
         SIDEBAR OVERLAY - Background gelap saat sidebar terbuka (mobile)
         ================================================ -->
    <div class="sidebar-overlay" id="sidebarOverlay"></div>
    
    <!-- ================================================
         SIDEBAR - Menu navigasi samping kiri
         ================================================ -->
    <div class="sidebar" id="sidebar">
        <!-- User Info untuk tampilan mobile -->
        <div class="sidebar-user-info">
            <div style="font-size: 14px; color: #ccc; margin-bottom: 5px;">Selamat Datang,</div>
            <div style="font-size: 18px; font-weight: bold; color: #fff; margin-bottom: 15px;">👤 Admin</div>
            <a href="<?= base_url('logout') ?>" style="background: #dc3545; color: #fff; padding: 8px 20px; border-radius: 5px; text-decoration: none; font-size: 14px; display: inline-block;">Logout</a>
        </div>
        
        <!-- Header sidebar dengan label Menu Navigasi -->
        <div class="sidebar-header">
            <h6>Menu Navigasi</h6>
        </div>
        
        <!-- Menu navigasi sidebar -->
        <!-- Dashboard: Halaman utama admin dengan statistik -->
        <a href="<?= base_url('admin') ?>"><i class="fas fa-chart-line"></i> Dashboard</a>
        <!-- Laporan Baru: Daftar laporan yang belum diverifikasi -->
        <a href="<?= base_url('admin/laporan/baru') ?>"><i class="fas fa-bell"></i> Laporan Baru</a>
        <!-- Daftar Laporan: Halaman ini (active) - semua laporan terverifikasi -->
        <a href="<?= base_url('admin/laporan') ?>" class="active"><i class="fas fa-clipboard-list"></i> Daftar Laporan</a>
    </div>

    <!-- ================================================
         MAIN CONTENT - Konten utama halaman
         ================================================ -->
    <div class="main-content" id="mainContent">
        <!-- ================================================
             FILTER SECTION - Form untuk filter/pencarian laporan
             ================================================ -->
        <div class="filter-section">
            <h5 class="mb-3">🔍 Filter Laporan</h5>
            <div class="row g-3">
                <!-- Filter berdasarkan email pelapor -->
                <div class="col-md-3">
                    <input type="text" class="form-control" id="filterEmail" placeholder="Cari Email...">
                </div>
                
                <!-- Filter berdasarkan status laporan -->
                <div class="col-md-3">
                    <select class="form-select" id="filterStatus">
                        <option value="">-- Semua Status --</option>
                        <option value="Baru">Baru</option>
                        <option value="Diproses">Diproses</option>
                        <option value="Dijadwalkan">Dijadwalkan</option>
                        <option value="Selesai">Selesai</option>
                    </select>
                </div>
                
                <!-- Filter berdasarkan prioritas (bagian dari Hybrid Scheduling) -->
                <div class="col-md-3">
                    <select class="form-select" id="filterPrioritas">
                        <option value="">-- Semua Prioritas --</option>
                        <option value="tinggi">🔴 Tinggi</option>
                        <option value="sedang">🟡 Sedang</option>
                        <option value="rendah">🟢 Rendah</option>
                    </select>
                </div>
                
                <!-- Filter berdasarkan jenis kerusakan -->
                <div class="col-md-3">
                    <select class="form-select" id="filterJenis">
                        <option value="">-- Semua Jenis --</option>
                        <option value="Fasilitas Publik Rusak">Fasilitas</option>
                        <option value="Jalan Berlubang">Jalan</option>
                    </select>
                </div>
            </div>
        </div>

        <!-- ================================================
             INFO HYBRID SCHEDULING - Penjelasan algoritma penjadwalan
             Algoritma ini menggabungkan Priority Scheduling dan FIFO:
             1. Prioritas tinggi diproses duluan
             2. Dalam prioritas sama, yang lebih dulu melapor diproses duluan
             ================================================ -->
        <div class="alert alert-info mb-3" style="border-left: 4px solid #667eea;">
            <i class="fas fa-info-circle"></i> <strong>Hybrid Scheduling:</strong> Laporan diurutkan berdasarkan prioritas (tinggi → rendah), kemudian waktu lapor (lama → baru).
        </div>

        <!-- ================================================
             FLASH MESSAGES - Notifikasi sukses/error dari controller
             ================================================ -->
        <!-- Alert sukses jika ada operasi berhasil -->
        <?php if (session()->getFlashdata('success')): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                ✓ <?= session()->getFlashdata('success') ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <!-- Alert error jika ada operasi gagal -->
        <?php if (session()->getFlashdata('error')): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                ✗ <?= session()->getFlashdata('error') ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <!-- ================================================
             TABEL DAFTAR LAPORAN - Menampilkan semua laporan
             Data sudah diurutkan dengan Hybrid Scheduling dari Controller
             ================================================ -->
        <div class="card">
            <div class="card-header">
                <h5>📋 Semua Laporan (Diurutkan berdasarkan Prioritas)</h5>
            </div>
            
            <!-- Jika ada data laporan -->
            <?php if (!empty($laporan)): ?>
                <div class="table-responsive">
                    <table class="table" id="laporanTable">
                        <!-- Header tabel dengan kolom-kolom informasi laporan -->
                        <thead>
                            <tr>
                                <th>ID</th>              <!-- ID unik laporan -->
                                <th>Email Pelapor</th>   <!-- Email yang melapor -->
                                <th>Jenis Kerusakan</th> <!-- Jalan Berlubang / Fasilitas Publik Rusak -->
                                <th>Prioritas</th>       <!-- Tinggi/Sedang/Rendah (untuk Hybrid Scheduling) -->
                                <th>Status</th>          <!-- Baru/Diproses/Dijadwalkan/Selesai -->
                                <th>Verifikasi</th>      <!-- Pending/Verified/Ditolak -->
                                <th>Tgl Lapor</th>       <!-- Tanggal laporan dibuat -->
                                <th>Tgl Perbaikan</th>   <!-- Tanggal dijadwalkan perbaikan -->
                                <th>Aksi</th>            <!-- Tombol-tombol aksi -->
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Loop untuk setiap laporan dari database -->
                            <?php foreach ($laporan as $l): ?>
                                <?php 
                                // ================================================
                                // KONFIGURASI PRIORITAS
                                // Bagian dari implementasi Hybrid Scheduling
                                // ================================================
                                
                                // Ambil prioritas, default 'sedang' jika tidak ada
                                $prioritas = $l['prioritas'] ?? 'sedang';
                                
                                // Warna badge berdasarkan prioritas
                                // Tinggi = Merah (urgent/darurat)
                                // Sedang = Kuning (normal)
                                // Rendah = Hijau (tidak urgent)
                                $prioritasClass = [
                                    'tinggi' => 'background: #dc3545; color: white;',
                                    'sedang' => 'background: #ffc107; color: #000;',
                                    'rendah' => 'background: #28a745; color: white;'
                                ];
                                
                                // Ikon emoji untuk prioritas
                                $prioritasIcon = [
                                    'tinggi' => '🔴',
                                    'sedang' => '🟡',
                                    'rendah' => '🟢'
                                ];
                                ?>
                                
                                <!-- Baris laporan dengan data-attribute untuk filter -->
                                <tr class="laporan-row" data-status="<?= $l['status'] ?>" data-jenis="<?= $l['jenis_kerusakan'] ?>" data-email="<?= $l['email_pelapor'] ?>" data-prioritas="<?= $prioritas ?>">
                                    <!-- ID Laporan -->
                                    <td><strong>#<?= $l['id'] ?></strong></td>
                                    
                                    <!-- Email Pelapor -->
                                    <td><?= $l['email_pelapor'] ?></td>
                                    
                                    <!-- Jenis Kerusakan -->
                                    <td><?= ucfirst($l['jenis_kerusakan']) ?></td>
                                    
                                    <!-- Badge Prioritas (Hybrid Scheduling) -->
                                    <td>
                                        <span class="status-badge" style="<?= $prioritasClass[$prioritas] ?? $prioritasClass['sedang'] ?>">
                                            <?= $prioritasIcon[$prioritas] ?? '🟡' ?> <?= ucfirst($prioritas) ?>
                                        </span>
                                    </td>
                                    
                                    <!-- Badge Status Laporan -->
                                    <td>
                                        <span class="status-badge status-<?= strtolower(str_replace(' ', '-', $l['status'])) ?>">
                                            <?= $l['status'] ?>
                                        </span>
                                    </td>
                                    
                                    <!-- Status Verifikasi Email -->
                                    <td>
                                        <?php if ($l['is_verified'] == 0): ?>
                                            <!-- Belum diverifikasi -->
                                            <span class="status-badge" style="background: #ff9800; color: white;">Pending</span>
                                        <?php elseif ($l['is_verified'] == 1): ?>
                                            <!-- Sudah diverifikasi/disetujui -->
                                            <span class="status-badge" style="background: #4caf50; color: white;">✓ Verified</span>
                                        <?php else: ?>
                                            <!-- Ditolak oleh admin -->
                                            <span class="status-badge" style="background: #f44336; color: white;">✗ Ditolak</span>
                                        <?php endif; ?>
                                    </td>
                                    
                                    <!-- Tanggal Lapor (diformat ke dd/mm/yyyy HH:mm) -->
                                    <td><?= date('d/m/Y H:i', strtotime($l['tgl_lapor'])) ?></td>
                                    
                                    <!-- Tanggal Perbaikan Dijadwalkan -->
                                    <td>
                                        <?php if ($l['tgl_perbaikan_dijadwalkan']): ?>
                                            <?= date('d/m/Y', strtotime($l['tgl_perbaikan_dijadwalkan'])) ?>
                                        <?php else: ?>
                                            <span class="text-muted">-</span>
                                        <?php endif; ?>
                                    </td>
                                    
                                    <!-- Tombol-tombol Aksi -->
                                    <td>
                                        <!-- Tombol verifikasi hanya muncul jika belum diverifikasi -->
                                        <?php if ($l['is_verified'] == 0): ?>
                                            <!-- Setujui laporan - ubah is_verified menjadi 1 -->
                                            <a href="<?= base_url('admin/dashboard/verify/' . $l['id'] . '/1') ?>" class="btn-action" style="background: #4caf50; color: white; margin-bottom: 4px;" onclick="return confirm('Setujui laporan ini?')">
                                                ✓ Setujui
                                            </a>
                                            <!-- Tolak laporan - minta alasan penolakan -->
                                            <a href="#" class="btn-action" style="background: #f44336; color: white;" onclick="rejectLaporan(<?= $l['id'] ?>); return false;">
                                                ✗ Tolak
                                            </a>
                                        <?php endif; ?>
                                        
                                        <!-- Tombol lihat detail laporan -->
                                        <a href="<?= base_url('admin/laporan/detail/' . $l['id']) ?>" class="btn-action btn-detail">
                                            Lihat Detail
                                        </a>
                                        
                                        <!-- Tombol hapus laporan dengan konfirmasi -->
                                        <a href="<?= base_url('admin/laporan/hapus/' . $l['id']) ?>" class="btn-action btn-hapus" onclick="return confirm('Yakin ingin menghapus?')">
                                            Hapus
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <!-- Tampilan jika tidak ada data laporan -->
                <div class="empty-state">
                    <div class="empty-state-icon">📭</div>
                    <p>Belum ada laporan</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
    
    <script>
        // Filter functionality
        document.getElementById('filterEmail').addEventListener('keyup', filterTable);
        document.getElementById('filterStatus').addEventListener('change', filterTable);
        document.getElementById('filterJenis').addEventListener('change', filterTable);
        document.getElementById('filterPrioritas').addEventListener('change', filterTable);
        
        function filterTable() {
            const email = document.getElementById('filterEmail').value.toLowerCase();
            const status = document.getElementById('filterStatus').value;
            const jenis = document.getElementById('filterJenis').value;
            const prioritas = document.getElementById('filterPrioritas').value;
            
            const rows = document.querySelectorAll('.laporan-row');
            rows.forEach(row => {
                const rowEmail = row.dataset.email.toLowerCase();
                const rowStatus = row.dataset.status;
                const rowJenis = row.dataset.jenis;
                const rowPrioritas = row.dataset.prioritas || 'sedang';
                
                const emailMatch = rowEmail.includes(email);
                const statusMatch = !status || rowStatus === status;
                const jenisMatch = !jenis || rowJenis === jenis;
                const prioritasMatch = !prioritas || rowPrioritas === prioritas;
                
                row.style.display = emailMatch && statusMatch && jenisMatch && prioritasMatch ? '' : 'none';
            });
        }
        
        // Function to reject laporan with reason
        function rejectLaporan(id) {
            const reason = prompt('Alasan penolakan laporan:');
            if (reason && reason.trim() !== '') {
                window.location.href = '<?= base_url('admin/dashboard/verify/') ?>' + id + '/2?reason=' + encodeURIComponent(reason);
            }
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
        });
    </script>
</body>
</html>
