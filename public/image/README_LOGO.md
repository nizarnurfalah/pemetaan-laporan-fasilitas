# 📝 Petunjuk Menambahkan Logo Diskominfo

## 🎯 Untuk menampilkan logo Diskominfo di cover PDF:

1. **Siapkan file logo** dengan nama: `logo-diskominfo.png`
2. **Format yang disarankan**: PNG dengan background transparan
3. **Ukuran optimal**: 400x400 pixels atau 1:1 ratio
4. **Letakkan file di folder ini**: `public/image/logo-diskominfo.png`

## 📁 Struktur File:
```
public/
└── image/
    └── logo-diskominfo.png  ← Letakkan logo di sini
```

## ✅ Setelah logo ditambahkan:
- Logo akan muncul otomatis di cover PDF
- Ukuran logo di PDF: 40x40mm (terpusat)
- Jika logo tidak ada, akan muncul placeholder text

## 🔄 Jika ingin mengubah ukuran atau posisi logo:
Edit file: `app/Controllers/Admin/Dashboard.php`
Baris: `$pdf->Image($logoPath, 85, 30, 40, 40);`
- Parameter 1-2: posisi X,Y (mm)
- Parameter 3-4: lebar,tinggi (mm)