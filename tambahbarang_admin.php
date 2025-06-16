<?php
include "service/database.php";
session_start();

// Ambil id_penjual dari session (asumsi sudah disimpan saat login)
if (isset($_SESSION['id'])) {
    $id_penjual = $_SESSION['id'];
} else {
    // Kalau belum login, redirect ke halaman login
    header("Location: login.php");
    exit();
}

// Proses tambah barang
if (isset($_POST['simpan'])) {
    $nama_barang = $_POST['nama_barang'];
    $harga = $_POST['harga'];
    $model = $_POST['model'];
    $sku = $_POST['sku'];
    $deskripsi = $_POST['deskripsi'];
    $gambar = '';

    if (!empty($_FILES['gambar']['name'])) {
        $gambar = time() . '_' . basename($_FILES['gambar']['name']);
        move_uploaded_file($_FILES['gambar']['tmp_name'], 'images/' . $gambar);
    }

    $stmt = $db->prepare("INSERT INTO tb_barang (nama_barang, harga, model, sku, deskripsi, gambar, kode_penjual) 
                          VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sissssi", $nama_barang, $harga, $model, $sku, $deskripsi, $gambar, $kode_penjual);
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
    <title>Tambah Barang - Admin</title>
    <style>
        form {
            width: 400px;
            margin: auto;
            font-family: Arial;
        }
        input, textarea, button {
            width: 100%;
            margin: 6px 0;
            padding: 8px;
        }
        button {
            background-color: #28a745;
            color: white;
            border: none;
        }
        a {
            display: inline-block;
            margin-top: 10px;
            color: #007bff;
            text-decoration: none;
        }
    </style>
</head>
<body>

    <h1 style="text-align:center;">Tambah Barang Baru</h1>
    <form method="POST" enctype="multipart/form-data">
        <label>Nama Barang:</label>
        <input type="text" name="nama_barang" required>

        <label>Harga:</label>
        <input type="number" name="harga" required>

        <label>Model:</label>
        <input type="text" name="model" required>

        <label>SKU:</label>
        <input type="text" name="sku" required>

        <label>Deskripsi:</label>
        <textarea name="deskripsi" rows="4" required></textarea>

        <label>Gambar:</label>
        <input type="file" name="gambar" accept="image/*">

        <button type="submit" name="simpan">Simpan</button>
        <a href="produk_bangunan.php">← Kembali ke Daftar Barang</a>
    </form>

</body>
</html>
