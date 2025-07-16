<?php
include 'functions.php';

$filename = "data/kategori.txt";
$data = readData($filename);

// Proses tambah/update kategori
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $namaKategori = trim($_POST['nama_kategori']);
    if (!empty($namaKategori)) {
        if (isset($_POST['add'])) {
            addData($filename, [$namaKategori]);
        } elseif (isset($_POST['update']) && isset($_POST['index'])) {
            $index = intval($_POST['index']);
            if (isset($data[$index])) {
                updateData($filename, $index, [$namaKategori]);
            }
        }
    }
    header("Location: kategori.php");
    exit;
}

// Proses hapus kategori
if (isset($_GET['delete'])) {
    $index = intval($_GET['delete']);
    if (isset($data[$index])) {
        deleteData($filename, $index);
    }
    header("Location: kategori.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Data Kategori</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"> <!-- Menambahkan Font Awesome -->
   
</head>
<body>

<div class="sidebar">
    <h2>PT. Niaga Mandiri</h2>
    <a href="index.php">Dashboard</a>
    <a href="penjualan.php">Data Penjualan</a>
    <a href="produk.php">Produk</a>
    <a href="kategori.php" class="active">Kategori Produk</a>
</div>

<div class="content">
    <h1>Kelola Kategori Produk</h1>
    <button onclick="openModal()">+ Tambah Data Kategori</button>

    <!-- Modal -->
    <div id="modalForm" class="modal">
        <div class="modal-content">
            <span onclick="closeModal()" class="close">&times;</span>
            <h2 id="modalTitle">Tambah Kategori</h2>
            <form method="POST" id="kategoriForm">
                <input type="hidden" name="index" id="index">
                <input type="text" name="nama_kategori" id="nama_kategori" placeholder="Nama Kategori" required>
                <button type="submit" name="add" id="submitBtn">Simpan</button>
            </form>
        </div>
    </div>

    <h2>🏷️ Daftar Kategori</h2>
    <table>
        <tr>
            <th>No</th>
            <th>Nama Kategori</th>
            <th>Aksi</th>
        </tr>
        <?php if (count($data) > 0): ?>
            <?php foreach ($data as $i => $row): ?>
                <tr>
                    <td><?= $i + 1 ?></td>
                    <td><?= htmlspecialchars($row[0]) ?></td>
                    <td>
                        <button class="btn-edit" onclick="editData(<?= $i ?>, '<?= htmlspecialchars($row[0]) ?>')">
                            <i class="fas fa-pencil-alt" style="margin-right: 5px;"></i> Edit
                        </button>
                        <a href="?delete=<?= $i ?>" class="btn-hapus" onclick="return confirm('Hapus kategori ini?')">
                            <i class="fas fa-trash-alt" style="margin-right: 5px;"></i> Hapus
                        </a>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr>
                <td colspan="3">Belum ada kategori.</td>
            </tr>
        <?php endif; ?>
    </table>

</div>

<script>
function openModal() {
    document.getElementById("modalForm").style.display = "block";
    document.getElementById("modalTitle").innerText = "Tambah Kategori";
    document.getElementById("submitBtn").name = "add";
    document.getElementById("kategoriForm").reset();
    document.getElementById("index").value = "";
}

function closeModal() {
    document.getElementById("modalForm").style.display = "none";
}

function editData(index, nama) {
    openModal();
    document.getElementById("modalTitle").innerText = "Edit Kategori";
    document.getElementById("nama_kategori").value = nama;
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
