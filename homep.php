<?php
include_once "service/database.php";
session_start();

// Pastikan admin sudah login
if (!isset($_SESSION["is_login"]) || $_SESSION["role"] !== 'penjual') {
    header("Location: login.php");
    exit();
}

// Query untuk ambil jumlah produk
$resProduk = $db->query("SELECT COUNT(*) AS total_produk FROM tb_barang");
$jumlahProduk = $resProduk->fetch_assoc()['total_produk'] ?? 0;

// Query untuk ambil jumlah pesanan baru (misal status = 'baru')
$resPesananBaru = $db->query("SELECT COUNT(*) AS total_pesanan_baru FROM riwayat ");
$jumlahPesananBaru = $resPesananBaru->fetch_assoc()['total_pesanan_baru'] ?? 0;

// Query untuk hitung omset bulan ini (jumlah total transaksi pada bulan sekarang)
$bulanIni = date('Y-m');
$resOmset = $db->query("SELECT SUM(total) AS omset_bulan FROM riwayat WHERE DATE_FORMAT(waktu, '%Y-%m') = '$bulanIni'");
$omsetBulanIni = $resOmset->fetch_assoc()['omset_bulan'] ?? 0;

// Query pelanggan aktif (misal yang pernah transaksi unik)
$resPelanggan = $db->query("SELECT COUNT(DISTINCT username) AS pelanggan_aktif FROM riwayat");
$pelangganAktif = $resPelanggan->fetch_assoc()['pelanggan_aktif'] ?? 0;

// Data pendapatan per bulan (6 bulan terakhir)
$resPendapatan = $db->query("
    SELECT DATE_FORMAT(waktu, '%Y-%m') AS bulan, SUM(total) AS total_pendapatan
    FROM riwayat
    WHERE waktu >= DATE_SUB(CURDATE(), INTERVAL 6 MONTH)
    GROUP BY bulan
    ORDER BY bulan ASC
");

// Data distribusi produk (jumlah produk per kategori atau nama barang)
$resDistribusiProduk = $db->query("
    SELECT nama_barang, SUM(jumlah) AS total_terjual
    FROM riwayat
    GROUP BY nama_barang
    ORDER BY total_terjual DESC
    LIMIT 5
");

// Siapkan array untuk grafik
$labelsPendapatan = [];
$dataPendapatan = [];
while ($row = $resPendapatan->fetch_assoc()) {
    $labelsPendapatan[] = $row['bulan'];
    $dataPendapatan[] = (int)$row['total_pendapatan'];
}

$labelsProduk = [];
$dataProduk = [];
while ($row = $resDistribusiProduk->fetch_assoc()) {
    $labelsProduk[] = $row['nama_barang'];
    $dataProduk[] = (int)$row['total_terjual'];
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Dashboard Toko Bangunan</title>
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <style>
    body {
      margin: 0;
      font-family: Arial, sans-serif;
      background-color: #f8f9fa;
    }
    .navbar {
      background-color: rgb(123, 128, 129);
      color: white;
      padding: 15px 20px;
      font-size: 20px;
      margin-bottom: 20px;
      border-radius: 8px;
    }
    .cards {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
      gap: 20px;
      margin: 20px 20px 40px;
    }
    .card {
      background-color: white;
      padding: 20px;
      border-radius: 12px;
      box-shadow: 0 2px 8px rgba(0,0,0,0.1);
      text-align: center;
      transition: transform 0.2s;
    }
    .card:hover {
      transform: scale(1.05);
    }
    .card h3 {
      margin: 10px 0;
      color: #343a40;
    }
    .card p {
      font-size: 24px;
      font-weight: bold;
      margin: 0;
      color: rgb(153, 165, 167);
    }
    .chart-container {
      background: white;
      padding: 20px;
      border-radius: 12px;
      box-shadow: 0 2px 8px rgba(0,0,0,0.1);
      margin: 20px;
    }
  </style>
</head>
<body>

<?php include_once "layout/header_admin.html"; ?>

<div class="content">
  <div class="navbar">
    Dashboard Baja Hitam
  </div>

  <h1 style="text-align:center;">Selamat Datang, Admin Toko!</h1>
  <p style="text-align:center;">Berikut adalah ringkasan aktivitas tokomu:</p>

  <div class="cards">
    <div class="card">
      <h3>Jumlah Produk</h3>
      <p><?= number_format($jumlahProduk) ?></p>
    </div>
    <div class="card">
      <h3>Pesanan Baru</h3>
      <p><?= number_format($jumlahPesananBaru) ?></p>
    </div>
    <div class="card">
      <h3>Omset Bulan Ini</h3>
      <p>Rp <?= number_format($omsetBulanIni, 0, ',', '.') ?></p>
    </div>
    <div class="card">
      <h3>Pelanggan Aktif</h3>
      <p><?= number_format($pelangganAktif) ?></p>
    </div>
  </div>

  <div class="chart-container">
    <h2>Pendapatan per Bulan</h2>
    <canvas id="incomeChart" height="100"></canvas>
  </div>

  <div class="chart-container">
    <h2>Distribusi Produk Terlaris</h2>
    <canvas id="productChart" height="100"></canvas>
  </div>
</div>

<script>
  const ctxIncome = document.getElementById('incomeChart').getContext('2d');
  const incomeChart = new Chart(ctxIncome, {
    type: 'line',
    data: {
      labels: <?= json_encode($labelsPendapatan) ?>,
      datasets: [{
        label: 'Pendapatan',
        data: <?= json_encode($dataPendapatan) ?>,
        backgroundColor: 'rgba(54, 162, 235, 0.2)',
        borderColor: 'rgba(54, 162, 235, 1)',
        borderWidth: 2,
        fill: true,
        tension: 0.3
      }]
    },
    options: {
      scales: {
        y: {
          beginAtZero: true,
          ticks: {
            callback: function(value) {
              return 'Rp ' + value.toLocaleString('id-ID');
            }
          }
        }
      }
    }
  });

  const ctxProduct = document.getElementById('productChart').getContext('2d');
  const productChart = new Chart(ctxProduct, {
    type: 'bar',
    data: {
      labels: <?= json_encode($labelsProduk) ?>,
      datasets: [{
        label: 'Jumlah Terjual',
        data: <?= json_encode($dataProduk) ?>,
        backgroundColor: 'rgba(255, 159, 64, 0.7)'
      }]
    },
    options: {
      indexAxis: 'y',
      scales: {
        x: {
          beginAtZero: true
        }
      }
    }
  });
</script>

</body>
</html>
