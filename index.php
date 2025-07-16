<?php
include 'functions.php';

// Fungsi untuk mengklasifikasikan kategori penjualan berdasarkan jumlah
function klasifikasiKategori($penjualan) {
    if ($penjualan >= 100000000) {
        return "Sangat Tinggi";
    } elseif ($penjualan >= 50000000) {
        return "Sedang";
    } elseif ($penjualan >= 20000000) {
        return "Cukup";
    } elseif ($penjualan >= 10000000) {
        return "Rendah";
    } else {
        return "Sangat Rendah";
    }
}

// Fungsi untuk mendapatkan data penjualan per bulan selama 6 bulan terakhir
function getPenjualanPerBulan($penjualan) {
    $penjualanPerBulan = array_fill(0, 6, 0); // Array untuk menyimpan jumlah penjualan per bulan
    $currentMonth = (int)date('m');
    $currentYear = (int)date('Y');

    foreach ($penjualan as $p) {
        $penjualanDate = new DateTime($p[0]);
        $monthDiff = ($currentYear - $penjualanDate->format('Y')) * 12 + ($currentMonth - $penjualanDate->format('m'));
        
        if ($monthDiff >= 0 && $monthDiff < 6) {
            $penjualanPerBulan[$monthDiff] += $p[3]; // Menambahkan penjualan pada bulan yang sesuai
        }
    }

    return array_reverse($penjualanPerBulan); // Membalik agar bulan terakhir muncul pertama
}

// Fungsi untuk menghitung produk terlaris berdasarkan kategori
function getProdukTerlarisPerKategori($penjualan) {
    $produkPerKategori = [];
    foreach ($penjualan as $p) {
        $kategori = klasifikasiKategori($p[3]);
        if (!isset($produkPerKategori[$kategori])) {
            $produkPerKategori[$kategori] = [];
        }
        $produkPerKategori[$kategori][$p[1]] = isset($produkPerKategori[$kategori][$p[1]]) 
            ? $produkPerKategori[$kategori][$p[1]] + $p[2] 
            : $p[2]; // Menambahkan jumlah produk per kategori
    }
    return $produkPerKategori;
}

// Load data penjualan
$penjualan = readData("data/penjualan.txt");

// Proses pencarian data penjualan berdasarkan nama produk dan tanggal
$produkPencarian = isset($_GET['produk']) ? $_GET['produk'] : '';
$tanggalPencarian = isset($_GET['tanggal']) ? $_GET['tanggal'] : '';
$penjualanTersaring = [];

// Proses pencarian jika tombol cari ditekan
if (!empty($produkPencarian) || !empty($tanggalPencarian)) {
    foreach ($penjualan as $p) {
        if ((empty($produkPencarian) || strpos(strtolower($p[1]), strtolower($produkPencarian)) !== false) && 
            (empty($tanggalPencarian) || strpos($p[0], $tanggalPencarian) !== false)) {
            $penjualanTersaring[] = $p;
        }
    }
}

// Menambahkan kategori ke data penjualan
$penjualanDenganKategori = [];
foreach ($penjualanTersaring as $p) {
    $penjualanAmount = $p[3]; // Total penjualan per transaksi
    $kategori = klasifikasiKategori($penjualanAmount);
    $p[] = $kategori;
    $penjualanDenganKategori[] = $p;
}

// Urutkan data penjualan berdasarkan penghasilan (descending)
usort($penjualanDenganKategori, function($a, $b) {
    return $b[3] - $a[3]; // Urutkan berdasarkan penjualan (index ke-3)
});

// Ambil statistik penjualan per bulan
$penjualanPerBulan = getPenjualanPerBulan($penjualan);

// Ambil produk terlaris per kategori
$produkTerlarisPerKategori = getProdukTerlarisPerKategori($penjualan);
?>

<link rel="stylesheet" href="style.css">
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script> <!-- Library Chart.js -->

<!-- Bagian Sidebar -->
<div class="sidebar">
    <h2>PT. Niaga Mandiri</h2>
    <a href="index.php" class="active">Dashboard</a>
    <a href="penjualan.php">Data Penjualan</a>
    <a href="produk.php">Produk</a>
    <a href="kategori.php">Kategori Produk</a>
</div>

<!-- Bagian Content -->
<div class="content">
    <h1>Dashboard</h1>

      <!-- Menampilkan Statistik Penjualan Per Bulan -->
    <h2>📊 Statistik Penjualan Selama 6 Bulan Terakhir</h2>
    <canvas id="penjualanPerBulanChart" width="200" height="200"></canvas>
    <script>
        const penjualanPerBulanCtx = document.getElementById('penjualanPerBulanChart').getContext('2d');
        const penjualanPerBulanChart = new Chart(penjualanPerBulanCtx, {
            type: 'line',
            data: {
                labels: ['6 Bulan Lalu', '5 Bulan Lalu', '4 Bulan Lalu', '3 Bulan Lalu', '2 Bulan Lalu', 'Bulan Ini'],
                datasets: [{
                    label: 'Jumlah Penjualan (Rp)',
                    data: <?php echo json_encode($penjualanPerBulan); ?>,
                    borderColor: '#FF6347',
                    fill: false,
                }]
            }
        });
    </script>

    <!-- Pencarian Penjualan Berdasarkan Nama Produk dan Tanggal -->
    <h2> Pencarian Penjualan</h2>
    <form method="GET" action="index.php">
        <input type="text" name="produk" placeholder="Cari Produk" value="<?= htmlspecialchars($produkPencarian) ?>" />
        <input type="date" name="tanggal" placeholder="Cari Tanggal" value="<?= htmlspecialchars($tanggalPencarian) ?>" />
        <button type="submit">Cari</button>
    </form>

    <!-- Menampilkan Hasil Pencarian setelah tombol cari ditekan -->
    <?php if (!empty($penjualanTersaring)): ?>
        <h2>📈 Hasil Pencarian Penjualan</h2>
        <ul>
            <?php foreach ($penjualanTersaring as $p): ?>
                <li><strong><?= $p[0] ?></strong> | <?= htmlspecialchars($p[1]) ?> | <?= number_format($p[2]) ?> pcs | Rp<?= number_format($p[3]) ?> | Kategori: <?= $p[4] ?></li>
            <?php endforeach; ?>
        </ul>
    <?php elseif (!empty($produkPencarian) || !empty($tanggalPencarian)): ?>
        <p>Tidak ada hasil pencarian untuk produk atau tanggal yang dipilih.</p>
    <?php endif; ?>


    <!-- Menampilkan Diagram Pie untuk Kategori Penjualan -->
    <h2>📊 Diagram Pie Kategori Penjualan</h2>
    <canvas id="kategoriChart" width="200" height="200"></canvas> <!-- Ukuran Pie Chart Lebih Kecil -->
    <script>
        const kategoriCtx = document.getElementById('kategoriChart').getContext('2d');
        const kategoriChart = new Chart(kategoriCtx, {
            type: 'pie',
            data: {
                labels: ["Sangat Tinggi", "Sedang", "Cukup", "Rendah", "Sangat Rendah"],
                datasets: [{
                    label: 'Kategori Penjualan',
                    data: [
                        <?php echo count(array_filter($penjualanDenganKategori, function($p) { return $p[4] == "Sangat Tinggi"; })); ?>,
                        <?php echo count(array_filter($penjualanDenganKategori, function($p) { return $p[4] == "Sedang"; })); ?>,
                        <?php echo count(array_filter($penjualanDenganKategori, function($p) { return $p[4] == "Cukup"; })); ?>,
                        <?php echo count(array_filter($penjualanDenganKategori, function($p) { return $p[4] == "Rendah"; })); ?>,
                        <?php echo count(array_filter($penjualanDenganKategori, function($p) { return $p[4] == "Sangat Rendah"; })); ?>
                    ],
                    backgroundColor: ['#FF6347', '#FFCD38', '#FFD700', '#4CAF50', '#8B0000'],
                    borderColor: '#fff',
                    borderWidth: 1
                }]
            }
        });
    </script>


    <!-- Produk Terlaris Berdasarkan Kategori -->
    <h2>📊 Produk Terlaris Berdasarkan Kategori</h2>
    <ul>
        <?php foreach ($produkTerlarisPerKategori as $kategori => $produk): ?>
            <li><strong><?= $kategori ?></strong>: 
                <?php arsort($produk); // Urutkan berdasarkan jumlah produk terjual
                foreach ($produk as $namaProduk => $jumlah) {
                    echo "$namaProduk: $jumlah pcs, ";
                } ?>
            </li>
        <?php endforeach; ?>
    </ul>

    <!-- Tabel Data Penjualan dengan Kategori -->
    <h2>📊 Tabel Data Penjualan dengan Kategori</h2>
    <table>
        <tr>
            <th>No</th>
            <th>Tanggal</th>
            <th>Produk</th>
            <th>Jumlah Terjual</th>
            <th>Penjualan (Rp)</th>
            <th>Kategori</th>
        </tr>
        <?php $i = 1; foreach ($penjualanDenganKategori as $p): ?>
            <tr>
                <td><?= $i++ ?></td>
                <td><?= $p[0] ?></td>
                <td><?= $p[1] ?></td>
                <td><?= number_format($p[2]) ?> pcs</td>
                <td>Rp <?= number_format($p[3]) ?></td>
                <td><?= $p[4] ?></td>
            </tr>
        <?php endforeach; ?>
    </table>
</div>

<!-- CSS -->
<style>
    /* Mengatur ukuran diagram pie */
    #kategoriChart, #penjualanPerBulanChart {
        max-width: 100%;
        max-height: 300px;
        margin: 0 auto;
    }
</style>
