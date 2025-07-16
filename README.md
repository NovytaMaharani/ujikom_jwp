# Sistem Manajemen Penjualan Berbasis Web - ujikom_jwp_novyta

Proyek ini adalah **Sistem Manajemen Penjualan Berbasis Web** yang dikembangkan untuk **PT. Niaga Mandiri** menggunakan PHP (native). Sistem ini dirancang untuk mengelola dan mengklasifikasikan data penjualan, mengelola kategori produk, serta memberikan analisis penjualan yang berguna. Aplikasi ini tidak menggunakan MySQL atau PHPMyAdmin, melainkan mengandalkan penyimpanan data dalam file teks (`txt`) dan CSV.

# Fitur 🚀

- **Dashboard**: Menampilkan statistik penjualan selama 6 bulan terakhir 📊.
- **Manajemen Produk**: Mengelola kategori produk, detail produk, dan harga 🛒.
- **Manajemen Data Penjualan**: Menambah, mengubah, dan menghapus data penjualan 📝.
- **Klasifikasi Penjualan**: Mengklasifikasikan penjualan ke dalam kategori seperti "Sangat Tinggi", "Tinggi", "Sedang", "Rendah", dan "Sangat Rendah" berdasarkan jumlah penjualan 🔥.
- **Pencarian**: Memungkinkan penyaringan data penjualan berdasarkan nama produk dan tanggal transaksi 🔍.
- **Diagram**: Menampilkan tren penjualan menggunakan diagram pie dan diagram garis dengan menggunakan Chart.js 📉.
- **Penyimpanan Data**: Data disimpan dalam file teks (`txt`) dan CSV (`penjualan.txt`, `kategori.txt`, `produk.txt`) 💾.

# Persyaratan 🛠️

- PHP 7.0 atau lebih tinggi
- Web server (Apache, Nginx, dll.)
- Pengetahuan dasar tentang PHP dan pengembangan web

# Instalasi 📝

1. **Clone repositori**:
   ```bash
   git clone https://github.com/username/ujikom_jwp_novyta.git
   cd ujikom_jwp_novyta
   ```
2. **Struktur File**:

   - `data/` : Berisi file data (`penjualan.txt`, `kategori.txt`, `produk.txt`).
   - `functions.php` : Berisi fungsi untuk membaca/menulis ke file teks.
   - `index.php` : Dashboard utama yang menampilkan statistik penjualan.
   - `kategori.php` : Mengelola kategori produk.
   - `produk.php` : Mengelola produk.
   - `penjualan.php` : Mengelola data penjualan.

3. **Pastikan server Anda terkonfigurasi** untuk menggunakan PHP. Anda bisa menggunakan `localhost` dengan Apache, atau server web lain yang Anda pilih.
4. **Buka di browser**: Akses `http://localhost/ujikom_jwp_novyta/index.php` untuk mulai menggunakan sistem.

# Penggunaan 💡

1. **Dashboard**: Melihat data penjualan untuk 6 bulan terakhir.
2. **Kategori Produk**: Menambah kategori baru atau memperbarui kategori yang ada.
3. **Produk**: Mengelola detail produk, termasuk nama, kategori, dan harga.
4. **Penjualan**: Menambah, mengubah, menghapus, dan mengklasifikasikan transaksi penjualan.
5. **Pencarian**: Menggunakan fitur pencarian untuk menemukan penjualan berdasarkan produk atau tanggal transaksi.

# Klasifikasi Penjualan 💰

- **Sangat Tinggi**: Penjualan lebih dari 100 juta.
- **Tinggi**: Penjualan antara 50 juta hingga 100 juta.
- **Sedang**: Penjualan antara 20 juta hingga 50 juta.
- **Rendah**: Penjualan antara 10 juta hingga 20 juta.
- **Sangat Rendah**: Penjualan kurang dari 10 juta.

# Format Data 🗂️

Data disimpan dalam file teks dengan setiap entri dipisahkan oleh karakter pipa (`|`). Format untuk setiap file adalah sebagai berikut:

- **penjualan.txt**:
  - `Tanggal|Nama Produk|Jumlah Terjual|Total Penjualan`
- **produk.txt**:
  - `Nama Produk|Kategori|Harga`
- **kategori.txt**:
  - `Nama Kategori`

# Lisensi 📄

Proyek ini dilisensikan di bawah **MIT License**.
