<?php
session_start();
include_once "service/database.php";

if (!isset($_SESSION['is_login']) || $_SESSION['role'] != 'penjual') {
    header("Location: login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8" />
    <title>Laporan Pesanan per Pembeli</title>
    <style>
        body { font-family: Arial, sans-serif; background: #f0f0f0; }
        .content { padding: 20px; }
        h2 { text-align: center; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { padding: 10px; border: 1px solid #ccc; text-align: center; }
        th { background-color: #343a40; color: white; }
        tr:nth-child(even) { background-color: #f9f9f9; }
        a.detail-link { color: #007bff; text-decoration: none; }
        a.detail-link:hover { text-decoration: underline; }
    </style>
</head>
<body>

<?php include "layout/header_admin.html"; ?>

<div class="content">
    <h2>Laporan Pesanan Berdasarkan Pembeli</h2>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Username</th>
                <th>Jumlah Pesanan</th>
                <th>Total Item</th>
                <th>Total Harga</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
        <?php
        $no = 1;
        $query = "
            SELECT 
                username, 
                COUNT(*) AS total_pesanan, 
                SUM(jumlah) AS total_item, 
                SUM(jumlah * harga) AS total_harga 
            FROM riwayat 
            GROUP BY username 
            ORDER BY total_harga DESC
        ";
        $result = $db->query($query);

        if ($result && $result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $username = htmlspecialchars($row['username']);
                echo "<tr>";
                echo "<td>" . $no++ . "</td>";
                echo "<td>$username</td>";
                echo "<td>" . $row['total_pesanan'] . "</td>";
                echo "<td>" . $row['total_item'] . "</td>";
                echo "<td>Rp " . number_format($row['total_harga'], 0, ',', '.') . "</td>";
                echo "<td><a class='detail-link' href='detail_laporan.php?user=" . urlencode($row['username']) . "'>Lihat Detail</a></td>";
                echo "</tr>";
            }
        } else {
            echo "<tr><td colspan='6'>Tidak ada data pesanan.</td></tr>";
        }
        ?>
        </tbody>
    </table>
</div>

</body>
</html>
