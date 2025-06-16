<?php
session_start();
include_once "service/database.php";

if (!isset($_SESSION['is_login']) || $_SESSION['role'] != 'penjual') {
    header("Location: login.php");
    exit();
}

$username = $_GET['user'] ?? '';

if (empty($username)) {
    echo "Username tidak valid.";
    exit;
}

// Update status kirim jika ada POST
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['status_kirim'])) {
    foreach ($_POST['status_kirim'] as $id_riwayat) {
        $update = $db->prepare("UPDATE riwayat SET status_kirim = 1 WHERE id = ?");
        $update->bind_param("i", $id_riwayat);
        $update->execute();
        $update->close();
    }
}

// Ambil semua pesanan user
$stmt = $db->prepare("SELECT * FROM riwayat WHERE username = ? ORDER BY DATE(waktu) DESC, waktu DESC");
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();

// Kelompokkan berdasarkan tanggal
$grouped = [];
while ($row = $result->fetch_assoc()) {
    $tanggal = date("Y-m-d", strtotime($row['waktu']));
    $grouped[$tanggal][] = $row;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8" />
    <title>Detail Pesanan - <?= htmlspecialchars($username) ?></title>
    <style>
        /* style sama seperti sebelumnya... dipersingkat */
        table { width: 100%; border-collapse: collapse; margin-top: 10px; background: white; }
        th, td { border: 1px solid #ccc; padding: 10px; text-align: center; }
        th { background-color: #343a40; color: white; }
    </style>
</head>
<body>

<div class="container content">
    <h2>Detail Pesanan: <?= htmlspecialchars($username) ?></h2>

    <form method="POST">
    <?php
    if (!empty($grouped)) {
        $no = 1;
        $totalKeseluruhan = 0;

        foreach ($grouped as $tanggal => $pesananPerTanggal) {
            echo "<h3>Tanggal: " . date("d-m-Y", strtotime($tanggal)) . "</h3>";
            echo "<table><tr>
                <th>No</th><th>Nama Barang</th><th>Jumlah</th><th>Harga Satuan</th><th>Total</th>
                <th>Catatan</th><th>Status Kirim</th><th>Tanggal</th>
            </tr>";

            foreach ($pesananPerTanggal as $row) {
                $total = $row['jumlah'] * $row['harga'];
                $totalKeseluruhan += $total;
                $checked = $row['status_kirim'] ? 'checked disabled' : '';
                echo "<tr>";
                echo "<td>" . $no++ . "</td>";
                echo "<td>" . htmlspecialchars($row['nama_barang']) . "</td>";
                echo "<td>" . $row['jumlah'] . "</td>";
                echo "<td>Rp " . number_format($row['harga'], 0, ',', '.') . "</td>";
                echo "<td>Rp " . number_format($total, 0, ',', '.') . "</td>";
                echo "<td>" . htmlspecialchars($row['catatan']) . "</td>";
                echo "<td><input type='checkbox' name='status_kirim[]' value='" . $row['id'] . "' $checked></td>";
                echo "<td>" . $row['waktu'] . "</td>";
                echo "</tr>";
            }

            echo "</table><br>";
        }

        echo "<p><strong>Total Keseluruhan: Rp " . number_format($totalKeseluruhan, 0, ',', '.') . "</strong></p>";
        echo "<button type='submit'>Perbarui Status Kirim</button>";
    } else {
        echo "<p>Tidak ada pesanan untuk pengguna ini.</p>";
    }
    ?>
    </form>

    <a href="laporan_admin.php" class="back-link">&larr; Kembali ke Laporan</a>
</div>

</body>
</html>
