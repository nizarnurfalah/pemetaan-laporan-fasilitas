<!--
============================================================================
VIEW DETAIL LAPORAN - HALAMAN UNTUK MELIHAT DETAIL LAPORAN
============================================================================

File ini adalah tampilan untuk menampilkan detail lengkap sebuah laporan.
Fitur yang ditampilkan:
1. Informasi laporan (jenis kerusakan, status, deskripsi)
2. Tanggal lapor dan tanggal perbaikan dijadwalkan (jika ada)
3. Catatan dari admin (jika ada)
4. Foto lokasi kerusakan
5. Peta lokasi dengan marker

Data yang dikirim dari Controller:
- $laporan: Array berisi data laporan dari database
- $is_reporter: Boolean untuk menentukan apakah pengakses adalah pelapor

Controller: Laporan::detail($id)
URL: /laporan/detail/{id}

Author: [Nama Anda]
============================================================================
-->

<!DOCTYPE html>
<html lang="id">
<head>
    <!-- Meta tag untuk encoding dan responsif -->
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Laporan - WebGIS Fasilitas Umum</title>
    
    <!-- CSS Libraries -->
    <!-- Bootstrap CSS: Framework tampilan -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" />
    <!-- Leaflet CSS: Styling peta -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.9.4/leaflet.min.css" />
    
    <!-- Custom CSS -->
    <style>
        /* Styling body dengan animasi fade in */
        body {
            background: #f5f5f5;
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
        }
        
        .navbar-brand {
            font-weight: bold;
            color: white !important;
        }
        
        /* Styling card (container utama) */
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
        
        /* Section informasi laporan */
        .info-section {
            padding: 20px 0;
            border-bottom: 1px solid #f0f0f0;
        }
        
        .info-section:last-child {
            border-bottom: none;
        }
        
        /* Label informasi */
        .info-label {
            font-weight: bold;
            color: #666;
            margin-bottom: 8px;
        }
        
        /* Nilai informasi */
        .info-value {
            color: #333;
            font-size: 15px;
        }
        
        /* Badge status laporan */
        .status-badge {
            display: inline-block;
            padding: 8px 16px;
            border-radius: 20px;
            font-size: 13px;
            font-weight: bold;
        }
        
        /* Warna badge berdasarkan status */
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
        
        /* Container foto */
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
        
        /* Container peta */
        #map {
            width: 100%;
            height: 400px;
            border-radius: 8px;
            margin: 20px 0;
        }
        
        /* Tombol kembali */
        .btn-kembali {
            background: #777;
            border: none;
            color: white;
            font-weight: bold;
            padding: 12px 30px;
            border-radius: 8px;
            text-decoration: none;
            display: inline-block;
            transition: transform 0.3s ease, background 0.3s ease;
        }
        
        .btn-kembali:hover {
            background: #666;
            color: white;
            text-decoration: none;
            transform: translateY(-2px);
        }
        
        /* Group tombol aksi */
        .button-group {
            display: flex;
            gap: 10px;
            margin-top: 20px;
        }
        
        /* ================================================
           RESPONSIVE - Tampilan untuk layar kecil (mobile)
           ================================================ */
        @media (max-width: 768px) {
            .button-group {
                flex-direction: column;
            }
            
            .btn-kembali, .btn-edit {
                width: 100%;
                text-align: center;
            }
        }
    </style>
</head>
<body>
    <!-- ================================================
         NAVBAR - Header navigasi
         ================================================ -->
    <nav class="navbar navbar-expand-lg mb-4">
        <div class="container">
            <!-- Logo dan nama aplikasi -->
            <a class="navbar-brand" href="<?= base_url('/') ?>">🗺️ WebGIS Fasilitas Umum</a>
            <!-- Tombol kembali ke peta -->
            <a href="<?= base_url('/') ?>" class="btn btn-light btn-sm">← Kembali ke Peta</a>
        </div>
    </nav>

    <!-- ================================================
         MAIN CONTAINER - Konten utama halaman
         ================================================ -->
    <div class="container" style="max-width: 700px;">
        <div class="card">
            <!-- Header card dengan ID laporan -->
            <div class="card-header">
                <h4>📝 Detail Laporan #<?= $laporan['id'] ?></h4>
            </div>
            
            <div class="card-body p-4">
                <!-- ================================================
                     INFO EMAIL PELAPOR
                     Hanya ditampilkan jika pengakses adalah pelapor
                     $is_reporter diset di controller
                     ================================================ -->
                <?php if (isset($is_reporter) && $is_reporter): ?>
                <div class="info-section">
                    <div class="info-label">Email Anda</div>
                    <div class="info-value"><?= $laporan['email_pelapor'] ?></div>
                </div>
                <?php endif; ?>

                <!-- ================================================
                     INFO JENIS KERUSAKAN
                     ucfirst() = huruf pertama kapital
                     ================================================ -->
                <div class="info-section">
                    <div class="info-label">Jenis Kerusakan</div>
                    <div class="info-value"><?= ucfirst($laporan['jenis_kerusakan']) ?></div>
                </div>

                <!-- ================================================
                     INFO STATUS LAPORAN
                     Badge dengan warna berbeda sesuai status
                     ================================================ -->
                <div class="info-section">
                    <div class="info-label">Status</div>
                    <div class="info-value">
                        <!-- Class status dibuat dinamis dari nilai status -->
                        <span class="status-badge status-<?= strtolower(str_replace(' ', '-', $laporan['status'])) ?>">
                            <?= $laporan['status'] ?>
                        </span>
                    </div>
                </div>

                <!-- ================================================
                     INFO DESKRIPSI KERUSAKAN
                     nl2br() = konversi newline ke <br>
                     ================================================ -->
                <div class="info-section">
                    <div class="info-label">Deskripsi Kerusakan</div>
                <!-- ================================================
                     INFO DESKRIPSI KERUSAKAN
                     nl2br() = konversi newline ke <br>
                     ================================================ -->
                <div class="info-section">
                    <div class="info-label">Deskripsi Kerusakan</div>
                    <div class="info-value"><?= nl2br($laporan['deskripsi']) ?></div>
                </div>

                <!-- ================================================
                     INFO TANGGAL LAPOR
                     Menggunakan date() untuk format tanggal Indonesia
                     strtotime() = konversi string ke timestamp
                     ================================================ -->
                <div class="info-section">
                    <div class="info-label">Tanggal Lapor</div>
                    <div class="info-value"><?= date('d/m/Y H:i', strtotime($laporan['tgl_lapor'])) ?></div>
                </div>

                <!-- ================================================
                     INFO TANGGAL PERBAIKAN DIJADWALKAN
                     Hanya ditampilkan jika admin sudah set jadwal
                     ================================================ -->
                <?php if ($laporan['tgl_perbaikan_dijadwalkan']): ?>
                    <div class="info-section">
                        <div class="info-label">Tanggal Perbaikan Dijadwalkan</div>
                        <div class="info-value">
                            📅 <?= date('d/m/Y H:i', strtotime($laporan['tgl_perbaikan_dijadwalkan'])) ?>
                        </div>
                    </div>
                <?php endif; ?>

                <!-- ================================================
                     CATATAN DARI ADMIN
                     Hanya ditampilkan jika admin memberikan catatan
                     ================================================ -->
                <?php if ($laporan['catatan_admin']): ?>
                    <div class="info-section">
                        <div class="info-label">📋 Catatan dari Admin</div>
                        <div class="info-value" style="background: #f5f7fa; padding: 12px; border-radius: 8px; border-left: 4px solid #667eea;">
                            <?= nl2br($laporan['catatan_admin']) ?>
                        </div>
                    </div>
                <?php endif; ?>

                <!-- ================================================
                     FOTO LOKASI KERUSAKAN
                     Gambar yang diupload pelapor
                     Disimpan di folder uploads/
                     ================================================ -->
                <div class="info-section">
                    <div class="info-label">📸 Foto Lokasi</div>
                    <div class="foto-container">
                        <!-- Path gambar dari folder uploads -->
                        <img src="<?= base_url('uploads/' . $laporan['foto_lokasi']) ?>" alt="Foto Lokasi">
                    </div>
                </div>

                <!-- ================================================
                     PETA LOKASI KERUSAKAN
                     Menggunakan Leaflet.js untuk menampilkan marker
                     ================================================ -->
                <div class="info-section">
                    <div class="info-label">🗺️ Lokasi Kerusakan</div>
                    <!-- Container peta Leaflet -->
                    <div id="map"></div>
                    <!-- Info koordinat -->
                    <div style="margin-top: 15px; padding: 12px; background: #f5f7fa; border-radius: 8px; border-left: 4px solid #667eea;">
                        <strong>Koordinat:</strong> <?= $laporan['latitude'] ?>, <?= $laporan['longitude'] ?>
                    </div>
                </div>

                <!-- ================================================
                     TOMBOL AKSI
                     Tombol untuk kembali ke halaman peta utama
                     ================================================ -->
                <div class="button-group">
                    <a href="<?= base_url('/') ?>" class="btn-kembali">
                        ← Kembali ke Peta
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- ================================================
         JAVASCRIPT LIBRARIES
         ================================================ -->
    <!-- Leaflet.js: Library untuk peta interaktif -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.9.4/leaflet.min.js"></script>
    <!-- Bootstrap JS: Untuk komponen interaktif -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
    
    <!-- ================================================
         SCRIPT INISIALISASI PETA
         Menampilkan peta dengan marker lokasi kerusakan
         ================================================ -->
    <script>
        // ================================================
        // INISIALISASI PETA LEAFLET
        // Koordinat diambil dari data laporan di database
        // Zoom level 15 untuk tampilan detail
        // ================================================
        const map = L.map('map').setView([<?= $laporan['latitude'] ?>, <?= $laporan['longitude'] ?>], 15);
        
        // Tambahkan layer tile OpenStreetMap
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '© OpenStreetMap contributors',
            maxZoom: 19,
        }).addTo(map);
        
        // ================================================
        // TAMBAH MARKER DI LOKASI KERUSAKAN
        // Marker dengan popup info laporan
        // ================================================
        L.marker([<?= $laporan['latitude'] ?>, <?= $laporan['longitude'] ?>])
            .bindPopup('Lokasi Laporan #<?= $laporan['id'] ?>')  // Popup dengan ID laporan
            .addTo(map)
            .openPopup();  // Tampilkan popup secara default
    </script>
</body>
</html>
