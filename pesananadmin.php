<?php
session_start();
include_once "service/database.php";
$username = $_SESSION['username']; // Pastikan ada session

$stmt = $db->prepare("INSERT INTO tb_detail_pembeli (username, nama_pembeli, no_telepon, alamat_pembeli) VALUES (?, ?, ?, ?)");
$stmt->bind_param("ssss", $username, $nama_pembeli, $no_telepon, $alamat_pembeli);



if (!isset($_SESSION['is_login']) || $_SESSION['role'] != 'penjual') {
    header("Location: login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8" />
    <title>Daftar Pesanan - Admin</title>
    <style>
        body { font-family: Arial, sans-serif; background: #f8f9fa; }
        .content { margin-left: 220px; padding: 20px; }
        table { border-collapse: collapse; width: 100%; margin-bottom: 40px; }
        th, td { border: 1px solid #ccc; padding: 10px; text-align: center; }
        th { background-color: #343a40; color: white; }
        tr:nth-child(even) { background-color: #eee; }
        h3 { background-color: #dee2e6; padding: 10px; }
    </style>
</head>
<body>

<?php include "layout/header_admin.html"; ?>

<div class="content">
    <h2>Daftar Pesanan Masuk</h2>

    <?php
    $stmt = $db->prepare("SELECT r.*, d.nama_pembeli, d.no_telepon, d.alamat_pembeli
                          FROM riwayat r
                          LEFT JOIN tb_detail_pembeli d ON r.username = d.username
                          ORDER BY r.waktu DESC");
    $stmt->execute();
    $result = $stmt->get_result();

    $pesanan_grouped = [];

    while ($row = $result->fetch_assoc()) {
        $tanggal = date('Y-m-d', strtotime($row['waktu']));
        $pesanan_grouped[$tanggal][] = $row;
    }

    if (!empty($pesanan_grouped)) {
        foreach ($pesanan_grouped as $tanggal => $pesanan_list) {
            echo "<h3>Tanggal: " . date('d M Y', strtotime($tanggal)) . "</h3>";
            echo "<table>
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Nama Pembeli</th>
                            <th>Username</th>
                            <th>Nomor Telepon</th>
                            <th>Alamat</th>
                            <th>Nama Barang</th>
                            <th>Jumlah</th>
                            <th>Catatan</th>
                            <th>Total Harga</th>
                        </tr>
                    </thead>
                    <tbody>";

            $no = 1;
            foreach ($pesanan_list as $row) {
                echo "<tr>";
                echo "<td>" . $no++ . "</td>";
                echo "<td>" . htmlspecialchars($row['nama_pembeli'] ?? '-') . "</td>";
                echo "<td>" . htmlspecialchars($row['username'] ?? '-') . "</td>";
                echo "<td>" . htmlspecialchars($row['no_telepon'] ?? '-') . "</td>";
                echo "<td>" . htmlspecialchars($row['alamat_pembeli'] ?? '-') . "</td>";
                echo "<td>" . htmlspecialchars($row['nama_barang']) . "</td>";
                echo "<td>" . $row['jumlah'] . "</td>";
                echo "<td>" . htmlspecialchars($row['catatan']) . "</td>";
                echo "<td>Rp " . number_format($row['jumlah'] * ($row['harga'] ?? 0), 0, ',', '.') . "</td>";
                echo "</tr>";
            }

            echo "</tbody></table>";
        }
    } else {
        echo "<p>Belum ada pesanan masuk.</p>";
    }
    ?>
</div>

</body>
</html>
