<?php
include 'functions.php';

$filename = "data/penjualan.txt";
$data = readData($filename);
$produk = readData("data/produk.txt");

// Fungsi untuk mengklasifikasikan kategori penjualan berdasarkan jumlah
function klasifikasiKategori($penjualan) {
    if ($penjualan >= 100000000) {
        return "Sangat Tinggi";  // Penjualan 100 juta ke atas
    } elseif ($penjualan >= 50000000) {
        return "Sedang";  // Penjualan antara 50 juta sampai 100 juta
    } elseif ($penjualan >= 20000000) {
        return "Cukup";  // Penjualan antara 20 juta sampai 50 juta
    } elseif ($penjualan >= 10000000) {
        return "Rendah";  // Penjualan antara 10 juta sampai 20 juta
    } else {
        return "Sangat Rendah";  // Penjualan kurang dari 10 juta
    }
}

// Fitur pencarian
$produkPencarian = isset($_GET['produk']) ? $_GET['produk'] : '';
$tanggalPencarian = isset($_GET['tanggal']) ? $_GET['tanggal'] : '';
$penjualanTersaring = [];

// Proses pencarian jika tombol cari ditekan
if (!empty($produkPencarian) || !empty($tanggalPencarian)) {
    foreach ($data as $p) {
        if ((empty($produkPencarian) || strpos(strtolower($p[1]), strtolower($produkPencarian)) !== false) && 
            (empty($tanggalPencarian) || strpos($p[0], $tanggalPencarian) !== false)) {
            $penjualanTersaring[] = $p;
        }
    }
} else {
    // Jika tidak ada pencarian, tampilkan semua data
    $penjualanTersaring = $data;
}

// Menambahkan kategori ke data penjualan
$penjualanDenganKategori = [];
foreach ($penjualanTersaring as $p) {
    $penjualanAmount = $p[3]; // Total penjualan per transaksi
    $kategori = klasifikasiKategori($penjualanAmount);
    $p[] = $kategori; // Menambahkan kategori ke data penjualan
    $penjualanDenganKategori[] = $p; // Menyimpan data penjualan beserta kategori
}

// Urutkan data penjualan berdasarkan penghasilan (descending)
usort($penjualanDenganKategori, function($a, $b) {
    return $b[3] - $a[3]; // Urutkan berdasarkan total penjualan (index ke-3)
});

// Proses Tambah / Update
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $tanggal = $_POST['tanggal'];
    $namaProduk = $_POST['nama_produk'];
    $jumlah = intval($_POST['jumlah']);

    $harga = 0;
    foreach ($produk as $p) {
        if ($p[0] == $namaProduk) {
            $harga = $p[2];
            break;
        }
    }
    $total = $jumlah * $harga;

    if (isset($_POST['add'])) {
        addData($filename, [$tanggal, $namaProduk, $jumlah, $total]);
    } elseif (isset($_POST['update']) && isset($_POST['index'])) {
        $index = intval($_POST['index']);
        updateData($filename, $index, [$tanggal, $namaProduk, $jumlah, $total]);
    }
    header("Location: penjualan.php");
    exit;
}

// Hapus
if (isset($_GET['delete'])) {
    $index = intval($_GET['delete']);
    deleteData($filename, $index);
    header("Location: penjualan.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Data Penjualan</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>

<body>

    <div class="sidebar">
        <h2>PT. Niaga Mandiri</h2>
        <a href="index.php">Dashboard</a>
        <a href="penjualan.php" class="active">Data Penjualan</a>
        <a href="produk.php">Produk</a>
        <a href="kategori.php">Kategori Produk</a>
    </div>

    <div class="content">
        <h1>Kelola Data Penjualan</h1>

        <!-- Tambah Data Penjualan -->
        <button onclick="openModal()">+ Tambah Data Penjualan</button>

        <!-- Modal Form -->
        <div id="modalForm" class="modal">
            <div class="modal-content">
                <span onclick="closeModal()" class="close">&times;</span>
                <h2 id="modalTitle">Tambah Data</h2>
                <form method="POST" id="penjualanForm">
                    <input type="hidden" name="index" id="index">
                    <input type="date" name="tanggal" id="tanggal" required>
                    <select name="nama_produk" id="nama_produk" required>
                        <option value="">-- Pilih Produk --</option>
                        <?php foreach ($produk as $p): ?>
                            <option value="<?= $p[0] ?>"><?= htmlspecialchars($p[0]) ?> (Rp<?= number_format($p[2]) ?>)</option>
                        <?php endforeach; ?>
                    </select>
                    <input type="number" name="jumlah" id="jumlah" placeholder="Jumlah" required>
                    <button type="submit" name="add" id="submitBtn">Tambah</button>
                </form>
            </div>
        </div>

        <!-- Pencarian Penjualan Berdasarkan Nama Produk dan Tanggal -->
        <h2>🔍 Pencarian Penjualan</h2>
        <form method="GET" action="penjualan.php">
            <input type="text" name="produk" placeholder="Cari Produk" value="<?= htmlspecialchars($produkPencarian) ?>" />
            <input type="date" name="tanggal" placeholder="Cari Tanggal" value="<?= htmlspecialchars($tanggalPencarian) ?>" />
            <button type="submit">Cari</button>
        </form>

        <!-- Menampilkan Data Penjualan Berdasarkan Pencarian -->
        <h2>📈 Daftar Penjualan</h2>
        <table>
            <tr>
                <th>No</th>
                <th>Tanggal</th>
                <th>Nama Produk</th>
                <th>Jumlah</th>
                <th>Total (Rp)</th>
                <th>Kategori</th>
                <th>Aksi</th>
            </tr>
            <?php foreach ($penjualanDenganKategori as $i => $row): ?>
                <tr>
                    <td><?= $i + 1 ?></td>
                    <td><?= htmlspecialchars($row[0]) ?></td>
                    <td><?= htmlspecialchars($row[1]) ?></td>
                    <td><?= $row[2] ?> pcs</td>
                    <td><?= number_format($row[3]) ?></td>
                    <td><?= htmlspecialchars($row[4]) ?></td> <!-- Menampilkan kategori -->
                    <td>
                        <button class="btn-edit" onclick="editData(<?= $i ?>, '<?= $row[0] ?>', '<?= $row[1] ?>', <?= $row[2] ?>)">
                            <i class="fas fa-pencil-alt" style="margin-right: 5px;"></i> Edit
                        </button>
                        <a href="?delete=<?= $i ?>" class="btn-hapus" onclick="return confirm('Hapus transaksi ini?')">
                            <i class="fas fa-trash-alt" style="margin-right: 5px;"></i> Hapus
                        </a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </table>
    </div>

    <script>
        function openModal() {
            document.getElementById("modalForm").style.display = "block";
            document.getElementById("modalTitle").innerText = "Tambah Data";
            document.getElementById("submitBtn").name = "add";
            document.getElementById("penjualanForm").reset();
            document.getElementById("index").value = "";
        }

        function closeModal() {
            document.getElementById("modalForm").style.display = "none";
        }

        function editData(index, tanggal, nama_produk, jumlah) {
            openModal();
            document.getElementById("modalTitle").innerText = "Edit Data";
            document.getElementById("tanggal").value = tanggal;
            document.getElementById("nama_produk").value = nama_produk;
            document.getElementById("jumlah").value = jumlah;
            document.getElementById("index").value = index;
            document.getElementById("submitBtn").name = "update";
        }

        window.onclick = function(event) {
            const modal = document.getElementById("modalForm");
            if (event.target == modal) {
                closeModal();
            }
        }
    </script>

</body>

</html>
