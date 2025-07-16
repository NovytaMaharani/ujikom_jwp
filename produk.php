<?php
include 'functions.php';

$filename = "data/produk.txt";
$data = readData($filename);
$kategori = readData("data/kategori.txt");

// Proses Tambah / Update
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $namaProduk = trim($_POST['nama_produk']);
    $kategoriProduk = trim($_POST['kategori']);
    $harga = intval($_POST['harga']);

    if (!empty($namaProduk) && !empty($kategoriProduk) && $harga > 0) {
        if (isset($_POST['add'])) {
            addData($filename, [$namaProduk, $kategoriProduk, $harga]);
        } elseif (isset($_POST['update']) && isset($_POST['index'])) {
            $index = intval($_POST['index']);
            if (isset($data[$index])) {
                updateData($filename, $index, [$namaProduk, $kategoriProduk, $harga]);
            }
        }
    }
    header("Location: produk.php");
    exit;
}

// Hapus
if (isset($_GET['delete'])) {
    $index = intval($_GET['delete']);
    if (isset($data[$index])) {
        deleteData($filename, $index);
    }
    header("Location: produk.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Data Produk</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">  <!-- Menambahkan Font Awesome -->
</head>

<body>

    <div class="sidebar">
        <h2>PT. Niaga Mandiri</h2>
        <a href="index.php">Dashboard</a>
        <a href="penjualan.php">Data Penjualan</a>
        <a href="produk.php" class="active">Produk</a>
        <a href="kategori.php">Kategori Produk</a>
    </div>

    <div class="content">
        <h1>Kelola Data Produk</h1>
        <button onclick="openModal()">+ Tambah Data Produk</button>

        <!-- Modal -->
        <div id="modalForm" class="modal">
            <div class="modal-content">
                <span onclick="closeModal()" class="close">&times;</span>
                <h2 id="modalTitle">Tambah Data Produk</h2>
                <form method="POST" id="produkForm">
                    <input type="hidden" name="index" id="index">
                    <input type="text" name="nama_produk" id="nama_produk" placeholder="Nama Produk" required>
                    <select name="kategori" id="kategori" required>
                        <option value="">-- Pilih Kategori --</option>
                        <?php foreach ($kategori as $k): ?>
                            <option value="<?= htmlspecialchars($k[0]) ?>"><?= htmlspecialchars($k[0]) ?></option>
                        <?php endforeach; ?>
                    </select>
                    <input type="number" name="harga" id="harga" placeholder="Harga" min="1" required>
                    <button type="submit" name="add" id="submitBtn">Simpan</button>
                </form>
            </div>
        </div>

        <h2>📝 Daftar Produk</h2>
        <table>
            <tr>
                <th>No</th>
                <th>Nama Produk</th>
                <th>Kategori</th>
                <th>Harga (Rp)</th>
                <th>Aksi</th>
            </tr>
            <?php foreach ($data as $i => $row): ?>
                <tr>
                    <td><?= $i + 1 ?></td>
                    <td><?= htmlspecialchars($row[0]) ?></td>
                    <td><?= htmlspecialchars($row[1]) ?></td>
                    <td><?= number_format($row[2]) ?></td>
                    <td>
                        <button class="btn-edit" onclick="editData(<?= $i ?>, '<?= $row[0] ?>', '<?= $row[1] ?>', <?= $row[2] ?>)">
                            <i class="fas fa-pencil-alt" style="margin-right: 5px;"></i> Edit
                        </button>
                        <a href="?delete=<?= $i ?>" class="btn-hapus" onclick="return confirm('Hapus produk ini?')">
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
            document.getElementById("modalTitle").innerText = "Tambah Produk";
            document.getElementById("submitBtn").name = "add";
            document.getElementById("produkForm").reset();
            document.getElementById("index").value = "";
        }

        function closeModal() {
            document.getElementById("modalForm").style.display = "none";
        }

        function editData(index, nama, kategori, harga) {
            openModal();
            document.getElementById("modalTitle").innerText = "Edit Produk";
            document.getElementById("nama_produk").value = nama;
            document.getElementById("kategori").value = kategori;
            document.getElementById("harga").value = harga;
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
