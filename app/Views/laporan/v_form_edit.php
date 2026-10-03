<!--
============================================================================
VIEW FORM EDIT LAPORAN - HALAMAN UNTUK MENGEDIT LAPORAN YANG SUDAH ADA
============================================================================

File ini adalah tampilan form untuk pengguna mengedit laporan yang sudah dibuat.
Terdapat 2 tahap:
1. Form verifikasi email - untuk memastikan hanya pelapor asli yang bisa edit
2. Form edit laporan - setelah email terverifikasi

Fitur yang tersedia:
1. Verifikasi email sebelum edit
2. Update jenis kerusakan dan deskripsi
3. Ganti foto lokasi (opsional)
4. Update koordinat lokasi di peta

Data yang dikirim dari Controller:
- $laporan: Array berisi data laporan dari database
- $show_form: Boolean untuk menentukan form mana yang ditampilkan

Controller: Laporan::edit($id), Laporan::verify($id)
URL: /laporan/edit/{id}

Author: [Nama Anda]
============================================================================
-->

<!DOCTYPE html>
<html lang="id">
<head>
    <!-- Meta tag untuk encoding dan responsif -->
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Laporan - WebGIS Fasilitas Umum</title>
    
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
        
        /* Styling card (container form) */
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
        
        /* Styling form group */
        .form-group {
            margin-bottom: 20px;
        }
        
        /* Label form */
        .form-label {
            font-weight: bold;
            color: #333;
            margin-bottom: 8px;
        }
        
        /* Input form */
        .form-control, .form-select {
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            padding: 10px 15px;
            transition: all 0.3s ease;
            font-size: 14px;
        }
        
        /* Efek focus pada input */
        .form-control:focus, .form-select:focus {
            border-color: #666;
            box-shadow: 0 0 0 0.2rem rgba(100, 100, 100, 0.15);
        }
        
        /* Text bantuan di bawah input */
        .form-text {
            font-size: 12px;
            color: #666;
        }
        
        /* Container peta */
        #map {
            width: 100%;
            height: 300px;
            border-radius: 8px;
            margin-top: 10px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }
        
        /* Tombol submit */
        .btn-submit {
            background: #666;
            border: none;
            color: white;
            font-weight: bold;
            padding: 12px 30px;
            border-radius: 8px;
            transition: transform 0.3s ease, background 0.3s ease;
        }
        
        .btn-submit:hover {
            background: #555;
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
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
        }
        
        .btn-kembali:hover {
            background: #666;
            color: white;
            text-decoration: none;
        }
        
        /* Alert message */
        .alert {
            border-radius: 8px;
            border: none;
            margin-bottom: 20px;
        }
        
        /* Preview foto baru yang akan diupload */
        .preview-foto {
            margin-top: 10px;
            max-width: 100%;
            max-height: 200px;
            border-radius: 8px;
            display: none;
        }
        
        /* Foto yang sudah ada (saat ini) */
        .current-foto {
            margin: 10px 0;
            max-width: 100%;
            max-height: 200px;
            border-radius: 8px;
        }
        
        /* Box info koordinat */
        .coordinates-info {
            background: #efefef;
            padding: 12px;
            border-radius: 8px;
            margin-top: 10px;
            border-left: 4px solid #666;
            font-size: 14px;
        }
        
        .coordinates-info strong {
            color: #333;
        }
        
        /* Tanda field wajib diisi */
        .required {
            color: #dc3545;
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
    <div class="container" style="max-width: 600px;">
        <!-- Flash message error jika ada -->
        <?php if (session()->getFlashdata('error')): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <?= session()->getFlashdata('error') ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <div class="card">
            <!-- Header card dengan ID laporan -->
            <div class="card-header">
                <h4>✏️ Edit Laporan #<?= $laporan['id'] ?></h4>
                <p class="mb-0 mt-2" style="font-size: 13px; opacity: 0.9;">Update data laporan kerusakan Anda</p>
            </div>
            
            <div class="card-body p-4">
                <!-- ================================================
                     KONDISI 1: FORM VERIFIKASI EMAIL
                     Ditampilkan jika user belum terverifikasi
                     $show_form = false atau tidak diset
                     ================================================ -->
                <?php if (!isset($show_form) || !$show_form): ?>
                    <!-- Pesan informasi tentang verifikasi -->
                    <div class="alert alert-info" role="alert">
                        🔒 <strong>Verifikasi Email Diperlukan</strong><br>
                        Untuk keamanan, silakan masukkan email yang sesuai dengan laporan sebelum mengedit.
                    </div>

                    <!-- Form untuk input email verifikasi -->
                    <form method="POST" action="<?= base_url('laporan/verify/' . $laporan['id']) ?>">
                        <?= csrf_field() ?> <!-- Token CSRF untuk keamanan -->
                        
                        <div class="form-group">
                            <label class="form-label">Email Pelapor <span class="required">*</span></label>
                            <input type="email" class="form-control" name="email_pelapor" 
                                   placeholder="Masukkan email yang sesuai" required autofocus>
                            <small class="form-text d-block mt-2">Masukkan email yang digunakan saat membuat laporan ini.</small>
                        </div>

                        <!-- Tombol aksi -->
                        <div class="d-flex gap-2 mt-4">
                            <button type="submit" class="btn btn-submit flex-grow-1">
                                ✓ Verifikasi Email
                            </button>
                            <a href="<?= base_url('/') ?>" class="btn btn-kembali">← Batal</a>
                        </div>
                    </form>
                    
                <!-- ================================================
                     KONDISI 2: FORM EDIT LAPORAN
                     Ditampilkan setelah email terverifikasi
                     $show_form = true
                     ================================================ -->
                <?php else: ?>
                    <!-- Pesan sukses verifikasi -->
                    <div class="alert alert-success" role="alert">
                        ✓ Email terverifikasi! Anda dapat mengedit laporan.
                    </div>

                    <!-- Form untuk edit data laporan -->
                    <form method="POST" action="<?= base_url('laporan/update/' . $laporan['id']) ?>" enctype="multipart/form-data">
                        <?= csrf_field() ?> <!-- Token CSRF untuk keamanan -->
                    
                    <!-- ================================================
                         INPUT EMAIL PELAPOR
                         Sudah terisi dari data laporan
                         ================================================ -->
                    <div class="form-group">
                        <label class="form-label">Email <span class="required">*</span></label>
                        <input type="email" class="form-control" name="email_pelapor" 
                               placeholder="nama@example.com" required 
                               value="<?= $laporan['email_pelapor'] ?>">
                    </div>

                    <!-- ================================================
                         DROPDOWN JENIS KERUSAKAN
                         Selected sesuai data laporan
                         ================================================ -->
                    <div class="form-group">
                        <label class="form-label">Jenis Kerusakan <span class="required">*</span></label>
                        <select class="form-select" name="jenis_kerusakan" required>
                            <option value="">-- Pilih Jenis Kerusakan --</option>
                            <option value="Jalan Berlubang" <?= $laporan['jenis_kerusakan'] === 'Jalan Berlubang' ? 'selected' : '' ?>>Jalan Berlubang</option>
                            <option value="Fasilitas Publik Rusak" <?= $laporan['jenis_kerusakan'] === 'Fasilitas Publik Rusak' ? 'selected' : '' ?>>Fasilitas Publik Rusak</option>
                        </select>
                    </div>

                    <!-- ================================================
                         TEXTAREA DESKRIPSI KERUSAKAN
                         Terisi dari data laporan sebelumnya
                         ================================================ -->
                    <div class="form-group">
                        <label class="form-label">Deskripsi Kerusakan <span class="required">*</span></label>
                        <textarea class="form-control" name="deskripsi" rows="4" 
                                  placeholder="Jelaskan detail kerusakan yang Anda temukan..."
                                  required><?= $laporan['deskripsi'] ?></textarea>
                    </div>

                    <!-- ================================================
                         INPUT FILE FOTO LOKASI (OPSIONAL)
                         Menampilkan foto saat ini dan opsi untuk mengganti
                         ================================================ -->
                    <div class="form-group">
                        <label class="form-label">Foto Lokasi</label>
                        <p style="font-size: 13px; color: #666;">Foto Saat Ini:</p>
                        <!-- Tampilkan foto yang sudah ada -->
                        <img src="<?= base_url('uploads/' . $laporan['foto_lokasi']) ?>" alt="Foto Saat Ini" class="current-foto">
                        <div style="margin-top: 10px;">
                            <label class="form-label">Ganti Foto (Opsional)</label>
                            <!-- Input file untuk foto baru -->
                            <input type="file" class="form-control" name="foto_lokasi" 
                                   accept="image/jpeg,image/png,image/gif,image/webp" onchange="previewFoto(event)">
                            <small class="form-text">✓ Format: JPG, PNG, GIF, WebP | ✓ Ukuran maksimal: 5MB</small>
                            <!-- Preview foto baru yang dipilih -->
                            <img id="preview-foto" class="preview-foto" alt="Preview">
                        </div>
                    </div>

                    <!-- ================================================
                         MAP LEAFLET - Peta untuk update lokasi
                         Marker awal di koordinat laporan sebelumnya
                         ================================================ -->
                    <div class="form-group">
                        <label class="form-label">Update Lokasi di Peta <span class="required">*</span></label>
                        <div id="map"></div>
                        <small class="form-text d-block mt-2">Klik pada peta untuk mengubah lokasi kerusakan</small>
                        <!-- Display koordinat yang dipilih -->
                        <div class="coordinates-info">
                            <strong>Koordinat:</strong> 
                            Lat: <span id="latitude-display"><?= $laporan['latitude'] ?></span> | 
                            Lon: <span id="longitude-display"><?= $laporan['longitude'] ?></span>
                        </div>
                    </div>

                    <!-- ================================================
                         HIDDEN INPUT UNTUK KOORDINAT
                         Diupdate saat user klik peta
                         ================================================ -->
                    <input type="hidden" name="latitude" id="latitude" value="<?= $laporan['latitude'] ?>" required>
                    <input type="hidden" name="longitude" id="longitude" value="<?= $laporan['longitude'] ?>" required>

                    <!-- ================================================
                         TOMBOL AKSI
                         ================================================ -->
                    <div class="d-flex gap-2 mt-4">
                        <button type="submit" class="btn btn-submit flex-grow-1">
                            ✓ Update Laporan
                        </button>
                        <a href="<?= base_url('laporan/detail/' . $laporan['id']) ?>" class="btn btn-kembali">← Batal</a>
                    </div>
                </form>
            <?php endif; ?>
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
         SCRIPT UTAMA - Preview Foto & Peta Leaflet
         ================================================ -->
    <script>
        // ================================================
        // PREVIEW FOTO BARU SEBELUM UPLOAD
        // Menampilkan preview gambar yang akan diupload
        // ================================================
        function previewFoto(event) {
            const file = event.target.files[0];  // Ambil file yang dipilih
            const preview = document.getElementById('preview-foto');
            
            if (file) {
                // Gunakan FileReader untuk membaca file sebagai Data URL
                const reader = new FileReader();
                reader.onload = function(e) {
                    preview.src = e.target.result;  // Set src gambar preview
                    preview.style.display = 'block';  // Tampilkan preview
                };
                reader.readAsDataURL(file);  // Mulai membaca file
            }
        }
        
        // ================================================
        // INISIALISASI PETA LEAFLET
        // Koordinat awal diambil dari data laporan di database
        // Zoom level 12 untuk tampilan yang cukup luas
        // ================================================
        const map = L.map('map').setView([<?= $laporan['latitude'] ?>, <?= $laporan['longitude'] ?>], 12);
        
        // Tambahkan layer tile OpenStreetMap
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '© OpenStreetMap contributors',
            maxZoom: 19,
        }).addTo(map);
        
        // Variable untuk menyimpan marker
        let marker = null;
        
        // ================================================
        // EVENT LISTENER KLIK PETA
        // Saat user klik di peta, pindahkan marker ke posisi baru
        // dan update semua input koordinat
        // ================================================
        map.on('click', function(e) {
            // Format koordinat dengan 8 digit desimal
            const lat = e.latlng.lat.toFixed(8);
            const lng = e.latlng.lng.toFixed(8);
            
            // Hapus marker lama jika ada
            if (marker) {
                map.removeLayer(marker);
            }
            
            // Buat marker baru dengan popup info
            marker = L.marker([lat, lng]).addTo(map)
                .bindPopup(`Lokasi Baru<br>Lat: ${lat}<br>Lon: ${lng}`)
                .openPopup();
            
            // Update hidden inputs (nilai yang dikirim ke server)
            document.getElementById('latitude').value = lat;
            document.getElementById('longitude').value = lng;
            
            // Update tampilan display koordinat
            document.getElementById('latitude-display').textContent = lat;
            document.getElementById('longitude-display').textContent = lng;
        });
        
        // ================================================
        // SET MARKER AWAL
        // Marker di posisi koordinat laporan saat ini
        // ================================================
        marker = L.marker([<?= $laporan['latitude'] ?>, <?= $laporan['longitude'] ?>]).addTo(map)
            .bindPopup('Lokasi Saat Ini - Klik peta untuk mengubah')
            .openPopup();
    </script>
</body>
</html>
