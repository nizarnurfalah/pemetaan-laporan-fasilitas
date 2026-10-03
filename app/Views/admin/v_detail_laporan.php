<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Laporan - Admin Dashboard</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.9.4/leaflet.min.css" />
    <style>
        * {
            margin: 0;
            padding: 0;
        }
        
        body {
            background: #f5f5f5;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            animation: fadeIn 0.5s ease-in-out;
            padding-top: 60px;
        }
        
        @keyframes fadeIn {
            from {
                opacity: 0;
            }
            to {
                opacity: 1;
            }
        }
        
        .wrapper-container {
            display: flex;
            min-height: 100vh;
        }
        
        .navbar {
            background: #333;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }
        
        .navbar-brand {
            font-weight: bold;
            font-size: 1.3rem;
            color: white !important;
        }
        
        /* Hamburger Toggle Button */
        .hamburger-toggle {
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.3);
            color: white !important;
            font-size: 1.5rem;
            cursor: pointer;
            padding: 8px 12px;
            border-radius: 6px;
            transition: all 0.3s ease;
            margin-right: 15px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 40px;
            min-height: 40px;
        }
        
        .hamburger-toggle:hover {
            background: rgba(255, 255, 255, 0.2) !important;
            border-color: rgba(255, 255, 255, 0.5) !important;
            color: #fff !important;
            transform: scale(1.05);
        }
        
        .hamburger-toggle:focus {
            outline: none;
            box-shadow: 0 0 0 2px rgba(255, 255, 255, 0.5);
        }
        
        .hamburger-toggle:active {
            transform: scale(0.95);
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
        
        /* Mobile admin info in sidebar */
        .sidebar-user-info {
            display: none;
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
        
        /* Navbar logout button styling */
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
        
        .info-row {
            padding: 15px 0;
            border-bottom: 1px solid #f0f0f0;
        }
        
        .info-row:last-child {
            border-bottom: none;
        }
        
        .info-label {
            font-weight: bold;
            color: #666;
            margin-bottom: 5px;
        }
        
        .info-value {
            color: #333;
            font-size: 15px;
        }
        
        .foto-container {
            text-align: center;
            margin: 20px 0;
        }
        
        .foto-container img {
            max-width: 100%;
            max-height: 400px;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }
        
        #map {
            width: 100%;
            height: 400px;
            border-radius: 8px;
            margin: 20px 0;
        }
        
        .status-badge {
            display: inline-block;
            padding: 8px 16px;
            border-radius: 20px;
            font-size: 13px;
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
        
        .form-group {
            margin-bottom: 15px;
        }
        
        .form-label {
            font-weight: bold;
            color: #333;
            margin-bottom: 5px;
        }
        
        .form-control, .form-select {
            border: 2px solid #e0e0e0;
            border-radius: 6px;
            padding: 10px;
        }
        
        .form-control:focus, .form-select:focus {
            border-color: #666;
            box-shadow: 0 0 0 0.2rem rgba(100, 100, 100, 0.15);
        }
        
        .btn-update {
            background: #666;
            border: none;
            color: white;
            font-weight: bold;
            padding: 10px 20px;
            border-radius: 6px;
            transition: transform 0.3s ease, background 0.3s ease;
        }
        
        .btn-update:hover {
            background: #555;
            color: white;
            transform: translateY(-2px);
        }
        
        .btn-kembali {
            background: #6c757d;
            border: none;
            color: white;
            font-weight: bold;
            padding: 10px 20px;
            border-radius: 6px;
            text-decoration: none;
        }
        
        .btn-kembali:hover {
            background: #5a6268;
            color: white;
            text-decoration: none;
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
        .alert {
            border-radius: 8px;
            border: none;
            margin-bottom: 20px;
        }
        
        /* Hamburger button styling - FORCE VISIBLE */
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
                font-size: 1.2rem;
            }
            
            /* Bootstrap classes d-none d-md-flex sudah handle responsive behavior */
            
            /* Show user info in sidebar on mobile */
            .sidebar-user-info {
                display: block !important;
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
            
            .detail-row {
                flex-direction: column;
                gap: 5px;
            }
            
            .detail-label {
                width: 100%;
                margin-bottom: 5px;
            }
            
            .detail-value {
                width: 100%;
            }
            
            .btn-action {
                padding: 8px 12px;
                font-size: 12px;
                margin: 5px;
                display: block;
                text-align: center;
            }
            
            .card-header h5 {
                font-size: 1rem;
            }
            
            .verification-section {
                padding: 15px;
            }
        }
    </style>
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark" style="background: #333; height: 60px; position: fixed; top: 0; left: 0; right: 0; z-index: 1030;">
        <div class="container-fluid">
            <button class="btn me-3" id="sidebarToggle" type="button" style="background: #555; border: 2px solid #fff; color: #fff; padding: 8px 12px; font-size: 16px;">
                <i class="fas fa-bars"></i>
            </button>
            <a class="navbar-brand" href="<?= base_url('admin') ?>">📄 Detail Laporan</a>
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
        <a href="<?= base_url('admin') ?>"><i class="fas fa-chart-line"></i> Dashboard</a>
        <a href="<?= base_url('admin/laporan/baru') ?>"><i class="fas fa-bell"></i> Laporan Baru</a>
        <a href="<?= base_url('admin/laporan') ?>"><i class="fas fa-clipboard-list"></i> Daftar Laporan</a>
    </div>

    <!-- Main Content -->
    <div class="main-content" id="mainContent">
        <?php if (session()->getFlashdata('success')): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                ✓ <?= session()->getFlashdata('success') ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <div class="row">
            <!-- Left Column - Detail Laporan -->
            <div class="col-lg-8">
                <!-- Info Laporan -->
                <div class="card">
                    <div class="card-header">
                        <h5>📝 Informasi Laporan #<?= $laporan['id'] ?></h5>
                    </div>
                    <div class="card-body">
                        <!-- Prioritas Badge -->
                        <div class="info-row" style="background: linear-gradient(135deg, #667eea10 0%, #764ba210 100%); padding: 15px; border-radius: 8px; margin-bottom: 15px;">
                            <div class="info-label"><i class="fas fa-sort-amount-up"></i> Prioritas (Hybrid Scheduling)</div>
                            <div class="info-value">
                                <?php 
                                $prioritas = $laporan['prioritas'] ?? 'sedang';
                                $prioritasStyle = [
                                    'tinggi' => 'background: #dc3545; color: white;',
                                    'sedang' => 'background: #ffc107; color: #000;',
                                    'rendah' => 'background: #28a745; color: white;'
                                ];
                                $prioritasIcon = [
                                    'tinggi' => '🔴',
                                    'sedang' => '🟡',
                                    'rendah' => '🟢'
                                ];
                                $prioritasDesc = [
                                    'tinggi' => 'Berbahaya / Mendesak',
                                    'sedang' => 'Perlu Diperbaiki',
                                    'rendah' => 'Bisa Ditunda'
                                ];
                                ?>
                                <span class="status-badge" style="<?= $prioritasStyle[$prioritas] ?? $prioritasStyle['sedang'] ?>; padding: 10px 20px; font-size: 14px;">
                                    <?= $prioritasIcon[$prioritas] ?? '🟡' ?> <?= ucfirst($prioritas) ?>
                                </span>
                                <small style="display: block; margin-top: 8px; color: #666;"><?= $prioritasDesc[$prioritas] ?? '' ?></small>
                            </div>
                        </div>
                        
                        <div class="info-row">
                            <div class="info-label">Email Pelapor</div>
                            <div class="info-value"><?= $laporan['email_pelapor'] ?></div>
                        </div>
                        <div class="info-row">
                            <div class="info-label">Jenis Kerusakan</div>
                            <div class="info-value"><?= ucfirst($laporan['jenis_kerusakan']) ?></div>
                        </div>
                        <div class="info-row">
                            <div class="info-label">Status</div>
                            <div class="info-value">
                                <span class="status-badge status-<?= strtolower(str_replace(' ', '-', $laporan['status'])) ?>">
                                    <?= $laporan['status'] ?>
                                </span>
                            </div>
                        </div>
                        <div class="info-row">
                            <div class="info-label">Deskripsi</div>
                            <div class="info-value"><?= nl2br($laporan['deskripsi']) ?></div>
                        </div>
                        <div class="info-row">
                            <div class="info-label">Tanggal Lapor</div>
                            <div class="info-value"><?= date('d/m/Y H:i', strtotime($laporan['tgl_lapor'])) ?></div>
                        </div>
                        <?php if ($laporan['tgl_perbaikan_dijadwalkan']): ?>
                            <div class="info-row">
                                <div class="info-label">Tanggal Perbaikan Dijadwalkan</div>
                                <div class="info-value"><?= date('d/m/Y H:i', strtotime($laporan['tgl_perbaikan_dijadwalkan'])) ?></div>
                            </div>
                        <?php endif; ?>
                        <?php if ($laporan['catatan_admin']): ?>
                            <div class="info-row">
                                <div class="info-label">Catatan Admin</div>
                                <div class="info-value"><?= nl2br($laporan['catatan_admin']) ?></div>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Foto Laporan -->
                <div class="card">
                    <div class="card-header">
                        <h5>📸 Foto Lokasi</h5>
                    </div>
                    <div class="card-body">
                        <div class="foto-container">
                            <img src="<?= base_url('uploads/' . $laporan['foto_lokasi']) ?>" alt="Foto Lokasi">
                        </div>
                    </div>
                </div>

                <!-- Peta Lokasi -->
                <div class="card">
                    <div class="card-header">
                        <h5>🗺️ Lokasi Kerusakan</h5>
                    </div>
                    <div class="card-body">
                        <div id="map"></div>
                        <div style="margin-top: 15px; padding: 15px; background: #f5f7fa; border-radius: 8px;">
                            <p><strong>Koordinat:</strong> <?= $laporan['latitude'] ?>, <?= $laporan['longitude'] ?></p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Column - Update Form -->
            <div class="col-lg-4">
                <!-- Verifikasi Section (if not verified yet) -->
                <?php if ($laporan['is_verified'] == 0): ?>
                <div class="card mb-3" style="border: 2px solid #ff9800;">
                    <div class="card-header" style="background: #ff9800; color: white;">
                        <h5>⚠️ Verifikasi Diperlukan</h5>
                    </div>
                    <div class="card-body">
                        <p style="font-size: 13px; color: #666;">Laporan ini belum diverifikasi. Silakan tinjau detail laporan dan pilih tindakan:</p>
                        <div class="d-grid gap-2">
                            <a href="<?= base_url('admin/dashboard/verify/' . $laporan['id'] . '/1') ?>" 
                               class="btn" style="background: #4caf50; color: white; font-weight: bold;"
                               onclick="return confirm('Setujui laporan ini? Laporan akan ditampilkan di peta publik.')">
                                ✓ Setujui Laporan
                            </a>
                            <a href="#" 
                               class="btn" style="background: #f44336; color: white; font-weight: bold;"
                               onclick="rejectLaporan(<?= $laporan['id'] ?>); return false;">
                                ✗ Tolak Laporan
                            </a>
                        </div>
                    </div>
                </div>
                <?php elseif ($laporan['is_verified'] == 1): ?>
                <div class="card mb-3" style="border: 2px solid #4caf50;">
                    <div class="card-body text-center">
                        <h5 style="color: #4caf50;">✓ Laporan Terverifikasi</h5>
                        <p style="font-size: 12px; color: #666; margin: 0;">
                            Diverifikasi oleh <strong><?= $laporan['verified_by'] ?></strong><br>
                            pada <?= date('d/m/Y H:i', strtotime($laporan['verified_at'])) ?>
                        </p>
                    </div>
                </div>
                <?php else: ?>
                <div class="card mb-3" style="border: 2px solid #f44336;">
                    <div class="card-body text-center">
                        <h5 style="color: #f44336;">✗ Laporan Ditolak</h5>
                        <p style="font-size: 12px; color: #666;">
                            <strong>Alasan:</strong> <?= $laporan['rejection_reason'] ?? 'Tidak memenuhi kriteria' ?>
                        </p>
                    </div>
                </div>
                <?php endif; ?>

                <div class="card">
                    <div class="card-header">
                        <h5>🔄 Update Status</h5>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="<?= base_url('admin/laporan/status/' . $laporan['id']) ?>">
                            <?= csrf_field() ?>
                            
                            <!-- Prioritas Section (Hybrid Scheduling) -->
                            <div class="form-group" style="background: linear-gradient(135deg, #667eea15 0%, #764ba215 100%); padding: 15px; border-radius: 8px; margin-bottom: 20px; border: 1px solid #667eea30;">
                                <label class="form-label" style="color: #667eea; font-weight: bold;">
                                    <i class="fas fa-sort-amount-up"></i> Set Prioritas (Hybrid Scheduling)
                                </label>
                                <?php 
                                $currentPrioritas = $laporan['prioritas'] ?? 'sedang';
                                ?>
                                <select class="form-select" name="prioritas" required style="border: 2px solid #667eea40;">
                                    <option value="tinggi" <?= $currentPrioritas === 'tinggi' ? 'selected' : '' ?>>🔴 Tinggi - Berbahaya / Mendesak</option>
                                    <option value="sedang" <?= $currentPrioritas === 'sedang' ? 'selected' : '' ?>>🟡 Sedang - Perlu Diperbaiki</option>
                                    <option value="rendah" <?= $currentPrioritas === 'rendah' ? 'selected' : '' ?>>🟢 Rendah - Bisa Ditunda</option>
                                </select>
                                <small style="color: #666; font-size: 11px; display: block; margin-top: 8px;">
                                    <i class="fas fa-info-circle"></i> Prioritas menentukan urutan penanganan laporan
                                </small>
                            </div>

                            <div class="form-group">
                                <label class="form-label">Status</label>
                                <select class="form-select" name="status" required>
                                    <option value="Baru" <?= $laporan['status'] === 'Baru' ? 'selected' : '' ?>>Baru</option>
                                    <option value="Diproses" <?= $laporan['status'] === 'Diproses' ? 'selected' : '' ?>>Diproses</option>
                                    <option value="Dijadwalkan" <?= $laporan['status'] === 'Dijadwalkan' ? 'selected' : '' ?>>Dijadwalkan</option>
                                    <option value="Selesai" <?= $laporan['status'] === 'Selesai' ? 'selected' : '' ?>>Selesai</option>
                                </select>
                            </div>

                            <div class="form-group">
                                <label class="form-label">Tanggal Perbaikan Dijadwalkan</label>
                                <input type="datetime-local" class="form-control" name="tgl_perbaikan_dijadwalkan" 
                                       value="<?= $laporan['tgl_perbaikan_dijadwalkan'] ? date('Y-m-d\TH:i', strtotime($laporan['tgl_perbaikan_dijadwalkan'])) : '' ?>">
                            </div>

                            <div class="form-group">
                                <label class="form-label">Catatan Admin</label>
                                <textarea class="form-control" name="catatan_admin" rows="4"><?= $laporan['catatan_admin'] ?></textarea>
                            </div>

                            <button type="submit" class="btn btn-update w-100">
                                💾 Update Status & Kirim Email
                            </button>
                        </form>

                        <hr class="my-3">

                        <div class="text-center">
                            <a href="<?= base_url('admin/laporan') ?>" class="btn btn-kembali w-100">
                                ← Kembali
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Info Box -->
                <div class="card mt-3" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; border: none;">
                    <div class="card-body">
                        <h6 class="card-title">ℹ️ Info</h6>
                        <p style="font-size: 12px; margin: 0;">
                            Ketika Anda mengubah status laporan, sistem akan secara otomatis mengirimkan email notifikasi ke pelapor dengan informasi terbaru tentang progres perbaikan.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.9.4/leaflet.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
    
    <script>
        // Inisialisasi Map
        const map = L.map('map').setView([<?= $laporan['latitude'] ?>, <?= $laporan['longitude'] ?>], 15);
        
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '© OpenStreetMap contributors',
            maxZoom: 19,
        }).addTo(map);
        
        // Tambah marker
        L.marker([<?= $laporan['latitude'] ?>, <?= $laporan['longitude'] ?>])
            .bindPopup('Lokasi Kerusakan: #<?= $laporan['id'] ?>')
            .addTo(map)
            .openPopup();
        
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
