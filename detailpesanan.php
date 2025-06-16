<?php
session_start();

if (!isset($_SESSION["is_login"])) {
    header("Location: login.php");
    exit();
}

if (!isset($_SESSION['pesanan_terakhir']) || empty($_SESSION['pesanan_terakhir'])) {
    echo "Tidak ada data pesanan ditemukan.";
    exit();

}
$total_harga = $_SESSION['total_harga_terakhir'] ?? 0;
unset($_SESSION['total_harga_terakhir']);


$pesanan = $_SESSION['pesanan_terakhir'];
unset($_SESSION['pesanan_terakhir']); // Hapus setelah ditampilkan
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Detail Pesanan</title>
    <link rel="stylesheet" href="css/style.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f4f4f4;
            margin: 0;
            padding: 20px;
            padding_left: 220px;
        }

        .container {
            max-width: 700px;
            margin: auto;
            background: #fff;
            padding: 30px 40px;
            border-radius: 12px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }

        h2 {
            text-align: center;
            margin-bottom: 25px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-left: 10px;
        }

        th, td {
            padding: 12px;
            border: 1px solid #ccc;
            text-align: center;
        }

        th {
            background: #f2f2f2;
        }

        .back-btn {
            display: block;
            text-align: center;
            margin-top: 20px;
        }

        .back-btn a {
            text-decoration: none;
            padding: 10px 20px;
            background: #4CAF50;
            color: white;
            border-radius: 6px;
        }

        .back-btn a:hover {
            background: #45a049;
        }
    </style>
</head>
<body>

<div class="container">
    <h2>Detail Pesanan Anda</h2>

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
        <?php foreach ($pesanan as $item): ?>
            <tr>
                <td><?= htmlspecialchars($item['nama_barang']) ?></td>
                <td>Rp <?= number_format($item['harga'], 0, ',', '.') ?></td>
                <td><?= $item['jumlah'] ?></td>
                <td>Rp <?= number_format($item['harga'] * $item['jumlah'], 0, ',', '.') ?></td>
                <td><?= htmlspecialchars($item['catatan']) ?: '-' ?></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
        <tfoot>
    <tr>
        <td colspan="3" style="text-align:right;"><strong>Total Harga Keseluruhan:</strong></td>
        <td colspan="2"><strong>Rp <?= number_format($total_harga, 0, ',', '.') ?></strong></td>
    </tr>
</tfoot>

    </table>

    <div class="back-btn">
        <a href="home.php">Kembali ke Home</a>
    </div>
</div>

</body>
</html>
