<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

include "service/database.php";
session_start();
$barang_edit = false;
$search = $_GET['search'] ?? '';


// Query barang dengan search
if ($search) {
    $searchEsc = $db->real_escape_string($search);
    $query = "SELECT * FROM tb_barang 
              WHERE nama_barang LIKE '%$searchEsc%' 
                 OR model LIKE '%$searchEsc%' 
                 OR sku LIKE '%$searchEsc%'
              ORDER BY id_barang DESC";
} else {
    $query = "SELECT * FROM tb_barang ORDER BY id_barang DESC";
}

$barang = $db->query($query);

// Proses tambah barang
if (isset($_POST['simpan'])) {
    $nama_barang = $_POST['nama_barang'];
    $harga = $_POST['harga'];
    $model = $_POST['model'];
    $sku = $_POST['sku'];
    $deskripsi = $_POST['deskripsi'];
    $gambar = '';
    $id_penjual = 1; // Sesuaikan dengan ID penjual yang sedang login

    if (!empty($_FILES['gambar']['name'])) {
        $gambar = time() . '_' . basename($_FILES['gambar']['name']);
        move_uploaded_file($_FILES['gambar']['tmp_name'], 'images/' . $gambar);
    }

    $stmt = $db->prepare("INSERT INTO tb_barang (nama_barang, harga, model, sku, deskripsi, gambar, id_penjual) 
                          VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sissssi", $nama_barang, $harga, $model, $sku, $deskripsi, $gambar, $id_penjual);
    $stmt->execute();
    $stmt->close();

    header('Location: produk_bangunan.php');
    exit();
}

// Proses update barang
if (isset($_POST['update'])) {
    $id_barang = $_POST['id_barang'];
    $nama_barang = $_POST['nama_barang'];
    $harga = $_POST['harga'];
    $model = $_POST['model'];
    $sku = $_POST['sku'];
    $deskripsi = $_POST['deskripsi'];

    if (!empty($_FILES['gambar']['name'])) {
        $gambar = time() . '_' . basename($_FILES['gambar']['name']);
        move_uploaded_file($_FILES['gambar']['tmp_name'], 'images/' . $gambar);
        $stmt = $db->prepare("UPDATE tb_barang SET nama_barang=?, harga=?, model=?, sku=?, deskripsi=?, gambar=? WHERE id_barang=?");
        $stmt->bind_param("sissssi", $nama_barang, $harga, $model, $sku, $deskripsi, $gambar, $id_barang);
    } else {
        $stmt = $db->prepare("UPDATE tb_barang SET nama_barang=?, harga=?, model=?, sku=?, deskripsi=? WHERE id_barang=?");
        $stmt->bind_param("sisssi", $nama_barang, $harga, $model, $sku, $deskripsi, $id_barang);
    }
    $stmt->execute();
    $stmt->close();

    header('Location: produk_bangunan.php');
    exit();
}

// Proses hapus barang
if (isset($_GET['hapus'])) {
    $id_barang = $_GET['hapus'];
    $result = $db->query("SELECT gambar FROM tb_barang WHERE id_barang=$id_barang");
    if ($result && $result->num_rows > 0) {
        $row = $result->fetch_assoc();
        if ($row['gambar'] && file_exists('images/' . $row['gambar'])) {
            unlink('images/' . $row['gambar']);
        }
    }
    $db->query("DELETE FROM tb_barang WHERE id_barang=$id_barang");
    header('Location: produk_bangunan.php');
    exit();
}

// Ambil data untuk edit jika ada

?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Manajemen Barang - Toko Bangunan</title>
  <style>
    <style>
.produk-table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 20px;
    font-family: Arial, sans-serif;
    box-shadow: 0 2px 6px rgba(0,0,0,0.1);
}

.produk-table thead {
    background-color: #007bff;
    color: #fff;
}

.produk-table th, .produk-table td {
    padding: 10px 12px;
    text-align: left;
    border: 1px solid #ddd;
    vertical-align: top;
}

.produk-table tbody tr:nth-child(even) {
    background-color: #f9f9f9;
}

.produk-table tbody tr:hover {
    background-color: #eef6ff;
}

.produk-img {
    width: 80px;
    height: 80px;
    object-fit: cover;
    border-radius: 6px;
    box-shadow: 0 1px 3px rgba(0,0,0,0.2);
}

/* Tombol Aksi */
.btn {
    display: inline-block;
    padding: 6px 12px;
    text-decoration: none;
    border-radius: 4px;
    color: #fff;
    font-size: 14px;
    margin: 2px;
    transition: background 0.2s ease;
}

.btn.edit {
    background-color: #28a745;
}

.btn.edit:hover {
    background-color: #218838;
}

.btn.hapus {
    background-color: #dc3545;
}

.btn.hapus:hover {
    background-color: #c82333;
}
</style>

  </style>
</head>
<body>
<?php include "layout/header_admin.html"; ?>

<div class="content">
    <h1>Manajemen Barang - Toko Bangunan</h1>

    <?php if (!$barang_edit): ?>
    <a class="tambah" href="tambahbarang_admin.php">+ Tambah Barang</a>

    <?php else: ?>
    <a href="produk_bangunan.php" style="color:#007bff; text-decoration:none;">&larr; Kembali ke daftar barang</a>
    <?php endif; ?>

    <form id="searchForm" method="GET" action="">
        <input type="text" name="search" placeholder="Cari nama barang, model, atau SKU..." value="<?= htmlspecialchars($search) ?>">
        <button type="submit">Cari</button>
        <?php if ($search): ?>
            <a href="produk_bangunan.php" style="margin-left:10px; text-decoration:none; color:#dc3545;">Reset</a>
        <?php endif; ?>
    </form>

    

    

    <table class="produk-table">
    <thead>
        <tr>
            <th>No</th>
            <th>Nama Barang</th>
            <th>Harga</th>
            <th>Model</th>
            <th>SKU</th>
            <th>Deskripsi</th>
            <th>Gambar</th>
            <th>Aksi</th>
        </tr>
    </thead>
    <tbody>
        <?php if ($barang && $barang->num_rows > 0): ?>
            <?php $no = 1; while($b = $barang->fetch_assoc()): ?>
                <tr>
                    <td><?= $no++; ?></td>
                    <td><?= htmlspecialchars($b['nama_barang']); ?></td>
                    <td>Rp <?= number_format($b['harga'], 0, ',', '.'); ?></td>
                    <td><?= htmlspecialchars($b['model']); ?></td>
                    <td><?= htmlspecialchars($b['sku']); ?></td>
                    <td><?= htmlspecialchars($b['deskripsi']); ?></td>
                    <td>
                        <?php if (!empty($b['gambar']) && file_exists('images/' . $b['gambar'])): ?>
                            <img src="images/<?= htmlspecialchars($b['gambar']); ?>" alt="Gambar" class="produk-img">
                        <?php else: ?>
                            <span style="color: #888;">(Tidak ada gambar)</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <a href="edit_admin.php?id=<?= $b['id_barang'] ?>" class="btn edit">Edit</a>

                        <a href="?hapus=<?= $b['id_barang'] ?>" class="btn hapus" onclick="return confirm('Yakin hapus?')">Hapus</a>
                    </td>
                </tr>
            <?php endwhile; ?>
        <?php else: ?>
            <tr><td colspan="8" style="text-align:center;">Data barang tidak ditemukan.</td></tr>
        <?php endif; ?>
    </tbody>
</table>

</div>
</body>
</html>
