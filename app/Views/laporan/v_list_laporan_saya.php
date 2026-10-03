<!--
============================================================================
VIEW LIST LAPORAN SAYA - DAFTAR LAPORAN BERDASARKAN EMAIL
============================================================================

File ini menampilkan daftar semua laporan yang dibuat oleh pengguna
berdasarkan email yang diverifikasi.

Fitur yang ditampilkan:
1. Info email dan total laporan
2. Card untuk setiap laporan dengan status verifikasi
3. Detail laporan (jenis, deskripsi, status, tanggal)
4. Link ke peta untuk laporan yang sudah diverifikasi
5. Pesan kosong jika tidak ada laporan

Status Verifikasi:
- is_verified = 0: Menunggu verifikasi (orange)
- is_verified = 1: Terverifikasi (hijau)
- is_verified = 2: Ditolak (merah)

Data yang dikirim dari Controller:
- $email: Email yang diverifikasi
- $laporan: Array berisi daftar laporan user

Controller: Laporan::verifikasiEmail()
URL: /laporan/verifikasi-email (POST)

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
    
    <!-- CSS Libraries -->
    <!-- Bootstrap CSS: Framework tampilan -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" />
    <!-- Font Awesome: Ikon -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
    
    <!-- Custom CSS -->
    <style>
        /* Styling body dengan gradient background */
        body {
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            min-height: 100vh;
            padding: 20px 0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
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
        
        /* Styling card laporan */
        .card {
            border: none;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            border-radius: 12px;
            margin-bottom: 20px;
            overflow: hidden;
            transition: transform 0.3s ease;
        }
        
        /* Efek hover pada card */
        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(0,0,0,0.15);
        }
        
        /* Header card */
        .card-header {
            background: #555;
            color: white;
            padding: 15px 20px;
            border-bottom: none;
            font-weight: bold;
        }
        
        /* Badge status verifikasi - TERVERIFIKASI */
        .badge-verified {
            background: #4caf50;
            color: white;
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: bold;
        }
        
        /* Badge status verifikasi - MENUNGGU */
        .badge-pending {
            background: #ff9800;
            color: white;
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: bold;
        }
        
        /* Badge status verifikasi - DITOLAK */
        .badge-rejected {
            background: #f44336;
            color: white;
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: bold;
        }
        
        /* Badge status laporan (Baru/Diproses/Dijadwalkan/Selesai) */
        .status-badge {
            display: inline-block;
            padding: 4px 10px;
            border-radius: 15px;
            font-size: 11px;
            font-weight: bold;
        }
        
        /* Warna status Baru */
        .status-baru {
            background: #888;
            color: #fff;
        }
        
        /* Warna status Diproses */
        .status-diproses {
            background: #d32f2f;
            color: #fff;
        }
        
        /* Warna status Dijadwalkan */
        .status-dijadwalkan {
            background: #fbc02d;
            color: #000;
        }
        
        /* Warna status Selesai */
        .status-selesai {
            background: #388e3c;
            color: #fff;
        }
        
        /* Tombol lihat di peta */
        .btn-peta {
            background: #2196F3;
            color: white;
            border: none;
            padding: 8px 16px;
            border-radius: 6px;
            font-size: 13px;
            font-weight: bold;
            text-decoration: none;
            display: inline-block;
            transition: all 0.3s ease;
        }
        
        .btn-peta:hover {
            background: #1976D2;
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(33, 150, 243, 0.4);
        }
        
        /* Baris info dalam card */
        .info-item {
            display: flex;
            padding: 10px 0;
            border-bottom: 1px solid #f0f0f0;
        }
        
        .info-item:last-child {
            border-bottom: none;
        }
        
        /* Label info */
        .info-label {
            flex: 0 0 150px;
            font-weight: 600;
            color: #666;
            font-size: 14px;
        }
        
        /* Nilai info */
        .info-value {
            flex: 1;
            color: #333;
            font-size: 14px;
        }
        
        /* Box info email dan total laporan */
        .alert-info-custom {
            background: #e3f2fd;
            color: #1976d2;
            border: 1px solid #90caf9;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 20px;
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
            
            .container {
                padding: 10px;
            }
            
            .page-title {
                font-size: 1.5rem;
                padding: 15px;
            }
            
            .laporan-card {
                margin-bottom: 15px;
            }
            
            .card-header h5 {
                font-size: 1rem;
            }
            
            .info-label {
                font-size: 12px;
            }
            
            .info-value {
                font-size: 13px;
            }
            
            .btn-view-map,
            .btn-kembali {
                padding: 8px 15px;
                font-size: 13px;
            }
            
            .status-badge {
                padding: 4px 10px;
                font-size: 11px;
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
         ================================================ -->
    <div class="container">
        <!-- ================================================
             INFO EMAIL DAN TOTAL LAPORAN
             Menampilkan email yang diverifikasi dan jumlah laporan
             ================================================ -->
        <div class="alert-info-custom text-center">
            <strong>📧 Email:</strong> <?= $email ?> | 
            <strong>📊 Total Laporan:</strong> <?= count($laporan) ?>
        </div>

        <!-- Flash message sukses -->
        <?php if (session()->getFlashdata('success')): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                ✓ <?= session()->getFlashdata('success') ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <!-- ================================================
             DAFTAR LAPORAN
             Loop untuk menampilkan setiap laporan dalam card
             ================================================ -->
        <?php if (!empty($laporan)): ?>
            <div class="row">
                <?php foreach ($laporan as $l): ?>
                    <div class="col-md-6 mb-3">
                        <div class="card">
                            <!-- ================================================
                                 HEADER CARD - ID Laporan dan Status Verifikasi
                                 ================================================ -->
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <span>📍 Laporan #<?= $l['id'] ?></span>
                                <!-- Badge status verifikasi berdasarkan is_verified -->
                                <?php if ($l['is_verified'] == 0): ?>
                                    <span class="badge-pending">⏳ Menunggu Verifikasi</span>
                                <?php elseif ($l['is_verified'] == 1): ?>
                                    <span class="badge-verified">✓ Terverifikasi</span>
                                <?php else: ?>
                                    <span class="badge-rejected">✗ Ditolak</span>
                                <?php endif; ?>
                            </div>
                            <div class="card-body">
                                <!-- Jenis Kerusakan -->
                                <div class="info-item">
                                    <div class="info-label">Jenis Kerusakan:</div>
                                    <div class="info-value"><?= $l['jenis_kerusakan'] ?></div>
                                </div>
                                <!-- Deskripsi (dipotong 80 karakter) -->
                                <div class="info-item">
                                    <div class="info-label">Deskripsi:</div>
                                    <div class="info-value"><?= substr($l['deskripsi'], 0, 80) ?>...</div>
                                </div>
                                <!-- Status Laporan dengan badge berwarna -->
                                <div class="info-item">
                                    <div class="info-label">Status:</div>
                                    <div class="info-value">
                                        <!-- Class dinamis berdasarkan status -->
                                        <span class="status-badge status-<?= strtolower(str_replace(' ', '-', $l['status'])) ?>">
                                            <?= $l['status'] ?>
                                        </span>
                                    </div>
                                </div>
                                <!-- Tanggal Lapor -->
                                <div class="info-item">
                                    <div class="info-label">Tanggal Lapor:</div>
                                    <div class="info-value"><?= date('d/m/Y H:i', strtotime($l['tgl_lapor'])) ?></div>
                                </div>
                                
                                <!-- ================================================
                                     INFO TAMBAHAN UNTUK LAPORAN TERVERIFIKASI
                                     ================================================ -->
                                <?php if ($l['is_verified'] == 1): ?>
                                    <div class="info-item">
                                        <div class="info-label">Verifikasi:</div>
                                        <div class="info-value">
                                            <small>Diverifikasi pada <?= date('d/m/Y H:i', strtotime($l['verified_at'])) ?></small>
                                        </div>
                                    </div>
                                <!-- ================================================
                                     INFO TAMBAHAN UNTUK LAPORAN DITOLAK
                                     ================================================ -->
                                <?php elseif ($l['is_verified'] == 2): ?>
                                    <div class="info-item">
                                        <div class="info-label">Alasan Ditolak:</div>
                                        <div class="info-value">
                                            <small class="text-danger"><?= $l['rejection_reason'] ?? 'Tidak memenuhi kriteria' ?></small>
                                        </div>
                                    </div>
                                <?php endif; ?>
                                
                                <!-- Jadwal Perbaikan (jika sudah dijadwalkan) -->
                                <?php if ($l['tgl_perbaikan_dijadwalkan']): ?>
                                    <div class="info-item">
                                        <div class="info-label">Jadwal Perbaikan:</div>
                                        <div class="info-value"><?= date('d/m/Y', strtotime($l['tgl_perbaikan_dijadwalkan'])) ?></div>
                                    </div>
                                <?php endif; ?>
                                
                                <!-- Catatan dari Admin (jika ada) -->
                                <?php if ($l['catatan_admin']): ?>
                                    <div class="info-item">
                                        <div class="info-label">Catatan Admin:</div>
                                        <div class="info-value"><small><?= $l['catatan_admin'] ?></small></div>
                                    </div>
                                <?php endif; ?>
                                
                                <!-- ================================================
                                     TOMBOL LIHAT DI PETA
                                     Hanya tampil untuk laporan yang sudah terverifikasi
                                     ================================================ -->
                                <?php if ($l['is_verified'] == 1): ?>
                                    <div class="text-center mt-3">
                                        <a href="<?= base_url('laporan?id=' . $l['id']) ?>" class="btn-peta">
                                            🗺️ Lihat di Peta
                                        </a>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <!-- ================================================
             PESAN JIKA TIDAK ADA LAPORAN
             ================================================ -->
        <?php else: ?>
            <div class="text-center" style="padding: 60px 20px;">
                <div style="font-size: 4rem; margin-bottom: 20px;">📭</div>
                <h4>Tidak Ada Laporan</h4>
                <p class="text-muted">Anda belum pernah membuat laporan dengan email ini.</p>
                <a href="<?= base_url('laporan/tambah') ?>" class="btn btn-kembali" style="margin-top: 20px;">
                    + Buat Laporan Baru
                </a>
            </div>
        <?php endif; ?>
    </div>

    <!-- Bootstrap JS: Untuk komponen interaktif -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
</body>
</html>
