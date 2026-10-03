<!--
============================================================================
VIEW FORM TAMBAH LAPORAN - HALAMAN UNTUK MEMBUAT LAPORAN BARU
============================================================================

File ini adalah tampilan form untuk pengguna (guest) membuat laporan baru.
Fitur yang tersedia:
1. Form input data laporan (email, jenis kerusakan, deskripsi, foto)
2. Peta interaktif untuk memilih lokasi kerusakan
3. Validasi reCAPTCHA untuk mencegah spam
4. Preview gambar sebelum upload
5. Deteksi lokasi GPS otomatis

Library yang digunakan:
- Bootstrap 5: Framework CSS
- Leaflet.js: Peta interaktif untuk memilih lokasi
- SweetAlert2: Popup notifikasi yang menarik
- Google reCAPTCHA: Verifikasi anti-bot

Controller: Laporan::tambah()
URL: /laporan/tambah

Author: [Nama Anda]
============================================================================
-->

<!DOCTYPE html>
<html lang="id">
<head>
    <!-- Meta tag untuk encoding dan responsif -->
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lapor Kerusakan - WebGIS Fasilitas Umum</title>
    
    <!-- CSS Libraries -->
    <!-- Bootstrap CSS: Framework tampilan -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" />
    <!-- Leaflet CSS: Styling peta -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.9.4/leaflet.min.css" />
    <!-- Google reCAPTCHA: Script untuk verifikasi anti-bot -->
    <script src="https://www.google.com/recaptcha/api.js" async defer></script>
    <!-- SweetAlert2: Library popup notifikasi -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
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
            border: 2px solid #ddd;
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
        
        /* Container peta untuk memilih lokasi */
        #map {
            width: 100%;
            height: 300px;
            border-radius: 8px;
            margin-top: 10px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }
        
        /* Tombol submit dengan efek hover */
        .btn-submit {
            background: #666;
            border: none;
            color: white;
            font-weight: bold;
            padding: 12px 30px;
            border-radius: 8px;
            transition: transform 0.3s ease, background 0.3s ease, opacity 0.3s ease;
        }
        
        /* Tombol disabled saat loading */
        .btn-submit:disabled {
            background: #ccc;
            color: #888;
            cursor: not-allowed;
            opacity: 0.6;
        }
        
        .btn-submit:hover:not(:disabled) {
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
        
        /* Tombol GPS untuk deteksi lokasi otomatis */
        .btn-gps {
            background: #666;
            border: none;
            color: white;
            font-weight: bold;
            padding: 10px 20px;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.3s ease;
            font-size: 14px;
        }
        
        .btn-gps:hover {
            background: #555;
            transform: translateY(-2px);
            box-shadow: 0 3px 10px rgba(0,0,0,0.15);
        }
        
        /* Styling status GPS */
        .btn-gps:disabled {
            background: #aaa;
            cursor: not-allowed;
            transform: none;
        }
        
        /* Alert error/success message */
        .alert {
            border-radius: 8px;
            border: none;
            margin-bottom: 20px;
        }
        
        /* Preview foto sebelum upload */
        .preview-foto {
            margin-top: 10px;
            max-width: 100%;
            max-height: 200px;
            border-radius: 8px;
            display: none;
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
        
        /* Box input koordinat manual */
        .coordinates-inputs {
            background: #efefef;
            padding: 15px;
            border-radius: 8px;
            margin-top: 15px;
            border-left: 4px solid #666;
        }
        
        .coordinates-inputs .form-group {
            margin-bottom: 10px;
        }
        
        .coordinates-inputs label {
            font-size: 13px;
            font-weight: 600;
            color: #333;
        }
        
        .coordinates-inputs input {
            font-size: 13px;
            padding: 8px 12px;
        }
        
        /* Status GPS */
        .gps-status {
            font-size: 12px;
            margin-top: 5px;
            color: #666;
        }
        
        .gps-status.success {
            color: #388e3c;
        }
        
        .gps-status.error {
            color: #d32f2f;
        }
        
        /* Tanda wajib diisi */
        .required {
            color: #dc3545;
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
            
            .card-header {
                padding: 15px;
            }
            
            .card-header h4 {
                font-size: 1.2rem;
            }
            
            .card-body {
                padding: 15px;
            }
            
            .form-label {
                font-size: 14px;
            }
            
            .btn-submit,
            .btn-kembali,
            .btn-gps {
                padding: 10px 20px;
                font-size: 14px;
                width: 100%;
                margin: 5px 0;
            }
            
            .coordinates-inputs .row {
                margin: 0;
            }
            
            .coordinates-inputs .col-md-6 {
                padding: 0 0 10px 0;
            }
            
            /* Tinggi peta lebih kecil di mobile */
            #map {
                height: 250px;
            }
        }
    </style>
</head>
<body>
    <!-- ================================================
         NAVBAR - Header navigasi
         Berisi judul aplikasi dan tombol kembali ke peta
         ================================================ -->
    <nav class="navbar navbar-expand-lg mb-4">
        <div class="container">
            <!-- Logo dan nama aplikasi, link ke halaman utama -->
            <a class="navbar-brand" href="<?= base_url('/') ?>">🗺️ WebGIS Fasilitas Umum</a>
            <!-- Tombol untuk kembali ke halaman peta utama -->
            <a href="<?= base_url('/') ?>" class="btn btn-light btn-sm">← Kembali ke Peta</a>
        </div>
    </nav>

    <!-- ================================================
         MAIN CONTAINER - Konten utama halaman
         ================================================ -->
    <div class="container" style="max-width: 600px;">
        <!-- 
        ================================================
        FLASH MESSAGE ERROR
        Menampilkan pesan error jika ada dari session
        ================================================ 
        -->
        <?php if (session()->getFlashdata('error')): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <?= session()->getFlashdata('error') ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <!-- ================================================
             CARD FORM LAPORAN
             Container utama untuk form input laporan
             ================================================ -->
        <div class="card">
            <!-- Header card dengan judul form -->
            <div class="card-header">
                <h4>📝 Lapor Kerusakan</h4>
                <p class="mb-0 mt-2" style="font-size: 13px; opacity: 0.9;">Bantu kami perbaiki fasilitas umum yang rusak</p>
            </div>
            
            <!-- ================================================
                 BODY CARD - Isi form laporan
                 ================================================ -->
            <div class="card-body p-4">
                <!-- 
                Form laporan dengan method POST
                action: mengirim ke Laporan::simpan()
                enctype: multipart untuk upload file gambar
                -->
                <form method="POST" action="<?= base_url('laporan/simpan') ?>" enctype="multipart/form-data">
                    <!-- ================================================
                         INPUT EMAIL PELAPOR
                         Email digunakan untuk verifikasi dan notifikasi
                         ================================================ -->
                    <div class="form-group">
                        <label class="form-label">Email <span class="required">*</span></label>
                        <input type="email" class="form-control" name="email_pelapor" 
                               placeholder="nama@example.com" required 
                               value="<?= old('email_pelapor') ?>">
                        <!-- old() = mengambil value sebelumnya jika validasi gagal -->
                        <small class="form-text">📧 Email akan digunakan untuk notifikasi status laporan Anda</small>
                    </div>

                    <!-- ================================================
                         DROPDOWN JENIS KERUSAKAN
                         Pilihan: Jalan Berlubang atau Fasilitas Publik Rusak
                         ================================================ -->
                    <div class="form-group">
                        <label class="form-label">Jenis Kerusakan <span class="required">*</span></label>
                        <select class="form-select" name="jenis_kerusakan" required>
                            <option value="">-- Pilih Jenis Kerusakan --</option>
                            <!-- Mengecek nilai sebelumnya untuk mempertahankan pilihan jika validasi gagal -->
                            <option value="Jalan Berlubang" <?= old('jenis_kerusakan') === 'Jalan Berlubang' ? 'selected' : '' ?>>Jalan Berlubang</option>
                            <option value="Fasilitas Publik Rusak" <?= old('jenis_kerusakan') === 'Fasilitas Publik Rusak' ? 'selected' : '' ?>>Fasilitas Publik Rusak</option>
                        </select>
                    </div>

                    <!-- ================================================
                         TEXTAREA DESKRIPSI KERUSAKAN
                         Penjelasan detail mengenai kerusakan yang ditemukan
                         ================================================ -->
                    <div class="form-group">
                        <label class="form-label">Deskripsi Kerusakan <span class="required">*</span></label>
                        <textarea class="form-control" name="deskripsi" rows="4" 
                                  placeholder="Jelaskan detail kerusakan yang Anda temukan..."
                                  required><?= old('deskripsi') ?></textarea>
                    </div>

                    <!-- ================================================
                         INPUT FILE FOTO LOKASI
                         Upload gambar kerusakan sebagai bukti
                         Format yang diterima: JPG, PNG, GIF, WebP
                         ================================================ -->
                    <div class="form-group">
                        <label class="form-label">Foto Lokasi <span class="required">*</span></label>
                        <input type="file" class="form-control" name="foto_lokasi" 
                               accept="image/jpeg,image/png,image/gif,image/webp" required onchange="previewFoto(event)">
                        <!-- onchange: memanggil fungsi previewFoto() saat file dipilih -->
                        <small class="form-text">✓ Format: JPG, PNG, GIF, WebP | ✓ Ukuran maksimal: 5MB</small>
                        <!-- Preview gambar yang akan diupload -->
                        <img id="preview-foto" class="preview-foto" alt="Preview">
                    </div>

                    <!-- ================================================
                         MAP LEAFLET - Peta untuk memilih lokasi kerusakan
                         Pengguna bisa:
                         1. Klik di peta untuk set koordinat
                         2. Gunakan GPS untuk deteksi otomatis
                         3. Input manual koordinat latitude/longitude
                         ================================================ -->
                    <div class="form-group">
                        <label class="form-label">Pilih Lokasi di Peta <span class="required">*</span></label>
                        <!-- Container peta Leaflet -->
                        <div id="map"></div>
                        <small class="form-text d-block mt-2">Klik pada peta untuk menandai lokasi kerusakan, atau gunakan tombol GPS</small>
                        
                        <!-- Display koordinat yang dipilih -->
                        <div class="coordinates-info">
                            <strong>Koordinat:</strong> 
                            Lat: <span id="latitude-display">-6.2088</span> | 
                            Lon: <span id="longitude-display">106.8456</span>
                        </div>
                        
                        <!-- ================================================
                             GPS BUTTON & INPUT KOORDINAT MANUAL
                             ================================================ -->
                        <div class="coordinates-inputs">
                            <!-- Tombol untuk mendapatkan lokasi dari GPS device -->
                            <div class="d-grid gap-2">
                                <button type="button" class="btn-gps" id="gps-button">📍 Gunakan Lokasi Saya</button>
                                <!-- Status hasil deteksi GPS -->
                                <div class="gps-status" id="gps-status"></div>
                            </div>
                            
                            <!-- Input manual koordinat -->
                            <div class="row mt-3">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="lat-input">Latitude</label>
                                        <input type="text" class="form-control" id="lat-input" placeholder="-6.9175" value="-6.9175">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="lon-input">Longitude</label>
                                        <input type="text" class="form-control" id="lon-input" placeholder="107.6062" value="107.6062">
                                    </div>
                                </div>
                            </div>
                            <small class="form-text">💡 Anda bisa klik di peta, input manual, atau gunakan GPS untuk set lokasi</small>
                        </div>
                    </div>

                    <!-- ================================================
                         HIDDEN INPUT UNTUK KOORDINAT
                         Nilai latitude dan longitude yang akan dikirim ke server
                         Diupdate otomatis saat user memilih lokasi di peta
                         ================================================ -->
                    <input type="hidden" name="latitude" id="latitude" value="-6.9175" required>
                    <input type="hidden" name="longitude" id="longitude" value="107.6062" required>

                    <!-- ================================================
                         GOOGLE reCAPTCHA
                         Widget verifikasi untuk mencegah spam/bot
                         data-sitekey: kunci publik reCAPTCHA (ini key testing)
                         data-callback: fungsi yang dipanggil setelah verifikasi berhasil
                         ================================================ -->
                    <div class="form-group mt-3">
                        <div class="g-recaptcha" data-sitekey="6LeIxAcTAAAAAJcZVRqyHh71UMIEGNQ_MXjiZKhI" data-callback="enableSubmit"></div>
                        <small class="form-text text-muted">⚠️ Harap centang kotak di atas untuk memverifikasi bahwa Anda bukan robot</small>
                    </div>

                    <!-- ================================================
                         TOMBOL AKSI
                         Submit: Kirim laporan (disabled sampai reCAPTCHA diisi)
                         Batal: Kembali ke halaman peta utama
                         ================================================ -->
                    <div class="d-flex gap-2 mt-4">
                        <!-- Tombol submit disabled by default, enabled setelah reCAPTCHA -->
                        <button type="submit" id="submitBtn" class="btn btn-submit flex-grow-1" disabled>
                            ✓ Kirim Laporan
                        </button>
                        <a href="<?= base_url('/') ?>" class="btn btn-kembali">← Batal</a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- ================================================
         JAVASCRIPT LIBRARIES
         ================================================ -->
    <!-- Leaflet.js: Library untuk peta interaktif -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.9.4/leaflet.min.js"></script>
    <!-- Bootstrap JS: Untuk komponen interaktif (modal, dropdown, dll) -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
    
    <!-- ================================================
         SCRIPT UNTUK FLASH MESSAGE & FORM HANDLING
         Menampilkan notifikasi sukses/error dengan SweetAlert2
         ================================================ -->
    <script>
        // Event listener saat DOM sudah siap
        document.addEventListener('DOMContentLoaded', function() {
            // Ambil elemen form
            const form = document.querySelector('form');
            
            // ================================================
            // CEK FLASH MESSAGE SUKSES
            // Jika ada flash message success dari server, tampilkan popup
            // ================================================
            <?php if(session()->getFlashdata('success')): ?>
                Swal.fire({
                    title: '✓ Berhasil!',
                    text: '<?= session()->getFlashdata('success') ?>',
                    icon: 'success',
                    confirmButtonText: 'Kembali ke Peta',
                    confirmButtonColor: '#636e72',
                    allowOutsideClick: false,  // Tidak bisa tutup dengan klik luar
                    allowEscapeKey: false      // Tidak bisa tutup dengan tombol ESC
                }).then((result) => {
                    // Setelah user klik tombol, redirect ke halaman utama
                    if (result.isConfirmed) {
                        window.location.href = '<?= base_url('/') ?>';
                    }
                });
            <?php endif; ?>
            
            // ================================================
            // CEK FLASH MESSAGE ERROR
            // Jika ada flash message error dari server, tampilkan popup
            // ================================================
            <?php if(session()->getFlashdata('error')): ?>
                Swal.fire({
                    title: '❌ Gagal!',
                    text: '<?= session()->getFlashdata('error') ?>',
                    icon: 'error',
                    confirmButtonText: 'Coba Lagi',
                    confirmButtonColor: '#636e72'
                });
            <?php endif; ?>
            
            // ================================================
            // LOADING STATE SAAT SUBMIT FORM
            // Menampilkan indikator loading pada tombol submit
            // ================================================
            if (form) {
                form.addEventListener('submit', function(e) {
                    // Biarkan form submit normal (tidak prevent default)
                    // Hanya tampilkan loading indicator
                    const submitBtn = document.getElementById('submitBtn');
                    if (submitBtn) {
                        const originalText = submitBtn.textContent;
                        submitBtn.disabled = true;
                        submitBtn.innerHTML = '⏳ Sedang mengirim...';
                        
                        // Reset button setelah 3 detik jika submit gagal
                        setTimeout(() => {
                            submitBtn.disabled = false;
                            submitBtn.textContent = originalText;
                        }, 3000);
                    }
                });
            }
        });
    </script>
    
    <!-- ================================================
         SCRIPT UTAMA - reCAPTCHA, Preview Foto, Peta Leaflet
         ================================================ -->
    <script>
        // ================================================
        // CALLBACK reCAPTCHA
        // Fungsi ini dipanggil otomatis setelah user berhasil
        // mencentang checkbox reCAPTCHA
        // ================================================
        function enableSubmit() {
            // Enable tombol submit
            document.getElementById('submitBtn').disabled = false;
            document.getElementById('submitBtn').style.opacity = '1';
            document.getElementById('submitBtn').style.cursor = 'pointer';
        }

        // ================================================
        // PREVIEW FOTO SEBELUM UPLOAD
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
        // Membuat peta interaktif untuk memilih lokasi kerusakan
        // Default view: Bandung (-6.9175, 107.6062) dengan zoom 13
        // ================================================
        const map = L.map('map').setView([-6.9175, 107.6062], 13);
        
        // Tambahkan layer tile OpenStreetMap
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '© OpenStreetMap contributors',
            maxZoom: 19,
        }).addTo(map);
        
        // Variable untuk menyimpan marker lokasi
        let marker = null;
        
        // ================================================
        // FUNGSI UPDATE MARKER
        // Dipanggil saat user memilih lokasi baru
        // - Hapus marker lama
        // - Buat marker baru di koordinat yang dipilih
        // - Update semua input terkait koordinat
        // ================================================
        function updateMarker(lat, lng) {
            // Format koordinat dengan 8 digit desimal
            lat = parseFloat(lat).toFixed(8);
            lng = parseFloat(lng).toFixed(8);
            
            // Hapus marker lama jika ada
            if (marker) {
                map.removeLayer(marker);
            }
            
            // Buat marker baru dengan popup info
            marker = L.marker([lat, lng]).addTo(map)
                .bindPopup(`Lokasi Laporan<br>Lat: ${lat}<br>Lon: ${lng}`)
                .openPopup();
            
            // Update hidden inputs (nilai yang dikirim ke server)
            document.getElementById('latitude').value = lat;
            document.getElementById('longitude').value = lng;
            
            // Update input manual koordinat
            document.getElementById('lat-input').value = lat;
            document.getElementById('lon-input').value = lng;
            
            // Update tampilan display koordinat
            document.getElementById('latitude-display').textContent = lat;
            document.getElementById('longitude-display').textContent = lng;
        }
        
        // ================================================
        // EVENT LISTENER KLIK PETA
        // Saat user klik di peta, set marker di posisi tersebut
        // ================================================
        map.on('click', function(e) {
            const lat = e.latlng.lat;
            const lng = e.latlng.lng;
            updateMarker(lat, lng);
        });
        
        // Set marker awal di Bandung (default)
        updateMarker(-6.9175, 107.6062);
        
        // ================================================
        // GPS BUTTON - DETEKSI LOKASI OTOMATIS
        // Menggunakan Geolocation API browser untuk mendapat lokasi
        // ================================================
        document.getElementById('gps-button').addEventListener('click', function() {
            const btn = this;
            const statusDiv = document.getElementById('gps-status');
            
            // Disable button dan tampilkan loading
            btn.disabled = true;
            btn.textContent = '⏳ Sedang mendapat lokasi...';
            statusDiv.textContent = '';
            statusDiv.className = 'gps-status';
            
            // Cek apakah browser support Geolocation API
            if (!navigator.geolocation) {
                statusDiv.textContent = '❌ Browser Anda tidak mendukung GPS';
                statusDiv.className = 'gps-status error';
                btn.disabled = false;
                btn.textContent = '📍 Gunakan Lokasi Saya';
                return;
            }
            
            // Request lokasi dari device
            navigator.geolocation.getCurrentPosition(
                // SUCCESS CALLBACK - Lokasi berhasil didapat
                function(position) {
                    const lat = position.coords.latitude;
                    const lng = position.coords.longitude;
                    const accuracy = position.coords.accuracy;  // Akurasi dalam meter
                    
                    // Update marker dan pindah view peta ke lokasi
                    updateMarker(lat, lng);
                    map.setView([lat, lng], 15);  // Zoom lebih dekat
                    
                    // Tampilkan status sukses dengan info akurasi
                    statusDiv.textContent = `✓ Lokasi didapat (akurasi: ±${Math.round(accuracy)}m)`;
                    statusDiv.className = 'gps-status success';
                    
                    btn.disabled = false;
                    btn.textContent = '📍 Gunakan Lokasi Saya';
                },
                // ERROR CALLBACK - Terjadi error saat mendapat lokasi
                function(error) {
                    let errorMsg = '';
                    // Tentukan pesan error berdasarkan kode error
                    switch(error.code) {
                        case error.PERMISSION_DENIED:
                            errorMsg = '❌ Izin GPS ditolak. Silakan aktifkan GPS di pengaturan browser.';
                            break;
                        case error.POSITION_UNAVAILABLE:
                            errorMsg = '❌ Informasi lokasi tidak tersedia';
                            break;
                        case error.TIMEOUT:
                            errorMsg = '❌ Request GPS timeout';
                            break;
                        default:
                            errorMsg = '❌ Error: ' + error.message;
                    }
                    
                    statusDiv.textContent = errorMsg;
                    statusDiv.className = 'gps-status error';
                    
                    btn.disabled = false;
                    btn.textContent = '📍 Gunakan Lokasi Saya';
                },
                // OPTIONS - Pengaturan Geolocation
                {
                    enableHighAccuracy: true,  // Gunakan GPS akurasi tinggi
                    timeout: 10000,            // Timeout 10 detik
                    maximumAge: 0              // Jangan gunakan cache lokasi
                }
            );
        });
        
        // ================================================
        // INPUT MANUAL LATITUDE
        // User bisa mengetik koordinat latitude secara manual
        // ================================================
        document.getElementById('lat-input').addEventListener('input', function() {
            const lat = this.value.trim();
            const lng = document.getElementById('lon-input').value.trim();
            
            if (lat && lng) {
                const latNum = parseFloat(lat);
                const lngNum = parseFloat(lng);
                
                // Validasi range koordinat yang valid
                // Latitude: -90 sampai 90
                // Longitude: -180 sampai 180
                if (!isNaN(latNum) && !isNaN(lngNum) && 
                    latNum >= -90 && latNum <= 90 && 
                    lngNum >= -180 && lngNum <= 180) {
                    updateMarker(latNum, lngNum);
                    map.setView([latNum, lngNum], 15);
                }
            }
        });
        
        // ================================================
        // INPUT MANUAL LONGITUDE
        // User bisa mengetik koordinat longitude secara manual
        // ================================================
        document.getElementById('lon-input').addEventListener('input', function() {
            const lat = document.getElementById('lat-input').value.trim();
            const lng = this.value.trim();
            
            if (lat && lng) {
                const latNum = parseFloat(lat);
                const lngNum = parseFloat(lng);
                
                // Validasi range koordinat yang valid
                if (!isNaN(latNum) && !isNaN(lngNum) && 
                    latNum >= -90 && latNum <= 90 && 
                    lngNum >= -180 && lngNum <= 180) {
                    updateMarker(latNum, lngNum);
                    map.setView([latNum, lngNum], 15);
                }
            }
        });
    </script>
</body>
</html>
