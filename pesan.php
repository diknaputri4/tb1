<?php
session_start();
include_once "service/database.php"; // Pastikan ini sesuai dengan jalur file database.php

// Pastikan user sudah login
if (!isset($_SESSION["is_login"])) {
    header("Location: login.php");
    exit();
}

$username = $_SESSION["username"];
$pesan = "";
$nama_barang = "";
$harga = 0;
$jumlah = 0;
$catatan = "";
$id_barang = 0;

// Jika user membuka halaman melalui klik nama barang
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['id_barang'])) {
    $id_barang = intval($_GET['id_barang']);

    // Ambil detail barang dari database
    $stmt = $db->prepare("SELECT * FROM tb_barang WHERE id_barang = ?");
    $stmt->bind_param("i", $id_barang);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($barang = $result->fetch_assoc()) {
        $nama_barang = $barang['nama_barang'];
        $harga = $barang['harga'];
    }
}

// Jika user mengirim form
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit'])) {
    $id_barang = intval($_POST['id_barang']);
    $nama_barang = $_POST['nama_barang'];
    $jumlah = intval($_POST['jumlah']);
    $catatan = $_POST['catatan'] ?? '';

    // Ambil harga dari database (agar valid walaupun ada perubahan harga)
    $stmt = $db->prepare("SELECT harga FROM tb_barang WHERE id_barang = ?");
    $stmt->bind_param("i", $id_barang);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($row = $result->fetch_assoc()) {
        $harga = (int)$row['harga'];
    } else {
        $harga = 0;
    }

    $total = $harga * $jumlah;

    // Simpan ke tabel riwayat
    $stmt = $db->prepare("INSERT INTO riwayat (username, id_barang, nama_barang, jumlah, harga, total, catatan, waktu) VALUES (?, ?, ?, ?, ?, ?, ?, NOW())");
    $stmt->bind_param("sisiiis", $username, $id_barang, $nama_barang, $jumlah, $harga, $total, $catatan);
    $stmt->execute();

    $pesan = "Pesanan berhasil";

    // Cek apakah data pembeli sudah ada
    $stmt = $db->prepare("SELECT * FROM tb_detail_pembeli WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 0) {
        // Auto buat data pembeli kosong
        $stmt = $db->prepare("INSERT INTO tb_detail_pembeli (username, nama_pembeli, no_telepon, alamat_pembeli) VALUES (?, '', '', '')");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $pembeli_detail = [
            'nama_pembeli' => '',
            'no_telepon' => '',
            'alamat_pembeli' => ''
        ];
    } else {
        $pembeli_detail = $result->fetch_assoc();
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Pesan Barang - Baja Hitam</title>
    <link rel="stylesheet" href="css/style.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
        }

        .form-container {
            max-width: 500px;
            margin: 40px auto;
            background-color: #fff;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }

        h2 {
            text-align: center;
            margin-bottom: 25px;
        }

        label {
            display: block;
            margin: 12px 0 6px;
            font-weight: bold;
        }

        input, textarea {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            border-radius: 6px;
            border: 1px solid #ccc;
        }

        input[readonly] {
            background-color: #eee;
        }

        button {
            width: 100%;
            background-color: #4CAF50;
            color: white;
            border: none;
            padding: 12px;
            border-radius: 6px;
            font-size: 16px;
            font-weight: bold;
            cursor: pointer;
        }

        .message {
            text-align: center;
            margin-bottom: 15px;
            font-weight: bold;
            color: green;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        th, td {
            padding: 12px;
            border: 1px solid #ccc;
        }

        th {
            background-color: #f2f2f2;
        }

        .back-btn {
            margin-top: 20px;
            text-align: center;
        }

        .back-btn a {
            display: inline-block;
            padding: 10px 20px;
            background-color: #4CAF50;
            color: white;
            text-decoration: none;
            border-radius: 6px;
            font-weight: bold;
            transition: background-color 0.3s ease;
        }

        .back-btn a:hover {
            background-color: #388e3c;
        }
    </style>
</head>
<body>

<?php include "layout/header.html"; ?>

<div class="form-container">
    <h2>Detail Pemesanan</h2>

    <?php if (!empty($pesan)): ?>
        <div class="message"><?= htmlspecialchars($pesan) ?></div>

        <?php if (isset($pembeli_detail) && $pembeli_detail['nama_pembeli'] == ''): ?>
            <p style="color: red; text-align: center;">
                ⚠️ Anda belum melengkapi data diri. <a href="profil.php">Lengkapi sekarang</a>.
            </p>
        <?php endif; ?>

        <table>
            <thead>
                <tr>
                    <th>Nama Barang</th>
                    <th>Harga Satuan</th>
                    <th>Jumlah</th>
                    <th>Total Harga</th>
                    <th>Catatan</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td><?= htmlspecialchars($nama_barang) ?></td>
                    <td>Rp <?= number_format($harga, 0, ',', '.') ?></td>
                    <td><?= htmlspecialchars($jumlah) ?></td>
                    <td>Rp <?= number_format($harga * $jumlah, 0, ',', '.') ?></td>
                    <td><?= htmlspecialchars($catatan) ?></td>
                </tr>
            </tbody>
        </table>
        <div class="back-btn">
            <a href="home.php">Kembali ke Home</a>
        </div>
    <?php else: ?>
        <form method="POST" action="pesan.php">
            <input type="hidden" name="id_barang" value="<?= htmlspecialchars($id_barang) ?>">

            <label for="nama_barang">Nama Barang</label>
            <input type="text" name="nama_barang" id="nama_barang" value="<?= htmlspecialchars($nama_barang) ?>" readonly required>

            <label for="harga">Harga</label>
            <input type="text" name="harga" id="harga" value="Rp <?= number_format($harga, 0, ',', '.') ?>" readonly required>

            <label for="jumlah">Jumlah</label>
            <input type="number" name="jumlah" id="jumlah" min="1" required>

            <label for="catatan">Catatan (Opsional)</label>
            <textarea name="catatan" id="catatan" rows="3"></textarea>

            <button type="submit" name="submit">Kirim Pesanan</button>
        </form>
    <?php endif; ?>
</div>

</body>
</html>
