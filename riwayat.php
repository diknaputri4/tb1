<?php
include_once "service/database.php";
session_start();

if (!isset($_SESSION["is_login"])) {
    echo "Silakan login terlebih dahulu.";
    exit();
}

$username = $_SESSION["username"];

$stmt = $db->prepare("SELECT * FROM riwayat WHERE username = ? ORDER BY waktu DESC");
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Riwayat Belanja</title>
    <style>
        table {
            border-collapse: collapse;
            width: 100%;
            max-width: 900px;
            margin: 20px auto;
        }
        th, td {
            border: 1px solid #ccc;
            padding: 10px 15px;
            text-align: left;
        }
        th {
            background-color: #eee;
        }
        body {
            font-family: Arial, sans-serif;
            margin: 40px;
            background-color: #f9f9f9;
        }
        h1 {
            text-align: center;
        }
        .no-transaksi {
            text-align: center;
            margin-top: 40px;
            font-size: 1.2em;
            color: #555;
        }
    </style>
</head>
<body>
<?php include_once "layout/header.html"; ?>

<h1>Riwayat Belanja</h1>

<?php if ($result->num_rows > 0): ?>
    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Nama Barang</th>
                <th>Jumlah</th>
                <th>Harga Satuan</th>
                <th>Total Harga</th>
                <th>Catatan</th>
                <th>Waktu Pemesanan</th>
            </tr>
        </thead>
        <tbody>
            <?php 
            $no = 1;
            while ($row = $result->fetch_assoc()): 
                $harga = (int)$row['harga'];
                $jumlah = (int)$row['jumlah'];
                $total = (int)$row['total']; // Bisa juga hitung manual: $harga * $jumlah
            ?>
                <tr>
                    <td><?= $no++ ?></td>
                    <td><?= htmlspecialchars($row['nama_barang']) ?></td>
                    <td><?= $jumlah ?></td>
                    <td>Rp <?= number_format($harga, 0, ',', '.') ?></td>
                    <td>Rp <?= number_format($total, 0, ',', '.') ?></td>
                    <td><?= htmlspecialchars($row['catatan'] ?: '-') ?></td>
                    <td><?= $row['waktu'] ?></td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
<?php else: ?>
    <p class="no-transaksi">Belum ada transaksi.</p>
<?php endif; ?>

</body>
</html>
