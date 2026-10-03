<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Baru - Admin Dashboard</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
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
        
        .card {
            border: none;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            border-radius: 12px;
            margin-bottom: 20px;
        }
        
        .card-header {
            background: #ff9800;
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
        
        .table tbody tr:hover {
            background: #fff8e1;
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
        
        .empty-state {
            text-align: center;
            padding: 40px 20px;
            color: #999;
        }
        
        .empty-state-icon {
            font-size: 3rem;
            margin-bottom: 10px;
        }
        
        .alert-info-custom {
            background: #e3f2fd;
            color: #1976d2;
            border: 1px solid #90caf9;
            border-radius: 8px;
            padding: 15px;
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
    </style>
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark" style="background: #333; height: 60px; position: fixed; top: 0; left: 0; right: 0; z-index: 1030;">
        <div class="container-fluid">
            <button class="btn me-3" id="sidebarToggle" type="button" style="background: #555; border: 2px solid #fff; color: #fff; padding: 8px 12px; font-size: 16px;">
                <i class="fas fa-bars"></i>
            </button>
            <a class="navbar-brand" href="<?= base_url('admin') ?>">🔔 Laporan Baru</a>
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
        <a href="<?= base_url('admin/laporan/baru') ?>" class="active"><i class="fas fa-bell"></i> Laporan Baru</a>
        <a href="<?= base_url('admin/laporan') ?>"><i class="fas fa-clipboard-list"></i> Daftar Laporan</a>
    </div>

    <!-- Main Content -->
    <div class="main-content" id="mainContent">
        <div class="alert-info-custom">
            <strong>ℹ️ Info:</strong> Halaman ini menampilkan laporan yang belum diverifikasi dan memerlukan persetujuan Anda.
        </div>

        <!-- Alerts -->
        <?php if (session()->getFlashdata('success')): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                ✓ <?= session()->getFlashdata('success') ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <?php if (session()->getFlashdata('error')): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                ✗ <?= session()->getFlashdata('error') ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <!-- Table -->
        <div class="card">
            <div class="card-header">
                <h5>🔔 Laporan Menunggu Verifikasi (<?= count($laporan) ?>)</h5>
            </div>
            
            <?php if (!empty($laporan)): ?>
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Email Pelapor</th>
                                <th>Jenis Kerusakan</th>
                                <th>Deskripsi</th>
                                <th>Status</th>
                                <th>Tgl Lapor</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($laporan as $l): ?>
                                <tr>
                                    <td><strong>#<?= $l['id'] ?></strong></td>
                                    <td><?= $l['email_pelapor'] ?></td>
                                    <td><?= ucfirst($l['jenis_kerusakan']) ?></td>
                                    <td><?= substr($l['deskripsi'], 0, 50) ?>...</td>
                                    <td>
                                        <span class="status-badge status-<?= strtolower(str_replace(' ', '-', $l['status'])) ?>">
                                            <?= $l['status'] ?>
                                        </span>
                                    </td>
                                    <td><?= date('d/m/Y H:i', strtotime($l['tgl_lapor'])) ?></td>
                                    <td>
                                        <a href="<?= base_url('admin/dashboard/verify/' . $l['id'] . '/1') ?>" class="btn-action" style="background: #4caf50; color: white;" onclick="return confirm('Setujui laporan ini?')">
                                            ✓ Setujui
                                        </a>
                                        <a href="#" class="btn-action" style="background: #f44336; color: white;" onclick="rejectLaporan(<?= $l['id'] ?>); return false;">
                                            ✗ Tolak
                                        </a>
                                        <a href="<?= base_url('admin/laporan/detail/' . $l['id']) ?>" class="btn-action btn-detail">
                                            Lihat Detail
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <div class="empty-state">
                    <div class="empty-state-icon">✅</div>
                    <p>Tidak ada laporan baru yang menunggu verifikasi</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
    
    <script>
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
