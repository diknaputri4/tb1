<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

include "service/database.php";
session_start();

$id_barang = $_GET['id'] ?? null;

if (!$id_barang) {
    header("Location: produk_bangunan.php");
    exit();
}

// Ambil data barang berdasarkan ID
$stmt = $db->prepare("SELECT * FROM tb_barang WHERE id_barang=?");
$stmt->bind_param("i", $id_barang);
$stmt->execute();
$result = $stmt->get_result();
$barang = $result->fetch_assoc();
$stmt->close();

if (!$barang) {
    echo "Barang tidak ditemukan.";
    exit();
}

// Proses update barang
if (isset($_POST['update'])) {
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

    header("Location: produk_bangunan.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Edit Barang</title>
</head>
<body>
<?php include "layout/header_admin.html"; ?>

<div class="content">
    <h2>Edit Barang</h2>

    <form method="POST" enctype="multipart/form-data">
        <label>Nama Barang:</label><br>
        <input type="text" name="nama_barang" value="<?= htmlspecialchars($barang['nama_barang']) ?>" required><br>

        <label>Harga:</label><br>
        <input type="number" name="harga" value="<?= $barang['harga'] ?>" required><br>

        <label>Model:</label><br>
        <input type="text" name="model" value="<?= htmlspecialchars($barang['model']) ?>" required><br>

        <label>SKU:</label><br>
        <input type="text" name="sku" value="<?= htmlspecialchars($barang['sku']) ?>" required><br>

        <label>Deskripsi:</label><br>
        <textarea name="deskripsi" rows="4"><?= htmlspecialchars($barang['deskripsi']) ?></textarea><br>

        <label>Gambar Saat Ini:</label><br>
        <?php if ($barang['gambar'] && file_exists('images/' . $barang['gambar'])): ?>
            <img src="images/<?= htmlspecialchars($barang['gambar']) ?>" style="width:100px;"><br>
        <?php else: ?>
            (Tidak ada gambar)<br>
        <?php endif; ?>

        <label>Ganti Gambar (opsional):</label><br>
        <input type="file" name="gambar" accept="image/*"><br><br>

        <button type="submit" name="update">Simpan Perubahan</button>
        <a href="produk_bangunan.php">Batal</a>
    </form>
</div>
</body>
</html>
