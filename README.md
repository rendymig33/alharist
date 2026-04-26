# Kasir Desktop PHP CI3 Style

Starter aplikasi kasir desktop sederhana dengan tampilan minimarket, font Arial, dan penyimpanan lokal memakai SQLite.

## Fitur yang sudah dibuat

- Master data barang
- Master data pelanggan
- Satuan terbesar dan satuan terkecil barang
- Setting harga beli, harga jual, presentase keuntungan, harga satuan, harga di atas 3 buah
- Stok barang dan EXP date
- Pencatatan utang pelanggan
- Pencatatan transaksi
- Jenis transaksi: QRIS, Tunai, Hutang
- Brankas: daftar bank dan saldo bank
- Perhitungan keuntungan harian dengan modal default `200000`
- Export master ke Excel `.xls`
- Import data dari Excel `.csv`
- Data tetap tersimpan saat aplikasi ditutup karena memakai `storage/kasir.sqlite`
- Tampilan sederhana merah-kuning ala kasir minimarket

## Struktur

- `index.php` front controller
- `application/` controller, model, view
- `database/schema.sql` skema database
- `storage/kasir.sqlite` database lokal otomatis dibuat saat pertama jalan

## Cara menjalankan

1. Pastikan PHP 8+ aktif dengan ekstensi `pdo_sqlite`.
2. Masuk ke folder project ini.
3. Jalankan:

```powershell
php -S localhost:8080
```

4. Buka `http://localhost:8080`

## Catatan

- Struktur dibuat bergaya CodeIgniter 3 agar mudah dipindahkan ke project CI3 penuh.
- Karena workspace awal kosong dan framework CI3 asli tidak tersedia di folder ini, saya buatkan versi ringan yang mengikuti pola `controller / model / view`.
- Untuk import, gunakan file CSV dari Excel.
