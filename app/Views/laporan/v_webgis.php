<!--
============================================================================
VIEW WEBGIS - HALAMAN UTAMA PETA INTERAKTIF
============================================================================

File ini adalah tampilan utama aplikasi WebGIS yang menampilkan:
1. Peta interaktif menggunakan Leaflet.js
2. Marker lokasi kerusakan dengan popup informasi
3. Sidebar dengan daftar laporan dan statistik
4. Fitur pencarian dan filter laporan

Library yang digunakan:
- Leaflet.js: Library JavaScript untuk peta interaktif
- Bootstrap 5: Framework CSS untuk tampilan responsif
- Chart.js: Library untuk membuat grafik statistik
- Font Awesome: Library ikon

Controller: Laporan::index()
URL: / atau /laporan

Author: [Nama Anda]
============================================================================
-->

<!DOCTYPE html>
<html lang="id">
<head>
    <!-- Meta tag untuk encoding dan responsif -->
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Peta Laporan - WebGIS Fasilitas Umum</title>
    
    <!-- CSS Libraries -->
    <!-- Leaflet CSS: Styling untuk komponen peta -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.9.4/leaflet.min.css" />
    <!-- Bootstrap CSS: Framework tampilan responsif -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" />
    <!-- Font Awesome: Library ikon -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
    <!-- Chart.js: Library untuk membuat grafik -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
    <!-- Custom CSS untuk styling halaman -->
    <style>
        /* Reset default margin dan padding */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        /* Styling body dengan animasi fade in */
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #f5f5f5;
            overflow: hidden;
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
        
        /* Styling navbar (header) */
        .navbar {
            background: #333 !important;
            height: 70px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
            padding: 12px 0 !important;
        }
        
        /* Logo/brand di navbar */
        .navbar-brand {
            color: white !important;
            font-weight: bold;
            font-size: 1.5rem;
            line-height: 1.2;
        }
        
        .navbar .container-fluid {
            padding: 0 1rem;
            align-items: center;
            height: 100%;
        }
        
        .navbar-nav {
            gap: 5px;
        }
        
        /* Tombol "Buat Laporan" */
        .btn-lapor {
            background-color: #555;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 6px;
            font-weight: 500;
            font-size: 14px;
            margin-right: 10px;
            transition: all 0.3s ease;
        }
        
        .btn-lapor:hover {
            background-color: #666;
            color: white;
            text-decoration: none;
            transform: translateY(-1px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.2);
        }
        
        /* Tombol "Login Admin" */
        .btn-login {
            background-color: #dc3545;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 6px;
            font-weight: 500;
            font-size: 14px;
            transition: all 0.3s ease;
        }
        
        .btn-login:hover {
            background-color: #c82333;
            color: white;
            text-decoration: none;
            transform: translateY(-1px);
            box-shadow: 0 4px 8px rgba(220, 53, 69, 0.3);
        }
        
        /* Container peta - tinggi penuh dikurangi navbar */
        #map {
            width: 100%;
            height: calc(100vh - 70px);
            background: #e8eef7;
            position: relative;
            z-index: 1;
        }
        
        /* Pastikan peta bisa diklik saat mode pilih lokasi */
        .map-click-mode #map {
            z-index: 500 !important;
            pointer-events: auto !important;
        }
        
        .map-click-mode .leaflet-container {
            pointer-events: auto !important;
        }
        
        /* Sembunyikan panel-panel saat mode pilih lokasi */
        .map-click-mode .floating-panel,
        .map-click-mode .panel-stats,
        .map-click-mode #priorityLegend {
            pointer-events: none;
            opacity: 0.3;
        }
        
        .leaflet-control-container {
            z-index: 10 !important;
        }
        
        /* Panel melayang untuk statistik dan filter */
        .floating-panel {
            position: fixed;
            background: white;
            border-radius: 10px;
            box-shadow: 0 8px 24px rgba(0,0,0,0.12);
            z-index: 1000 !important;
            border: 1px solid #e5e7eb;
        }
        
        /* Panel statistik horizontal */
        .panel-stats {
            position: fixed;
            bottom: 30px;
            left: 700px;
            width: 450px;
            padding: 12px;
            display: flex;
            align-items: center;
            gap: 15px;
            background: white;
            border-radius: 10px;
            box-shadow: 0 8px 24px rgba(0,0,0,0.12);
            z-index: 1000;
            border: 1px solid #e5e7eb;
        }
        
        /* Stats section - left side - compact */
        .stats-section {
            display: flex;
            flex-direction: column;
            min-width: 140px;
            max-width: 140px;
        }
        
        .panel-stats h6 {
            font-size: 10px;
            font-weight: bold;
            color: #333;
            margin: 0 0 6px 0;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .stat-total {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 8px;
            padding-bottom: 6px;
            border-bottom: 1px solid #eee;
        }
        
        .stat-label {
            font-size: 10px;
            color: #666;
            font-weight: 600;
        }
        
        .stat-value {
            font-weight: bold;
            color: #333;
            font-size: 12px;
        }
        
        .legend-section {
            margin-top: 0;
            padding-top: 0;
            border-top: none;
        }
        
        .legend-item {
            font-size: 9px !important;
            margin-bottom: 3px !important;
            gap: 4px !important;
        }
        
        .legend-dot {
            width: 8px !important;
            height: 8px !important;
        }
        
        /* Charts section - right side - compact */
        .charts-section {
            display: flex;
            gap: 15px;
            align-items: center;
            flex: 1;
            justify-content: center;
        }
        
        .chart-item {
            text-align: center;
        }
        
        .chart-item h6 {
            font-size: 9px;
            font-weight: 600;
            color: #666;
            margin: 0 0 4px 0;
            text-transform: uppercase;
        }
        
        .chart-canvas {
            width: 80px !important;
            height: 80px !important;
        }
        
        .legend-item {
            display: flex;
            align-items: center;
            gap: 6px;
            margin-bottom: 6px;
            font-size: 11px;
            color: #555;
        }
        
        .legend-dot {
            width: 10px;
            height: 10px;
            border-radius: 50%;
            flex-shrink: 0;
        }
        
        /* Panel filter dan total laporan */
        .panel-filter {
            top: 90px;
            left: 20px;
            width: 280px;
            height: auto;
            max-height: calc(100vh - 120px);
            padding: 0;
            display: block !important;
            visibility: visible !important;
            opacity: 1 !important;
            background: #555;
            border: none;
            overflow-y: auto;
        }
        
        .panel-filter-header {
            padding: 12px 20px 10px 20px;
            border-bottom: 1px solid rgba(255,255,255,0.2);
            margin-bottom: 5px;
            background: rgba(0,0,0,0.1);
        }
        
        .panel-filter h6 {
            font-size: 12px;
            font-weight: 600;
            color: #ddd;
            margin: 0;
            text-transform: uppercase;
            letter-spacing: 2px;
        }
        
        .filter-options {
            display: flex;
            flex-direction: column;
            gap: 0;
            padding: 0;
        }
        
        .filter-options label {
            font-size: 14px;
            margin: 0;
            cursor: pointer;
            color: rgba(255,255,255,0.9);
            display: flex;
            align-items: center;
            gap: 8px;
            font-weight: 500;
            transition: all 0.3s ease;
            padding: 10px 20px;
            border-left: 4px solid transparent;
        }
        
        .filter-options label:hover {
            background: rgba(255,255,255,0.15);
            color: white;
            border-left-color: #aaa;
        }
        
        .filter-options input[type="checkbox"] {
            cursor: pointer;
            margin: 0;
            width: 18px;
            height: 18px;
            accent-color: #aaa;
        }
        
        .filter-options input[type="radio"] {
            cursor: pointer;
            margin: 0;
            width: 18px;
            height: 18px;
            accent-color: #aaa;
        }
        
        /* Panel daftar lokasi laporan */
        .panel-locations {
            top: 395px;
            bottom: auto;
            left: 20px;
            width: 280px;
            height: calc(100vh - 415px);
            padding: 0;
            display: flex;
            flex-direction: column;
            overflow: hidden;
            box-sizing: border-box;
            background: #555;
            border: none;
        }
        
        #mobileFilterSection {
            display: none;
        }
        
        .panel-locations-header {
            padding: 12px 20px 10px 20px;
            border-bottom: 1px solid rgba(255,255,255,0.2);
            background: rgba(0,0,0,0.1);
            flex-shrink: 0;
        }
        
        .panel-locations h6 {
            font-size: 12px;
            font-weight: 600;
            color: #ddd;
            margin: 0;
            padding: 0;
            text-transform: uppercase;
            letter-spacing: 2px;
            flex-shrink: 0;
        }
        
        .search-box {
            margin: 0;
            padding: 10px 15px;
            flex-shrink: 0;
        }
        
        .search-box input {
            width: 100%;
            padding: 10px 12px;
            border: none;
            border-radius: 6px;
            font-size: 13px;
            background: rgba(255,255,255,0.9);
            box-sizing: border-box;
        }
        
        .search-box input::placeholder {
            color: #666;
        }
        
        .search-box input:focus {
            outline: none;
            background: white;
        }
        
        #locationList {
            flex: 1;
            overflow-y: auto;
            overflow-x: hidden;
            min-height: 0;
            padding: 0 15px 15px 15px;
        }
        
        .location-item {
            padding: 15px;
            margin-bottom: 10px;
            background: rgba(255,255,255,0.95);
            border-radius: 8px;
            border-left: 4px solid #aaa;
            cursor: pointer;
            font-size: 14px;
            transition: all 0.3s ease;
            box-sizing: border-box;
            box-shadow: 0 2px 8px rgba(0,0,0,0.15);
        }
        
        .location-item:hover {
            background: white;
            transform: translateX(4px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.2);
            border-left-color: #ddd;
        }
        
        .location-item-title {
            font-weight: 700;
            color: #333;
            margin-bottom: 6px;
            font-size: 15px;
        }
        
        .location-item-desc {
            color: #555;
            font-size: 13px;
            margin-bottom: 6px;
        }
        
        .location-status {
            display: inline-block;
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 12px;
            font-weight: bold;
            color: white;
        }
        
        .status-baru {
            background-color: #6b7280;
        }
        
        .status-diproses {
            background-color: #dc2626;
        }
        
        .status-dijadwalkan {
            background-color: #f59e0b;
            color: #fff;
        }
        
        .status-selesai {
            background-color: #16a34a;
        }
        
        /* Panel kontrol layer peta */
        .panel-layers {
            bottom: 20px;
            right: 20px;
            width: 200px;
            padding: 20px;
        }
        
        .panel-layers h6 {
            font-size: 12px;
            font-weight: bold;
            color: #333;
            margin-bottom: 12px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .layer-options {
            display: flex;
            flex-direction: column;
            gap: 8px;
        }
        
        .layer-options label {
            font-size: 13px;
            margin: 0;
            cursor: pointer;
            color: #333;
            display: flex;
            align-items: center;
            gap: 6px;
        }
        
        .layer-options input[type="radio"] {
            cursor: pointer;
            margin: 0;
        }
        
        /* Scrollbar Styling */
        #locationList::-webkit-scrollbar {
            width: 6px;
        }
        
        #locationList::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 3px;
        }
        
        #locationList::-webkit-scrollbar-thumb {
            background: #888;
            border-radius: 3px;
        }
        
        #locationList::-webkit-scrollbar-thumb:hover {
            background: #555;
        }
        
        /* =====================================================
           SLIDE PANEL - Lapor Kerusakan dari Kanan
           ===================================================== */
        /* Overlay dihapus agar peta tetap bisa diklik */
        
        .slide-panel {
            position: fixed;
            top: 0;
            right: -450px;
            width: 420px;
            max-width: 100%;
            height: 100vh;
            background: #fff;
            box-shadow: -5px 0 30px rgba(0, 0, 0, 0.2);
            z-index: 2000;
            transition: right 0.3s ease;
            display: flex;
            flex-direction: column;
            overflow: hidden;
        }
        
        .slide-panel.active {
            right: 0;
        }
        
        .slide-panel-header {
            background: #555;
            color: white;
            padding: 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-shrink: 0;
        }
        
        .slide-panel-header h4 {
            margin: 0;
            font-weight: bold;
            font-size: 18px;
        }
        
        .slide-panel-header .close-btn {
            background: rgba(255,255,255,0.2);
            border: none;
            color: white;
            width: 36px;
            height: 36px;
            border-radius: 50%;
            font-size: 18px;
            cursor: pointer;
            transition: all 0.2s ease;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .slide-panel-header .close-btn:hover {
            background: rgba(255,255,255,0.3);
            transform: rotate(90deg);
        }
        
        .slide-panel-body {
            flex: 1;
            overflow-y: auto;
            padding: 20px;
        }
        
        .slide-panel-body .form-group {
            margin-bottom: 18px;
        }
        
        .slide-panel-body .form-label {
            font-weight: 600;
            color: #333;
            margin-bottom: 6px;
            font-size: 14px;
            display: block;
        }
        
        .slide-panel-body .form-control,
        .slide-panel-body .form-select {
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            padding: 10px 14px;
            font-size: 14px;
            transition: all 0.2s ease;
        }
        
        .slide-panel-body .form-control:focus,
        .slide-panel-body .form-select:focus {
            border-color: #666;
            box-shadow: 0 0 0 3px rgba(100, 100, 100, 0.1);
        }
        
        .slide-panel-body .required {
            color: #dc3545;
        }
        
        .slide-panel-body .form-text {
            font-size: 12px;
            color: #888;
            margin-top: 4px;
        }
        
        .slide-panel-body .preview-foto {
            max-width: 100%;
            max-height: 150px;
            border-radius: 8px;
            margin-top: 10px;
            display: none;
            border: 2px solid #e0e0e0;
        }
        
        .location-picker-info {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 15px;
            border-radius: 10px;
            margin-bottom: 15px;
        }
        
        .location-picker-info h6 {
            margin: 0 0 8px 0;
            font-size: 14px;
            font-weight: bold;
        }
        
        .location-picker-info p {
            margin: 0;
            font-size: 12px;
            opacity: 0.9;
        }
        
        .location-display {
            background: #f8f9fa;
            padding: 12px;
            border-radius: 8px;
            border-left: 4px solid #666;
            font-size: 13px;
            margin-top: 10px;
        }
        
        .location-display .coords {
            font-family: monospace;
            color: #333;
            font-weight: 600;
        }
        
        .btn-gps-panel {
            background: #666;
            color: white;
            border: none;
            padding: 10px 16px;
            border-radius: 8px;
            font-size: 13px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s ease;
            width: 100%;
            margin-top: 10px;
        }
        
        .btn-gps-panel:hover {
            background: #555;
            transform: translateY(-1px);
        }
        
        .btn-gps-panel:disabled {
            background: #aaa;
            cursor: not-allowed;
            transform: none;
        }
        
        .gps-status-panel {
            font-size: 12px;
            margin-top: 6px;
            text-align: center;
        }
        
        .gps-status-panel.success { color: #28a745; }
        .gps-status-panel.error { color: #dc3545; }
        
        .slide-panel-footer {
            padding: 20px;
            border-top: 1px solid #e0e0e0;
            background: #f8f9fa;
            flex-shrink: 0;
        }
        
        .btn-submit-panel {
            background: #555;
            color: white;
            border: none;
            padding: 14px 20px;
            border-radius: 8px;
            font-size: 15px;
            font-weight: bold;
            width: 100%;
            cursor: pointer;
            transition: all 0.2s ease;
        }
        
        .btn-submit-panel:hover:not(:disabled) {
            background: #444;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.2);
        }
        
        .btn-submit-panel:disabled {
            background: #ccc;
            color: #888;
            cursor: not-allowed;
        }
        
        /* Map click indicator when panel is open */
        .map-click-mode #map {
            cursor: crosshair !important;
        }
        
        .map-click-mode .leaflet-container {
            cursor: crosshair !important;
        }
        
        .map-click-indicator {
            position: fixed;
            top: 80px;
            left: 50%;
            transform: translateX(-50%);
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 12px 24px;
            border-radius: 30px;
            font-size: 14px;
            font-weight: 600;
            z-index: 1500;
            box-shadow: 0 4px 15px rgba(102, 126, 234, 0.4);
            display: none;
            animation: pulse 2s infinite;
        }
        
        @keyframes pulse {
            0%, 100% { transform: translateX(-50%) scale(1); }
            50% { transform: translateX(-50%) scale(1.02); }
        }
        
        .map-click-mode .map-click-indicator {
            display: block;
        }
        
        /* Responsive Slide Panel - Mobile Bottom Sheet */
        @media (max-width: 768px) {
            .slide-panel {
                width: 100%;
                height: 55vh;
                top: auto;
                bottom: -60vh;
                right: 0;
                left: 0;
                border-radius: 20px 20px 0 0;
                transition: bottom 0.3s ease;
            }
            
            .slide-panel.active {
                bottom: 0;
                right: 0;
            }
            
            .slide-panel-header {
                padding: 15px;
                border-radius: 20px 20px 0 0;
                position: relative;
            }
            
            .slide-panel-header::before {
                content: '';
                position: absolute;
                top: 8px;
                left: 50%;
                transform: translateX(-50%);
                width: 40px;
                height: 4px;
                background: rgba(255,255,255,0.5);
                border-radius: 2px;
            }
            
            .slide-panel-header h4 {
                font-size: 16px;
            }
            
            .slide-panel-body {
                padding: 15px;
                max-height: calc(55vh - 140px);
            }
            
            .slide-panel-footer {
                padding: 15px;
            }
            
            .location-picker-info {
                padding: 12px;
            }
            
            .location-picker-info h6 {
                font-size: 13px;
            }
            
            .location-picker-info p {
                font-size: 11px;
            }
            
            .map-click-indicator {
                top: 75px;
                font-size: 12px;
                padding: 10px 18px;
                left: 50%;
                transform: translateX(-50%);
                white-space: nowrap;
            }
            
            /* Hide other panels when slide panel is open */
            .map-click-mode .floating-panel,
            .map-click-mode .panel-stats,
            .map-click-mode .mobile-menu-btn {
                display: none !important;
            }
        }

        /* Desktop - Hide mobile button */
        .mobile-menu-btn {
            display: none;
        }
        
        /* Mobile Responsive */
        @media (max-width: 768px) {
            .floating-panel {
                display: none;
            }
            
            /* Hide priority legend on mobile */
            #priorityLegend {
                display: none !important;
            }
            
            /* Mobile toggle buttons */
            .mobile-menu-btn {
                position: fixed !important;
                bottom: 135px !important;
                right: 15px !important;
                background: linear-gradient(135deg, #636e72 0%, #57606f 100%) !important;
                color: white !important;
                border: none !important;
                padding: 12px !important;
                border-radius: 50% !important;
                box-shadow: 0 6px 20px rgba(0,0,0,0.3) !important;
                z-index: 998 !important;
                font-size: 20px !important;
                width: 50px !important;
                height: 50px !important;
                cursor: pointer !important;
                display: flex !important;
                align-items: center !important;
                justify-content: center !important;
                transition: transform 0.2s ease, box-shadow 0.2s ease !important;
                visibility: visible !important;
                opacity: 1 !important;
            }
            
            .mobile-menu-btn:active {
                transform: scale(0.9) !important;
                box-shadow: 0 2px 8px rgba(0,0,0,0.4) !important;
            }
        }
        
        .no-reports {
            text-align: center;
            color: #999;
            padding: 20px 10px;
            font-size: 12px;
        }
        
        /* Popup styles */
        .popup-content {
            width: 300px;
            font-size: 14px;
        }
        
        .popup-content h5 {
            margin-bottom: 10px;
            color: #333;
        }
        
        .popup-content img {
            width: 100%;
            height: 150px;
            object-fit: cover;
            border-radius: 3px;
            margin-bottom: 10px;
            border: 1px solid #ddd;
        }
        
        .popup-content p {
            color: #555;
            margin: 5px 0;
        }
        
        .popup-actions {
            margin-top: 10px;
            display: flex;
            gap: 5px;
        }
        
        .popup-actions a {
            flex: 1;
            padding: 5px;
            text-align: center;
            border-radius: 3px;
            text-decoration: none;
            font-size: 12px;
            font-weight: bold;
        }
        
        .popup-actions .btn-detail {
            background-color: #636e72;
            color: white;
        }
        
        .popup-actions .btn-detail:hover {
            background-color: #57606f;
            color: white;
        }
        
        .popup-actions .btn-edit {
            background-color: #74b9ff;
            color: white;
        }
        
        .popup-actions .btn-edit:hover {
            background-color: #5f9ea0;
            color: white;
        }
        
        /* Desktop - hide mobile button */
        .mobile-menu-btn {
            display: none;
        }
        
        /* Make zoom and location buttons bigger on desktop */
        .leaflet-control.leaflet-bar a {
            width: 45px !important;
            height: 45px !important;
            line-height: 45px !important;
            font-size: 20px !important;
        }
        
        .go-to-location-btn {
            width: 45px !important;
            height: 45px !important;
            font-size: 22px !important;
        }
        
        /* Mobile Responsive */
        @media (max-width: 768px) {
            /* Mobile backdrop - click to close */
            .mobile-backdrop {
                position: fixed;
                top: 0;
                left: 0;
                right: 0;
                bottom: 0;
                background: rgba(0, 0, 0, 0.5);
                z-index: 1002;
                display: none;
                opacity: 0;
                transition: opacity 0.3s ease;
            }
            
            .mobile-backdrop.show {
                display: block;
                opacity: 1;
            }
            
            /* Hide desktop panels on mobile */
            .panel-filter {
                display: none !important;
            }
            
            /* Hide desktop panel-locations charts section on mobile */
            .panel-locations > div:first-child {
                display: none !important;
            }
            
            /* Show mobile stats panel */
            #mobileStatsPanel {
                display: flex !important;
            }
            
            /* Mobile stats panel styling */
            #mobileStatsPanel.panel-stats {
                position: fixed !important;
                bottom: 10px !important;
                left: 10px !important;
                right: 10px !important;
                top: auto !important;
                width: auto !important;
                padding: 12px !important;
                z-index: 900 !important;
                display: flex !important;
                flex-direction: row !important;
                align-items: flex-start !important;
                gap: 12px !important;
                height: auto !important;
                max-height: 140px !important;
            }
            
            /* Show charts on mobile - right side */
            .charts-section {
                display: flex !important;
                flex-direction: row !important;
                gap: 10px !important;
                align-items: center !important;
                margin: 0 !important;
                flex-shrink: 0 !important;
            }
            
            .charts-section .chart-item {
                text-align: center !important;
            }
            
            .charts-section .chart-item h6 {
                font-size: 7px !important;
                margin: 0 0 3px 0 !important;
                display: block !important;
            }
            
            .charts-section canvas {
                width: 55px !important;
                height: 55px !important;
            }
            
            /* Stats section with legend for horizontal layout */
            .stats-section {
                min-width: auto !important;
                flex: 1 !important;
            }
            
            .stats-section .legend-section {
                display: block !important;
                margin-top: 6px !important;
            }
            
            .stats-section h6 {
                margin: 0 0 2px 0 !important;
            }
            
            .panel-stats h6 {
                font-size: 9px !important;
                margin: 0 0 4px 0 !important;
                font-weight: 600 !important;
            }
            
            .panel-stats .stat-total {
                margin-bottom: 4px !important;
                font-size: 9px !important;
                padding-bottom: 4px !important;
                border-bottom: 1px solid #ddd !important;
            }
            
            .panel-stats .stat-label {
                font-size: 8px !important;
                color: #666 !important;
            }
            
            .panel-stats .stat-value {
                font-size: 9px !important;
                font-weight: 600 !important;
                color: #333 !important;
            }
            
            .panel-stats .legend-section {
                margin-top: 4px !important;
            }
            
            .panel-stats .legend-section h6 {
                font-size: 7px !important;
                margin: 0 0 3px 0 !important;
            }
            
            .panel-stats .legend-item {
                font-size: 7px !important;
                margin-bottom: 2px !important;
                gap: 3px !important;
            }
            
            .panel-stats .legend-dot {
                width: 6px !important;
                height: 6px !important;
            }
            
            /* Add legend for mobile */
            .stats-section .legend-section {
                display: block !important;
                margin-top: 4px !important;
            }
            
            .stats-section .legend-section h6 {
                font-size: 7px !important;
                margin: 0 0 3px 0 !important;
                color: #666 !important;
            }
            
            .stats-section .legend-item {
                display: flex !important;
                align-items: center !important;
                gap: 3px !important;
                margin-bottom: 2px !important;
                font-size: 7px !important;
                color: #555 !important;
            }
            
            .stats-section .legend-dot {
                width: 6px !important;
                height: 6px !important;
                border-radius: 50% !important;
                flex-shrink: 0 !important;
            }

            /* Mobile panel locations - collapsible bottom sheet */
            .panel-locations {
                position: fixed !important;
                bottom: 0 !important;
                left: 0 !important;
                right: 0 !important;
                width: 100% !important;
                height: auto !important;
                max-height: 80vh !important;
                padding: 0 !important;
                margin: 0 !important;
                border-radius: 16px 16px 0 0 !important;
                display: none !important;
                flex-direction: column !important;
                transform: translateY(100%) !important;
                transition: transform 0.5s ease !important;
                z-index: 1003 !important;
            }
            
            .panel-locations.open {
                display: flex !important;
                transform: translateY(0) !important;
            }
            
            /* Header - clickable */
            .panel-locations h6:first-of-type {
                margin: 0 !important;
                padding: 15px !important;
                background: linear-gradient(135deg, #555 0%, #666 100%) !important;
                color: white !important;
                border-radius: 16px 16px 0 0 !important;
                flex-shrink: 0 !important;
                cursor: pointer !important;
                display: flex !important;
                justify-content: space-between !important;
                align-items: center !important;
                font-size: 16px !important;
            }
            
            .panel-locations h6:first-of-type::after {
                content: '✕';
                font-size: 20px;
                font-weight: bold;
                cursor: pointer;
            }
            
            /* Filter section */
            #mobileFilterSection {
                display: flex !important;
                flex-direction: column !important;
                padding: 10px 15px !important;
                border-bottom: 1px solid #eee !important;
                flex-shrink: 0 !important;
            }
            
            #mobileFilterSection .filter-options label {
                font-size: 10px;
                margin-bottom: 4px;
            }
            
            /* Search */
            .search-box {
                padding: 10px 15px !important;
                margin: 0 !important;
                flex-shrink: 0;
            }
            
            /* Location list - scrollable */
            #locationList {
                flex: 1 !important;
                overflow-y: auto !important;
                padding: 0 !important;
                margin: 0 !important;
                min-height: 0;
            }
            
            .location-item {
                padding: 8px 15px !important;
                margin: 0 0 6px 0 !important;
            }
            
            /* Mobile toggle button */
            .mobile-menu-btn {
                position: fixed !important;
                bottom: 20px !important;
                right: 20px !important;
                width: 50px !important;
                height: 50px !important;
                display: flex !important;
                align-items: center !important;
                justify-content: center !important;
                z-index: 998 !important;
                background: linear-gradient(135deg, #555 0%, #666 100%) !important;
                color: white !important;
                border: none !important;
                border-radius: 50% !important;
                font-size: 20px !important;
                cursor: pointer !important;
                box-shadow: 0 4px 16px rgba(0,0,0,0.4) !important;
                padding: 0 !important;
            }
            
            /* Navbar */
            .navbar {
                height: auto !important;
                min-height: 60px !important;
                padding: 0.5rem 0 !important;
            }
            
            .navbar-brand {
                font-size: 16px;
                font-weight: bold;
                flex: 1;
                text-align: center;
            }
            
            .navbar .container-fluid {
                padding: 0.5rem 0.5rem !important;
                flex-wrap: wrap;
            }
            
            .navbar-nav {
                flex-direction: row;
                gap: 5px;
                width: 100%;
                margin-top: 0.5rem;
            }
            
            .btn-lapor, .btn-login {
                padding: 8px 10px;
                font-size: 11px;
                flex: 1;
            }
            
            .leaflet-top.leaflet-left {
                top: 70px !important;
            }
            
            /* Ensure GPS location button is visible on mobile */
            .leaflet-top.leaflet-right {
                top: 70px !important;
                right: 10px !important;
            }
            
            .leaflet-control.leaflet-bar {
                margin-top: 0 !important;
                box-shadow: 0 2px 8px rgba(0,0,0,0.3) !important;
            }
            
            .leaflet-control.leaflet-bar a {
                width: 50px !important;
                height: 50px !important;
                line-height: 50px !important;
                font-size: 22px !important;
            }
            
            .go-to-location-btn {
                width: 50px !important;
                height: 50px !important;
                font-size: 24px !important;
            }
        }
        
        @media (max-width: 480px) {
            .navbar-brand {
                font-size: 14px;
                font-weight: bold;
            }
            
            .btn-lapor,
            .btn-login {
                padding: 6px 8px;
                font-size: 10px;
            }
            
            #map {
                height: calc(85vh - 60px) !important;
            }
        }
    </style>
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg">
        <div class="container-fluid">
            <a class="navbar-brand" href="<?= base_url('/') ?>"><i class="fas fa-map-marked-alt"></i> WebGIS Fasilitas Umum</a>
            <div class="navbar-nav ms-auto">
                <a href="<?= base_url('laporan/saya') ?>" class="btn btn-lapor"><i class="fas fa-clipboard-list"></i> Laporan Saya</a>
                <button type="button" class="btn btn-lapor" id="btnOpenSlidePanel"><i class="fas fa-plus"></i> Lapor Kerusakan</button>
                
                <?php if (session()->get('is_logged_in') && session()->get('admin_id') && !isset($is_public_page)): ?>
                    <a href="<?= base_url('admin') ?>" class="btn btn-lapor"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
                    <a href="<?= base_url('logout') ?>" class="btn btn-login"><i class="fas fa-sign-out-alt"></i> Logout</a>
                <?php elseif (session()->get('admin_id') && !isset($is_public_page)): ?>
                    <!-- Admin login tapi belum set is_logged_in -->
                    <a href="<?= base_url('admin') ?>" class="btn btn-lapor"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
                    <a href="<?= base_url('logout') ?>" class="btn btn-login"><i class="fas fa-sign-out-alt"></i> Logout</a>
                <?php endif; ?>
            </div>
        </div>
    </nav>

    <!-- Map Container -->
    <div id="map"></div>
    
    <!-- Legend: Dual Indicator System (Fixed Bottom Right) -->
    <div id="priorityLegend" style="position: fixed; bottom: 30px; right: 20px; background: white; padding: 14px 18px; border-radius: 10px; box-shadow: 0 4px 16px rgba(0,0,0,0.15); z-index: 900; font-size: 12px; border: 1px solid #e5e7eb; max-width: 280px;">
        <div style="font-weight: bold; margin-bottom: 10px; color: #333; font-size: 11px; text-transform: uppercase; letter-spacing: 0.5px;">
            <i class="fas fa-map-marker-alt"></i> Keterangan Marker
        </div>
        
        <!-- Status Legend (Warna Isi) -->
        <div style="margin-bottom: 12px;">
            <div style="font-weight: 600; color: #555; font-size: 10px; margin-bottom: 6px; text-transform: uppercase;">📋 Warna Isi = Status</div>
            <div style="display: flex; flex-direction: column; gap: 4px; padding-left: 8px;">
                <div style="display: flex; align-items: center; gap: 8px;">
                    <span style="width: 14px; height: 14px; background: #6c757d; border-radius: 50%; box-shadow: 0 1px 3px rgba(0,0,0,0.2);"></span>
                    <span style="color: #666; font-size: 11px;">Baru</span>
                </div>
                <div style="display: flex; align-items: center; gap: 8px;">
                    <span style="width: 14px; height: 14px; background: #fd7e14; border-radius: 50%; box-shadow: 0 1px 3px rgba(0,0,0,0.2);"></span>
                    <span style="color: #666; font-size: 11px;">Diproses</span>
                </div>
                <div style="display: flex; align-items: center; gap: 8px;">
                    <span style="width: 14px; height: 14px; background: #17a2b8; border-radius: 50%; box-shadow: 0 1px 3px rgba(0,0,0,0.2);"></span>
                    <span style="color: #666; font-size: 11px;">Dijadwalkan</span>
                </div>
                <div style="display: flex; align-items: center; gap: 8px;">
                    <span style="width: 14px; height: 14px; background: #28a745; border-radius: 50%; box-shadow: 0 1px 3px rgba(0,0,0,0.2);"></span>
                    <span style="color: #666; font-size: 11px;">Selesai</span>
                </div>
            </div>
        </div>
        
        <!-- Priority Legend (Warna Border) -->
        <div style="padding-top: 10px; border-top: 1px solid #eee;">
            <div style="font-weight: 600; color: #555; font-size: 10px; margin-bottom: 6px; text-transform: uppercase;">⭕ Warna Garis = Prioritas</div>
            <div style="display: flex; flex-direction: column; gap: 4px; padding-left: 8px;">
                <div style="display: flex; align-items: center; gap: 8px;">
                    <span style="width: 14px; height: 14px; background: #fff; border-radius: 50%; border: 4px solid #dc3545; box-shadow: 0 1px 3px rgba(0,0,0,0.2);"></span>
                    <span style="color: #666; font-size: 11px;">Tinggi (Mendesak)</span>
                </div>
                <div style="display: flex; align-items: center; gap: 8px;">
                    <span style="width: 14px; height: 14px; background: #fff; border-radius: 50%; border: 3px solid #ffc107; box-shadow: 0 1px 3px rgba(0,0,0,0.2);"></span>
                    <span style="color: #666; font-size: 11px;">Sedang</span>
                </div>
                <div style="display: flex; align-items: center; gap: 8px;">
                    <span style="width: 14px; height: 14px; background: #fff; border-radius: 50%; border: 2px solid #28a745; box-shadow: 0 1px 3px rgba(0,0,0,0.2);"></span>
                    <span style="color: #666; font-size: 11px;">Rendah</span>
                </div>
            </div>
        </div>
        
        <div style="margin-top: 10px; padding-top: 8px; border-top: 1px solid #eee; font-size: 10px; color: #888; line-height: 1.4;">
            <i class="fas fa-info-circle"></i> Prioritas ditentukan admin berdasarkan tingkat urgensi.
        </div>
    </div>
    
    <!-- Mobile Backdrop -->
    <div class="mobile-backdrop" id="mobileBackdrop"></div>
    
    <!-- Mobile Menu Button -->
    <button class="mobile-menu-btn" id="mobileMenuBtn"><i class="fas fa-map-marker-alt"></i></button>

    <!-- =====================================================
         SLIDE PANEL - Lapor Kerusakan
         ===================================================== -->
    
    <div class="map-click-indicator" id="mapClickIndicator">
        <i class="fas fa-mouse-pointer"></i> Klik di peta untuk pilih lokasi
    </div>
    
    <div class="slide-panel" id="slidePanel">
        <div class="slide-panel-header">
            <h4><i class="fas fa-edit"></i> Lapor Kerusakan</h4>
            <button type="button" class="close-btn" id="btnCloseSlidePanel">
                <i class="fas fa-times"></i>
            </button>
        </div>
        
        <div class="slide-panel-body">
            <form id="formLaporSlide" enctype="multipart/form-data">
                <!-- Location Picker Info -->
                <div class="location-picker-info">
                    <h6><i class="fas fa-map-pin"></i> Pilih Lokasi di Peta</h6>
                    <p>Klik pada peta di sebelah kiri untuk menandai lokasi kerusakan, atau gunakan GPS.</p>
                </div>
                
                <div class="location-display">
                    <strong><i class="fas fa-crosshairs"></i> Koordinat:</strong><br>
                    <span class="coords">
                        Lat: <span id="panelLatDisplay">-6.9175</span> | 
                        Lng: <span id="panelLngDisplay">107.6062</span>
                    </span>
                </div>
                
                <button type="button" class="btn-gps-panel" id="btnGpsPanel">
                    <i class="fas fa-location-arrow"></i> Gunakan Lokasi Saya (GPS)
                </button>
                <div class="gps-status-panel" id="gpsStatusPanel"></div>
                
                <input type="hidden" name="latitude" id="panelLatitude" value="-6.9175">
                <input type="hidden" name="longitude" id="panelLongitude" value="107.6062">
                
                <hr style="margin: 20px 0; border-color: #e0e0e0;">
                
                <!-- Email -->
                <div class="form-group">
                    <label class="form-label">Email <span class="required">*</span></label>
                    <input type="email" class="form-control" name="email_pelapor" id="panelEmail" 
                           placeholder="nama@example.com" required>
                    <small class="form-text"><i class="fas fa-envelope"></i> Untuk notifikasi status laporan</small>
                </div>
                
                <!-- Jenis Kerusakan -->
                <div class="form-group">
                    <label class="form-label">Jenis Kerusakan <span class="required">*</span></label>
                    <select class="form-select" name="jenis_kerusakan" id="panelJenis" required>
                        <option value="">-- Pilih Jenis --</option>
                        <option value="Jalan Berlubang">Jalan Berlubang</option>
                        <option value="Fasilitas Publik Rusak">Fasilitas Publik Rusak</option>
                    </select>
                </div>
                
                <!-- Deskripsi -->
                <div class="form-group">
                    <label class="form-label">Deskripsi <span class="required">*</span></label>
                    <textarea class="form-control" name="deskripsi" id="panelDeskripsi" rows="3" 
                              placeholder="Jelaskan detail kerusakan..." required></textarea>
                </div>
                
                <!-- Foto -->
                <div class="form-group">
                    <label class="form-label">Foto Lokasi <span class="required">*</span></label>
                    <input type="file" class="form-control" name="foto_lokasi" id="panelFoto" 
                           accept="image/jpeg,image/png,image/gif,image/webp" required>
                    <small class="form-text"><i class="fas fa-camera"></i> JPG, PNG, GIF, WebP (Max 5MB)</small>
                    <img id="panelFotoPreview" class="preview-foto" alt="Preview">
                </div>
                
                <!-- reCAPTCHA -->
                <div class="form-group">
                    <div class="g-recaptcha" data-sitekey="6LeIxAcTAAAAAJcZVRqyHh71UMIEGNQ_MXjiZKhI" data-callback="enablePanelSubmit" id="panelRecaptcha"></div>
                    <small class="form-text"><i class="fas fa-shield-alt"></i> Verifikasi bahwa Anda bukan robot</small>
                </div>
            </form>
        </div>
        
        <div class="slide-panel-footer">
            <button type="button" class="btn-submit-panel" id="btnSubmitPanel" disabled>
                <i class="fas fa-paper-plane"></i> Kirim Laporan
            </button>
        </div>
    </div>
    
    <!-- SweetAlert2 for notifications -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <!-- reCAPTCHA -->
    <script src="https://www.google.com/recaptcha/api.js" async defer></script>

    <!-- Mobile Only - Statistics Panel (Always Visible) -->
    <div class="floating-panel panel-stats" id="mobileStatsPanel" style="display: none;">
        <div class="stats-section">
            <h6>📊 Total</h6>
            <div class="stat-total">
                <span class="stat-label">Jumlah:</span>
                <span class="stat-value" id="mobileCount">8</span>
            </div>
        </div>
        <div class="charts-section">
            <div class="chart-item">
                <h6>Status</h6>
                <canvas id="mobileStatusChart" style="width: 55px !important; height: 55px !important;"></canvas>
            </div>
            <div class="chart-item">
                <h6>Jenis</h6>
                <canvas id="mobileJenisChart" style="width: 55px !important; height: 55px !important;"></canvas>
            </div>
        </div>
    </div>

    <!-- Floating Panel - Total Laporan & Filter (Top Left) -->
    <div class="floating-panel panel-filter">
        <!-- Total Laporan Section -->
        <div class="panel-filter-header">
            <h6><i class="fas fa-chart-bar"></i> TOTAL LAPORAN</h6>
            <div class="stat-total" style="display: flex; justify-content: space-between; align-items: center; margin-top: 6px;">
                <span style="font-size: 14px; color: rgba(255,255,255,0.8);">Jumlah:</span>
                <span style="font-size: 22px; font-weight: bold; color: white;" id="totalCount">0</span>
            </div>
        </div>
        
        <!-- Filter Section -->
        <div style="padding: 8px 0 0 0;">
            <div style="padding: 0 20px 6px 20px; border-bottom: 1px solid rgba(255,255,255,0.1);">
                <h6 style="font-size: 10px; color: #ddd; letter-spacing: 1px; margin: 0;"><i class="fas fa-filter"></i> FILTER KERUSAKAN</h6>
            </div>
            <div class="filter-options">
                <label><input type="checkbox" class="filter-checkbox" value="Jalan Berlubang" checked> Jalan Berlubang</label>
                <label><input type="checkbox" class="filter-checkbox" value="Fasilitas Publik Rusak" checked> Fasilitas Publik Rusak</label>
            </div>
        </div>
        
        <!-- Peta Dasar Section -->
        <div style="padding: 4px 0 8px 0;">
            <div style="padding: 0 20px 4px 20px; border-bottom: 1px solid rgba(255,255,255,0.1);">
                <h6 style="font-size: 10px; color: #ddd; letter-spacing: 1px; margin: 0;"><i class="fas fa-map"></i> PETA DASAR</h6>
            </div>
            <div class="layer-options" style="padding: 4px 20px;">
                <label style="display: flex; align-items: center; gap: 8px; color: rgba(255,255,255,0.9); font-size: 12px; margin-bottom: 4px; cursor: pointer;">
                    <input type="radio" name="baseLayer" value="osm" checked style="width: 14px; height: 14px; accent-color: #aaa;"> OpenStreetMap
                </label>
                <label style="display: flex; align-items: center; gap: 8px; color: rgba(255,255,255,0.9); font-size: 12px; cursor: pointer;">
                    <input type="radio" name="baseLayer" value="satellite" style="width: 14px; height: 14px; accent-color: #aaa;"> Satellite
                </label>
            </div>
        </div>
    </div>

    <!-- Floating Panel - Charts & Locations List (Bottom Left) -->
    <div class="floating-panel panel-locations">
        <!-- Header Section -->
        <div class="panel-locations-header">
            <h6><i class="fas fa-chart-pie"></i> STATISTIK</h6>
            <div style="display: flex; gap: 15px; justify-content: center; margin-top: 10px;">
                <div class="chart-item" style="text-align: center;">
                    <h6 style="font-size: 9px; font-weight: 600; color: rgba(255,255,255,0.7); margin-bottom: 5px;">STATUS</h6>
                    <canvas id="statusChart" style="width: 70px !important; height: 70px !important;"></canvas>
                </div>
                <div class="chart-item" style="text-align: center;">
                    <h6 style="font-size: 9px; font-weight: 600; color: rgba(255,255,255,0.7); margin-bottom: 5px;">JENIS</h6>
                    <canvas id="jenisChart" style="width: 70px !important; height: 70px !important;"></canvas>
                </div>
            </div>
        </div>
        
        <!-- Daftar Laporan Header -->
        <div style="padding: 10px 20px 8px 20px; border-bottom: 1px solid rgba(255,255,255,0.1);">
            <h6 style="font-size: 10px; color: #ddd; letter-spacing: 1px; margin: 0;"><i class="fas fa-list"></i> DAFTAR LAPORAN</h6>
        </div>
        
        <!-- Search -->
        <div class="search-box">
            <input type="text" id="searchLocation" placeholder="Cari lokasi..." />
        </div>
        
        <!-- Mobile Filter -->
        <div id="mobileFilterSection">
            <div style="font-size: 10px; font-weight: bold; color: #ddd; margin-bottom: 6px; text-transform: uppercase;"><i class="fas fa-filter"></i> Filter Jenis Kerusakan</div>
            <div class="filter-options" style="padding: 0;">
                <label style="padding: 8px 0;"><input type="checkbox" class="filter-checkbox" value="Jalan Berlubang" checked> Jalan Berlubang</label>
                <label style="padding: 8px 0;"><input type="checkbox" class="filter-checkbox" value="Fasilitas Publik Rusak" checked> Fasilitas Publik Rusak</label>
            </div>
        </div>
        <div id="locationList"></div>
    </div>





    <!-- Scripts -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.9.4/leaflet.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.9.1/chart.min.js"></script>
    
    <script>
        // Smart base URL detection
        let baseUrl = window.location.origin;
        
        // Auto-detect correct path based on current location
        if (window.location.pathname.includes('/PEMETAAN_LAPORAN_FASILITAS/public/')) {
            // Already on correct path
            baseUrl = window.location.origin + '/PEMETAAN_LAPORAN_FASILITAS/public/';
        } else if (window.location.pathname.includes('/public/')) {
            // Direct public access
            baseUrl = window.location.origin + '/public/';
        } else {
            // Assume need to add full path
            baseUrl = window.location.origin + '/PEMETAAN_LAPORAN_FASILITAS/public/';
        }
        
        console.log('📍 Detected Base URL:', baseUrl);
        console.log('📍 Current path:', window.location.pathname);
        let map = null;
        let allLaporan = [];
        let markers = [];
        let baseLayers = {};
        let currentBaseLayer = null;
        let statusChart = null;    // Chart.js instance for status chart
        let jenisChart = null;     // Chart.js instance for jenis chart
        let userLocation = null;  // Store user location coordinates
        
        // Initialize map dengan koordinat Bandung
        function initMap() {
            map = L.map('map', {
                zoomControl: false  // Disable default zoom control
            }).setView([-6.9175, 107.6062], 12);
            
            // Add zoom control di top-right
            L.control.zoom({ position: 'topright' }).addTo(map);
            
            // 📍 Get user location
            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(
                    function(position) {
                        const userLat = position.coords.latitude;
                        const userLng = position.coords.longitude;
                        userLocation = [userLat, userLng];  // Store for later use
                        
                        console.log('📍 User location:', userLat, userLng);
                        
                        // Add blue marker untuk user location
                        const userMarker = L.circleMarker([userLat, userLng], {
                            radius: 8,
                            fillColor: '#4285F4',
                            color: '#ffffff',
                            weight: 3,
                            opacity: 1,
                            fillOpacity: 0.9,
                            className: 'user-location-marker'
                        });
                        
                        userMarker.bindPopup('📍 <strong>Lokasi Anda Saat Ini</strong>', {
                            className: 'user-location-popup'
                        });
                        userMarker.addTo(map);
                        
                        // Add pulsing animation effect
                        const pulseMarker = L.circleMarker([userLat, userLng], {
                            radius: 12,
                            fillColor: '#4285F4',
                            color: '#4285F4',
                            weight: 1,
                            opacity: 0.3,
                            fillOpacity: 0.2,
                            className: 'user-location-pulse'
                        });
                        
                        pulseMarker.addTo(map);
                        
                        // Optional: animate the pulse
                        setInterval(() => {
                            pulseMarker.setRadius(pulseMarker.getRadius() === 12 ? 18 : 12);
                            pulseMarker.setStyle({
                                opacity: pulseMarker.getRadius() === 12 ? 0.3 : 0.1
                            });
                        }, 1500);
                        
                        // Enable the "Go to my location" button
                        addGoToLocationButton();
                    },
                    function(error) {
                        console.warn('⚠️ Geolocation error:', error.message);
                        // Still add GPS button even if geolocation fails
                        addGoToLocationButton();
                    },
                    {
                        enableHighAccuracy: false,
                        timeout: 10000,
                        maximumAge: 0
                    }
                );
            } else {
                console.warn('⚠️ Geolocation tidak support di browser ini');
                // Still add GPS button even if geolocation not supported
                addGoToLocationButton();
            }
            
            // OpenStreetMap layer
            baseLayers.osm = L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '© OpenStreetMap contributors',
                maxZoom: 19
            });
            
            // Satellite layer
            baseLayers.satellite = L.tileLayer('https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}', {
                attribution: 'Tiles © Esri',
                maxZoom: 18
            });
            
            // Set default layer
            currentBaseLayer = baseLayers.osm;
            currentBaseLayer.addTo(map);
        }
        
        // Load data dari API
        function loadLaporan() {
            console.log('Loading laporan from:', baseUrl + 'laporan/getDataJson');
            
            fetch(baseUrl + 'laporan/getDataJson')
                .then(response => {
                    console.log('Response status:', response.status);
                    if (!response.ok) {
                        throw new Error(`HTTP error! status: ${response.status}`);
                    }
                    return response.json();
                })
                .then(data => {
                    console.log('Data received:', data);
                    console.log('Data length:', data ? data.length : 'null');
                    
                    if (!data || !Array.isArray(data)) {
                        console.error('Invalid data format received:', data);
                        return;
                    }
                    
                    allLaporan = data;
                    console.log('All laporan set to:', allLaporan);
                    
                    // Update statistics and UI with delay to ensure DOM is ready
                    setTimeout(() => {
                        console.log('🔄 Updating UI components...');
                        updateStats(allLaporan);
                        displayLocationList(allLaporan);
                        displayMarkers(allLaporan);
                        setupFilterListeners();  // 🔧 Setup filter event listeners after data loads
                    }, 100);
                    
                    // Check if there's an ID parameter in URL to zoom to specific location
                    const urlParams = new URLSearchParams(window.location.search);
                    const laporanId = urlParams.get('id');
                    if (laporanId) {
                        const targetLaporan = allLaporan.find(l => l.id == laporanId);
                        if (targetLaporan) {
                            zoomToLocation(targetLaporan.latitude, targetLaporan.longitude);
                            // Open popup for that marker
                            setTimeout(() => {
                                markers.forEach(marker => {
                                    const markerLatLng = marker.getLatLng();
                                    if (markerLatLng.lat == targetLaporan.latitude && markerLatLng.lng == targetLaporan.longitude) {
                                        marker.openPopup();
                                    }
                                });
                            }, 500);
                        }
                    }
                })
                .catch(error => {
                    console.error('Error loading data:', error);
                    
                    // Show error in UI
                    const container = document.getElementById('locationList');
                    if (container) {
                        container.innerHTML = '<div class="no-reports">Error loading data: ' + error.message + '</div>';
                    }
                    
                    const totalCountEl = document.getElementById('totalCount');
                    if (totalCountEl) {
                        totalCountEl.textContent = '0';
                    }
                    
                    alert('Error loading data: ' + error.message);
                });
        }
        
        // Update statistics
        function updateStats(laporan) {
            console.log('updateStats called with:', laporan ? laporan.length : 'null', 'items');
            
            const totalCountEl = document.getElementById('totalCount');
            console.log('totalCount element:', totalCountEl);
            
            if (totalCountEl) {
                totalCountEl.textContent = laporan ? laporan.length : 0;
                console.log('Updated totalCount to:', laporan ? laporan.length : 0);
            } else {
                console.error('Element with ID totalCount not found!');
            }
            
            try {
                updateCharts(laporan);  // Update charts dengan data baru
                updateMobileLegend(laporan);  // Update mobile legend
            } catch (error) {
                console.error('Error updating charts:', error);
            }
        }
        
        // Update mobile stats and legend
        function updateMobileLegend(laporan) {
            if (window.innerWidth > 768) return;  // Only for mobile
            
            // Update mobile count
            const mobileCountEl = document.getElementById('mobileCount');
            if (mobileCountEl) {
                mobileCountEl.textContent = laporan ? laporan.length : 0;
            }
            
            if (!laporan || laporan.length === 0) return;
            
            // Count by status
            const statusCount = {
                'Baru': 0,
                'Diproses': 0,
                'Dijadwalkan': 0,
                'Selesai': 0
            };
            
            // Count by jenis kerusakan
            const jenisCount = {
                'Jalan Berlubang': 0,
                'Fasilitas Publik Rusak': 0
            };
            
            laporan.forEach(item => {
                statusCount[item.status] = (statusCount[item.status] || 0) + 1;
                jenisCount[item.jenis_kerusakan] = (jenisCount[item.jenis_kerusakan] || 0) + 1;
            });
            
            // Update mobile charts if they exist
            const mobileStatusChart = window.mobileStatusChart;
            const mobileJenisChart = window.mobileJenisChart;
            
            if (mobileStatusChart) {
                mobileStatusChart.data.datasets[0].data = [
                    statusCount['Baru'],
                    statusCount['Diproses'], 
                    statusCount['Dijadwalkan'],
                    statusCount['Selesai']
                ];
                mobileStatusChart.update();
            }
            
            if (mobileJenisChart) {
                mobileJenisChart.data.datasets[0].data = [
                    jenisCount['Jalan Berlubang'],
                    jenisCount['Fasilitas Publik Rusak']
                ];
                mobileJenisChart.update();
            }
            
            // Find mobile stats section and add legend HTML
            const statsSection = document.querySelector('#mobileStatsPanel .stats-section');
            if (statsSection) {
                // Remove existing legend
                const existingLegend = statsSection.querySelector('.legend-section');
                if (existingLegend) {
                    existingLegend.remove();
                }
                
                // Add new legend
                const legendHTML = `
                    <div class="legend-section">
                        <h6>STATUS</h6>
                        <div class="legend-item"><div class="legend-dot" style="background: #6b7280;"></div>Baru: ${statusCount['Baru']}</div>
                        <div class="legend-item"><div class="legend-dot" style="background: #dc2626;"></div>Diproses: ${statusCount['Diproses']}</div>
                        <div class="legend-item"><div class="legend-dot" style="background: #f59e0b;"></div>Dijadwalkan: ${statusCount['Dijadwalkan']}</div>
                        <div class="legend-item"><div class="legend-dot" style="background: #16a34a;"></div>Selesai: ${statusCount['Selesai']}</div>
                    </div>
                `;
                statsSection.insertAdjacentHTML('beforeend', legendHTML);
            }
        }
        
        // 📊 Initialize pie charts
        function initCharts() {
            console.log('Initializing charts...');
            
            const statusChartEl = document.getElementById('statusChart');
            const jenisChartEl = document.getElementById('jenisChart');
            
            console.log('Status chart element:', statusChartEl);
            console.log('Jenis chart element:', jenisChartEl);
            
            if (!statusChartEl || !jenisChartEl) {
                console.error('Chart elements not found');
                return;
            }
            
            // Cek apakah Chart.js sudah dimuat
            if (typeof Chart === 'undefined') {
                console.error('Chart.js library not loaded - retrying in 1 second');
                setTimeout(initCharts, 1000);
                return;
            }
            
            console.log('Chart.js loaded, creating charts...');
            
            const statusCtx = statusChartEl.getContext('2d');
            const jenisCtx = jenisChartEl.getContext('2d');
            
            // Status Chart (Pie)
            statusChart = new Chart(statusCtx, {
                type: 'pie',
                data: {
                    labels: ['Baru', 'Diproses', 'Dijadwalkan', 'Selesai'],
                    datasets: [{
                        data: [0, 0, 0, 0],
                        backgroundColor: ['#6b7280', '#dc2626', '#f59e0b', '#16a34a'],
                        borderWidth: 2,
                        borderColor: '#fff'
                    }]
                },
                options: {
                    responsive: false,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false  // Hide legend untuk save space
                        }
                    }
                }
            });
            
            // Jenis Kerusakan Chart (Pie)
            jenisChart = new Chart(jenisCtx, {
                type: 'pie',
                data: {
                    labels: ['Jalan Berlubang', 'Fasilitas Publik Rusak'],
                    datasets: [{
                        data: [0, 0],
                        backgroundColor: ['#2d3436', '#74b9ff'],
                        borderWidth: 2,
                        borderColor: '#fff'
                    }]
                },
                options: {
                    responsive: false,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false  // Hide legend untuk save space
                        }
                    }
                }
            });
        }
        
        // � Initialize mobile pie charts
        function initMobileCharts() {
            console.log('Initializing mobile charts...');
            
            const mobileStatusChartEl = document.getElementById('mobileStatusChart');
            const mobileJenisChartEl = document.getElementById('mobileJenisChart');
            
            if (!mobileStatusChartEl || !mobileJenisChartEl) {
                console.log('Mobile chart elements not found');
                return;
            }
            
            // Cek apakah Chart.js sudah dimuat
            if (typeof Chart === 'undefined') {
                console.error('Chart.js library not loaded - retrying in 1 second');
                setTimeout(initMobileCharts, 1000);
                return;
            }
            
            console.log('Chart.js loaded, creating mobile charts...');
            
            const mobileStatusCtx = mobileStatusChartEl.getContext('2d');
            const mobileJenisCtx = mobileJenisChartEl.getContext('2d');
            
            // Mobile Status Chart (Pie)
            window.mobileStatusChart = new Chart(mobileStatusCtx, {
                type: 'pie',
                data: {
                    labels: ['Baru', 'Diproses', 'Dijadwalkan', 'Selesai'],
                    datasets: [{
                        data: [0, 0, 0, 0],
                        backgroundColor: ['#6b7280', '#dc2626', '#f59e0b', '#16a34a'],
                        borderWidth: 2,
                        borderColor: '#fff'
                    }]
                },
                options: {
                    responsive: false,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        }
                    }
                }
            });
            
            // Mobile Jenis Kerusakan Chart (Pie)
            window.mobileJenisChart = new Chart(mobileJenisCtx, {
                type: 'pie',
                data: {
                    labels: ['Jalan Berlubang', 'Fasilitas Publik Rusak'],
                    datasets: [{
                        data: [0, 0],
                        backgroundColor: ['#2d3436', '#74b9ff'],
                        borderWidth: 2,
                        borderColor: '#fff'
                    }]
                },
                options: {
                    responsive: false,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        }
                    }
                }
            });
        }
        
        // �📈 Update charts dengan data laporan
        function updateCharts(laporan) {
            console.log('updateCharts called with:', laporan ? laporan.length : 'null', 'items');
            
            if (!laporan || laporan.length === 0) {
                console.log('No data to update charts');
                return;
            }
            
            // Count by status
            const statusCount = {
                'Baru': 0,
                'Diproses': 0,
                'Dijadwalkan': 0,
                'Selesai': 0
            };
            
            // Count by jenis kerusakan
            const jenisCount = {
                'Jalan Berlubang': 0,
                'Fasilitas Publik Rusak': 0
            };
            
            laporan.forEach(item => {
                statusCount[item.status] = (statusCount[item.status] || 0) + 1;
                jenisCount[item.jenis_kerusakan] = (jenisCount[item.jenis_kerusakan] || 0) + 1;
            });
            
            console.log('Status counts:', statusCount);
            console.log('Jenis counts:', jenisCount);
            
            // Update status chart
            if (statusChart) {
                statusChart.data.datasets[0].data = [
                    statusCount['Baru'],
                    statusCount['Diproses'], 
                    statusCount['Dijadwalkan'],
                    statusCount['Selesai']
                ];
                statusChart.update();
                console.log('Status chart updated');
            } else {
                console.error('Status chart not initialized');
            }
            
            // Update jenis chart
            if (jenisChart) {
                jenisChart.data.datasets[0].data = [
                    jenisCount['Jalan Berlubang'],
                    jenisCount['Fasilitas Publik Rusak']
                ];
                jenisChart.update();
                console.log('Jenis chart updated');
            } else {
                console.error('Jenis chart not initialized');
            }
        }
        
        // Display markers on map
        function displayMarkers(laporan) {
            console.log('displayMarkers called with:', laporan ? laporan.length : 'null', 'items');
            
            if (!map) {
                console.error('Map not initialized yet');
                return;
            }
            
            // Remove old markers
            markers.forEach(marker => map.removeLayer(marker));
            markers = [];
            
            if (!laporan || laporan.length === 0) {
                console.log('No markers to display');
                return;
            }
            
            // Add new markers
            laporan.forEach((item, index) => {
                console.log('Adding marker for:', item.id, item.jenis_kerusakan, 'at', item.latitude, item.longitude);
                
                // === DUAL INDICATOR SYSTEM ===
                // Warna ISI (fillColor) = STATUS laporan
                let fillColor = '#6c757d'; // Default: Baru (abu-abu)
                let statusLabel = item.status || 'Baru';
                
                if (item.status === 'Diproses') {
                    fillColor = '#fd7e14'; // Orange
                } else if (item.status === 'Dijadwalkan') {
                    fillColor = '#17a2b8'; // Cyan/Biru
                } else if (item.status === 'Selesai') {
                    fillColor = '#28a745'; // Hijau
                }
                
                // Warna BORDER = PRIORITAS
                let prioritas = item.prioritas || 'sedang';
                let borderColor = '#ffc107'; // Default: Sedang (kuning)
                let borderWeight = 3;
                let prioritasIcon = '🟡';
                let prioritasLabel = 'Sedang';
                
                if (prioritas === 'tinggi') {
                    borderColor = '#dc3545'; // Merah
                    borderWeight = 4;
                    prioritasIcon = '🔴';
                    prioritasLabel = 'Tinggi';
                } else if (prioritas === 'rendah') {
                    borderColor = '#28a745'; // Hijau
                    borderWeight = 2;
                    prioritasIcon = '🟢';
                    prioritasLabel = 'Rendah';
                }
                
                // Status badge color for popup
                let statusColor = '#6c757d'; // Baru
                if (item.status === 'Diproses') statusColor = '#fd7e14';
                if (item.status === 'Dijadwalkan') statusColor = '#17a2b8';
                if (item.status === 'Selesai') statusColor = '#28a745';
                
                const popupContent = `
                    <div class="popup-content">
                        <h5>${item.jenis_kerusakan}</h5>
                        <img src="${baseUrl}uploads/${item.foto_lokasi}" alt="Foto" onerror="this.src='${baseUrl}image/no-image.png'">
                        <div style="display: flex; gap: 8px; margin: 10px 0; flex-wrap: wrap;">
                            <span style="background: ${statusColor}; color: #fff; padding: 4px 10px; border-radius: 12px; font-size: 11px; font-weight: bold;">
                                📋 ${statusLabel}
                            </span>
                            <span style="background: ${borderColor}; color: ${prioritas === 'sedang' ? '#000' : '#fff'}; padding: 4px 10px; border-radius: 12px; font-size: 11px; font-weight: bold;">
                                ${prioritasIcon} ${prioritasLabel}
                            </span>
                        </div>
                        <p><strong>Deskripsi:</strong> ${item.deskripsi.substring(0, 80)}...</p>
                        <p><strong>Tanggal Lapor:</strong> ${new Date(item.tgl_lapor).toLocaleDateString('id-ID')}</p>
                        <div class="popup-actions">
                            <a href="${baseUrl}laporan/detail/${item.id}" class="btn-detail">📄 Detail</a>
                        </div>
                    </div>
                `;
                
                const marker = L.circleMarker([item.latitude, item.longitude], {
                    radius: 10,
                    fillColor: fillColor,      // Warna isi = STATUS
                    color: borderColor,         // Warna border = PRIORITAS
                    weight: borderWeight,       // Ketebalan border = tingkat prioritas
                    opacity: 1,
                    fillOpacity: 0.9
                });
                
                marker.bindPopup(popupContent);
                marker.addTo(map);
                markers.push(marker);
            });
            
            console.log('Added', markers.length, 'markers to map');
        }
        
        // Display locations list
        function displayLocationList(laporan) {
            console.log('displayLocationList called with:', laporan ? laporan.length : 'null', 'items');
            const container = document.getElementById('locationList');
            console.log('Container element:', container);
            
            if (!container) {
                console.error('Location list container not found');
                return;
            }
            
            if (!laporan || laporan.length === 0) {
                console.log('No data to display');
                container.innerHTML = '<div class="no-reports" style="padding: 20px; text-align: center; color: #666;">Tidak ada laporan</div>';
                return;
            }
            
            console.log('Creating HTML for', laporan.length, 'items');
            const html = laporan.map((item, index) => {
                console.log('Processing item:', item.id, item.jenis_kerusakan);
                
                // Prioritas styling
                let prioritas = item.prioritas || 'sedang';
                let prioritasBg = '#ffc107';
                let prioritasColor = '#000';
                let prioritasIcon = '🟡';
                
                if (prioritas === 'tinggi') {
                    prioritasBg = '#dc3545';
                    prioritasColor = '#fff';
                    prioritasIcon = '🔴';
                } else if (prioritas === 'rendah') {
                    prioritasBg = '#28a745';
                    prioritasColor = '#fff';
                    prioritasIcon = '🟢';
                }
                
                return `
                    <div class="location-item" onclick="zoomToLocation(${item.latitude}, ${item.longitude})" style="border-left-color: ${prioritasBg};">
                        <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 6px;">
                            <div class="location-item-title">${item.jenis_kerusakan}</div>
                            <span style="background: ${prioritasBg}; color: ${prioritasColor}; padding: 2px 8px; border-radius: 10px; font-size: 10px; font-weight: bold; white-space: nowrap;">
                                ${prioritasIcon} ${prioritas.charAt(0).toUpperCase() + prioritas.slice(1)}
                            </span>
                        </div>
                        <div class="location-item-desc">${item.deskripsi ? item.deskripsi.substring(0, 50) : 'No description'}...</div>
                        <span class="location-status status-${item.status.toLowerCase().replace(' ', '-')}">${item.status}</span>
                    </div>
                `;
            }).join('');
            
            container.innerHTML = html;
            console.log('Location list updated with', laporan.length, 'items');
        }
        
        // Zoom to location with smooth fly animation
        function zoomToLocation(lat, lng) {
            // Close mobile panel if open
            if (window.innerWidth <= 768) {
                const panelLocations = document.querySelector('.panel-locations');
                const backdrop = document.getElementById('mobileBackdrop');
                if (panelLocations) panelLocations.classList.remove('open');
                if (backdrop) backdrop.classList.remove('show');
            }
            
            // Fly to location with smooth animation
            map.flyTo([lat, lng], 16, {
                duration: 1.5,
                easeLinearity: 0.25
            });
        }
        
        // 🎯 Add "Go to my location" button (GPS button)
        function addGoToLocationButton() {
            const GoToLocation = L.Control.extend({
                options: {
                    position: 'topright'
                },
                
                onAdd: function(map) {
                    const container = L.DomUtil.create('div', 'leaflet-control leaflet-bar');
                    container.style.backgroundColor = 'white';
                    container.style.borderRadius = '4px';
                    container.style.boxShadow = '0 1px 5px rgba(0,0,0,0.2)';
                    container.style.marginTop = '50px';  // Space below zoom control
                    
                    const btn = L.DomUtil.create('button', 'go-to-location-btn', container);
                    btn.innerHTML = '📍';
                    btn.style.border = 'none';
                    btn.style.background = 'white';
                    btn.style.cursor = 'pointer';
                    btn.style.display = 'flex';
                    btn.style.alignItems = 'center';
                    btn.style.justifyContent = 'center';
                    btn.style.transition = 'all 0.2s ease';
                    btn.title = 'Go to my location';
                    
                    btn.onmouseenter = function() {
                        btn.style.background = '#f0f0f0';
                    };
                    btn.onmouseleave = function() {
                        btn.style.background = 'white';
                    };
                    
                    btn.onclick = function(e) {
                        L.DomEvent.stopPropagation(e);
                        L.DomEvent.preventDefault(e);
                        
                        if (userLocation) {
                            console.log('Flying to user location:', userLocation);
                            map.flyTo(userLocation, 16, {
                                duration: 1.5,
                                easeLinearity: 0.25
                            });
                            
                            // Add pulse animation to button
                            btn.style.transform = 'scale(0.9)';
                            setTimeout(() => {
                                btn.style.transform = 'scale(1)';
                            }, 150);
                        } else {
                            alert('📍 Lokasi Anda tidak tersedia. Silakan aktifkan GPS/geolocation di perangkat Anda.');
                        }
                    };
                    
                    return container;
                }
            });
            
            new GoToLocation().addTo(map);
        }
        
        // Search functionality
        const searchInput = document.getElementById('searchLocation');
        if (searchInput) {
            searchInput.addEventListener('keyup', (e) => {
                const query = e.target.value.toLowerCase();
                const filtered = allLaporan.filter(item => 
                    item.jenis_kerusakan.toLowerCase().includes(query) ||
                    item.deskripsi.toLowerCase().includes(query)
                );
                displayLocationList(filtered);
                displayMarkers(filtered);
            });
        }
        
        // Filter event listeners - setup after data loads
        function setupFilterListeners() {
            console.log('🔧 Setting up filter listeners...');
            const checkboxes = document.querySelectorAll('.filter-checkbox');
            console.log('Found checkboxes:', checkboxes.length);
            
            if (checkboxes.length === 0) {
                console.warn('No checkboxes found!');
                return;
            }
            
            checkboxes.forEach(checkbox => {
                checkbox.addEventListener('change', function() {
                    console.log('✓ Checkbox changed:', this.value, '-> Checked:', this.checked);
                    
                    // Sync state to all checkboxes with same value
                    document.querySelectorAll('.filter-checkbox[value="' + this.value + '"]').forEach(cb => {
                        cb.checked = this.checked;
                    });
                    
                    applyFilters();
                });
            });
            console.log('✓ Filter listeners attached!');
        }
        
        function applyFilters() {
            console.log('🔧 applyFilters triggered');
            
            if (!allLaporan || allLaporan.length === 0) {
                console.log('❌ No data loaded');
                return;
            }
            
            // Get ALL checkboxes regardless of visibility
            const allCheckboxes = document.querySelectorAll('.filter-checkbox');
            console.log('Total checkboxes found:', allCheckboxes.length);
            
            // Determine checked state - track which values have at least one checked checkbox
            const checkedValues = new Set();
            allCheckboxes.forEach(checkbox => {
                if (checkbox.checked) {
                    checkedValues.add(checkbox.value);
                    console.log('  ✓', checkbox.value, 'is checked');
                }
            });
            
            console.log('Checked values:', Array.from(checkedValues));
            
            // Filter data based on checked values
            let filtered = [];
            if (checkedValues.size === 0) {
                // Nothing checked = show all
                filtered = allLaporan;
                console.log('No filters selected, showing all');
            } else {
                // Show only items that match checked values
                filtered = allLaporan.filter(item => checkedValues.has(item.jenis_kerusakan));
                console.log('Filtered to:', filtered.length, 'items');
            }
            
            console.log('Final result: ' + filtered.length + '/' + allLaporan.length + ' items');
            displayLocationList(filtered);
            displayMarkers(filtered);
        }
        
        // Layer control
        document.querySelectorAll('input[name="baseLayer"]').forEach(radio => {
            radio.addEventListener('change', (e) => {
                if (currentBaseLayer) map.removeLayer(currentBaseLayer);
                currentBaseLayer = baseLayers[e.target.value];
                currentBaseLayer.addTo(map);
            });
        });
        
        // Initialize when DOM is ready
        function startApplication() {
            console.log('🚀 Starting application...');
            
            // Initialize map first
            initMap();
            
            // Wait for map to be ready, then load data
            setTimeout(() => {
                console.log('📊 Initializing charts...');
                initCharts();  // Initialize pie charts
                
                // Initialize mobile charts if on mobile
                if (window.innerWidth <= 768) {
                    console.log('📱 Initializing mobile charts...');
                    setTimeout(initMobileCharts, 100);
                }
                
                setTimeout(() => {
                    console.log('📡 Loading data...');
                    loadLaporan();
                }, 200);
                
                console.log('📱 Setting up mobile...');
                initMobileToggle();
                
                // Initialize Slide Panel AFTER map is ready
                console.log('📝 Initializing slide panel...');
                initSlidePanel();
            }, 300);
            
            console.log('✅ Application started');
        }
        
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', startApplication);
        } else {
            startApplication();
        }
        
        // Mobile panel toggle functionality
        function initMobileToggle() {
            if (window.innerWidth <= 768) {
                const panels = document.querySelectorAll('.floating-panel');
                const backdrop = document.getElementById('mobileBackdrop');
                const menuBtn = document.getElementById('mobileMenuBtn');
                const panelLocations = document.querySelector('.panel-locations');
                
                // Helper function to close all panels
                function closeAllPanels() {
                    panels.forEach(p => p.classList.remove('open'));
                    if (backdrop) backdrop.classList.remove('show');
                }
                
                // Direct toggle panel locations on button click (no menu)
                if (menuBtn && panelLocations) {
                    menuBtn.addEventListener('click', function(e) {
                        e.stopPropagation();
                        const isOpen = panelLocations.classList.contains('open');
                        
                        if (isOpen) {
                            closeAllPanels();
                        } else {
                            panelLocations.classList.add('open');
                            if (backdrop) backdrop.classList.add('show');
                        }
                        
                        console.log('Panel toggled, isOpen:', !isOpen);
                    });
                }
                
                // Panel header click to toggle or close
                panels.forEach(panel => {
                    // Skip stats panel (always visible)
                    if (panel.classList.contains('panel-stats')) return;
                    
                    const header = panel.querySelector('h6:first-of-type');
                    if (header) {
                        header.addEventListener('click', function(e) {
                            e.stopPropagation();
                            const isOpen = panel.classList.contains('open');
                            
                            if (isOpen) {
                                // Close panel
                                closeAllPanels();
                            } else {
                                // Open panel
                                closeAllPanels();
                                panel.classList.add('open');
                                if (backdrop) backdrop.classList.add('show');
                            }
                        });
                    }
                });
                
                // Close on backdrop click
                if (backdrop) {
                    backdrop.addEventListener('click', function(e) {
                        e.stopPropagation();
                        console.log('Backdrop clicked - closing panels');
                        closeAllPanels();
                    });
                }
            }
        }
        
        // Re-init on window resize
        window.addEventListener('resize', function() {
            initMobileToggle();
        });
        
        // =====================================================
        // SLIDE PANEL - Lapor Kerusakan Handler  
        // =====================================================
        
        // Variables (global scope for slide panel)
        let reportMarker = null;
        let isSelectingLocation = false;
        
        // reCAPTCHA callback (must be global scope)
        window.enablePanelSubmit = function() {
            const btn = document.getElementById('btnSubmitPanel');
            if (btn) btn.disabled = false;
        };
        
        function initSlidePanel() {
            console.log('🔧 initSlidePanel() called');
            console.log('🔧 Map exists:', !!map);
            
            if (!map) {
                console.error('❌ Map not ready!');
                return;
            }
            
            const slidePanel = document.getElementById('slidePanel');
            const mapClickIndicator = document.getElementById('mapClickIndicator');
            const btnOpenSlidePanel = document.getElementById('btnOpenSlidePanel');
            const btnCloseSlidePanel = document.getElementById('btnCloseSlidePanel');
            const btnSubmitPanel = document.getElementById('btnSubmitPanel');
            const btnGpsPanel = document.getElementById('btnGpsPanel');
            
            console.log('🔧 Elements found:', {
                slidePanel: !!slidePanel,
                btnOpenSlidePanel: !!btnOpenSlidePanel,
                btnCloseSlidePanel: !!btnCloseSlidePanel
            });
            
            // Function to update marker
            function updateReportMarker(lat, lng) {
                lat = parseFloat(lat).toFixed(6);
                lng = parseFloat(lng).toFixed(6);
                console.log('📍 Setting marker at:', lat, lng);
                
                if (reportMarker) {
                    map.removeLayer(reportMarker);
                }
                
                reportMarker = L.circleMarker([lat, lng], {
                    radius: 15,
                    fillColor: '#e74c3c',
                    color: '#fff',
                    weight: 4,
                    opacity: 1,
                    fillOpacity: 0.9
                }).addTo(map);
                
                reportMarker.bindPopup('<b>📍 Lokasi Laporan</b><br>Klik peta untuk pindah').openPopup();
                
                document.getElementById('panelLatitude').value = lat;
                document.getElementById('panelLongitude').value = lng;
                document.getElementById('panelLatDisplay').textContent = lat;
                document.getElementById('panelLngDisplay').textContent = lng;
            }
            
            // Open panel
            function openPanel() {
                console.log('📂 Opening panel...');
                slidePanel.classList.add('active');
                document.body.classList.add('map-click-mode');
                isSelectingLocation = true;
                
                map.closePopup();
                
                const center = map.getCenter();
                updateReportMarker(center.lat, center.lng);
            }
            
            // Close panel
            function closePanel() {
                console.log('📁 Closing panel...');
                slidePanel.classList.remove('active');
                document.body.classList.remove('map-click-mode');
                isSelectingLocation = false;
                
                if (reportMarker) {
                    map.removeLayer(reportMarker);
                    reportMarker = null;
                }
            }
            
            // Button listeners
            if (btnOpenSlidePanel) {
                btnOpenSlidePanel.onclick = function(e) {
                    e.preventDefault();
                    openPanel();
                };
                console.log('✅ Open button ready');
            }
            
            if (btnCloseSlidePanel) {
                btnCloseSlidePanel.onclick = function(e) {
                    e.preventDefault();
                    closePanel();
                };
                console.log('✅ Close button ready');
            }
            
            // MAP CLICK - the main event
            map.on('click', function(e) {
                if (!isSelectingLocation) return;
                
                console.log('🗺️ Map clicked at:', e.latlng.lat, e.latlng.lng);
                updateReportMarker(e.latlng.lat, e.latlng.lng);
                
                if (mapClickIndicator) {
                    mapClickIndicator.innerHTML = '<i class="fas fa-check"></i> Lokasi dipilih!';
                    mapClickIndicator.style.background = '#28a745';
                    setTimeout(function() {
                        mapClickIndicator.innerHTML = '<i class="fas fa-mouse-pointer"></i> Klik di peta untuk pilih lokasi';
                        mapClickIndicator.style.background = 'linear-gradient(135deg, #667eea 0%, #764ba2 100%)';
                    }, 1500);
                }
            });
            console.log('✅ Map click listener ready');
            
            // GPS Button
            if (btnGpsPanel) {
                btnGpsPanel.onclick = function() {
                    const statusDiv = document.getElementById('gpsStatusPanel');
                    btnGpsPanel.disabled = true;
                    btnGpsPanel.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Mencari...';
                    
                    if (!navigator.geolocation) {
                        statusDiv.textContent = '❌ GPS tidak didukung';
                        btnGpsPanel.disabled = false;
                        btnGpsPanel.innerHTML = '<i class="fas fa-location-arrow"></i> Gunakan GPS';
                        return;
                    }
                    
                    navigator.geolocation.getCurrentPosition(
                        function(pos) {
                            updateReportMarker(pos.coords.latitude, pos.coords.longitude);
                            map.setView([pos.coords.latitude, pos.coords.longitude], 16);
                            statusDiv.textContent = '✓ Lokasi ditemukan';
                            statusDiv.style.color = '#28a745';
                            btnGpsPanel.disabled = false;
                            btnGpsPanel.innerHTML = '<i class="fas fa-location-arrow"></i> Gunakan GPS';
                        },
                        function(err) {
                            statusDiv.textContent = '❌ ' + err.message;
                            statusDiv.style.color = '#dc3545';
                            btnGpsPanel.disabled = false;
                            btnGpsPanel.innerHTML = '<i class="fas fa-location-arrow"></i> Gunakan GPS';
                        },
                        {enableHighAccuracy: true, timeout: 10000}
                    );
                };
            }
            
            // Photo preview
            const panelFoto = document.getElementById('panelFoto');
            if (panelFoto) {
                panelFoto.onchange = function(e) {
                    const file = e.target.files[0];
                    const preview = document.getElementById('panelFotoPreview');
                    if (file && preview) {
                        const reader = new FileReader();
                        reader.onload = function(ev) {
                            preview.src = ev.target.result;
                            preview.style.display = 'block';
                        };
                        reader.readAsDataURL(file);
                    }
                };
            }
            
            // Form submit
            if (btnSubmitPanel) {
                btnSubmitPanel.onclick = function() {
                    const form = document.getElementById('formLaporSlide');
                    const email = document.getElementById('panelEmail').value.trim();
                    const jenis = document.getElementById('panelJenis').value;
                    const deskripsi = document.getElementById('panelDeskripsi').value.trim();
                    const foto = document.getElementById('panelFoto').files[0];
                    
                    if (!email || !jenis || !deskripsi || !foto) {
                        Swal.fire({icon: 'warning', title: 'Form Belum Lengkap', text: 'Mohon isi semua field', confirmButtonColor: '#666'});
                        return;
                    }
                    
                    const formData = new FormData(form);
                    btnSubmitPanel.disabled = true;
                    btnSubmitPanel.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Mengirim...';
                    
                    fetch('<?= base_url("laporan/simpan") ?>', {
                        method: 'POST', 
                        body: formData,
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    })
                    .then(r => {
                        // Check if response is ok
                        if (!r.ok) {
                            throw new Error('Server error: ' + r.status);
                        }
                        // Try to parse as JSON
                        return r.text().then(text => {
                            try {
                                return JSON.parse(text);
                            } catch (e) {
                                console.error('Response is not JSON:', text);
                                throw new Error('Invalid response format');
                            }
                        });
                    })
                    .then(data => {
                        if (data.success) {
                            Swal.fire({icon: 'success', title: 'Berhasil!', text: data.message, confirmButtonColor: '#666'})
                            .then(() => {
                                closePanel();
                                form.reset();
                                document.getElementById('panelFotoPreview').style.display = 'none';
                                loadLaporan();
                                if (typeof grecaptcha !== 'undefined') grecaptcha.reset();
                                btnSubmitPanel.disabled = true;
                            });
                        } else {
                            Swal.fire({icon: 'error', title: 'Gagal!', text: data.message || 'Terjadi kesalahan', confirmButtonColor: '#666'});
                            btnSubmitPanel.disabled = false;
                            if (typeof grecaptcha !== 'undefined') grecaptcha.reset();
                        }
                    })
                    .catch(err => {
                        console.error('Fetch error:', err);
                        Swal.fire({icon: 'error', title: 'Error!', text: 'Kesalahan jaringan: ' + err.message, confirmButtonColor: '#666'});
                        btnSubmitPanel.disabled = false;
                        if (typeof grecaptcha !== 'undefined') grecaptcha.reset();
                    })
                    .finally(() => {
                        btnSubmitPanel.innerHTML = '<i class="fas fa-paper-plane"></i> Kirim Laporan';
                    });
                };
            }
            
            // Escape key
            document.addEventListener('keydown', function(e) {
                if (e.key === 'Escape' && isSelectingLocation) closePanel();
            });
            
            console.log('✅ Slide panel fully initialized!');
        }
    </script>
</body>
</html>

