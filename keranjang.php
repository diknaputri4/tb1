<?php
session_start();
include_once "service/database.php";
if (!isset($_SESSION["is_login"])) {
    header("Location: login.php");
    exit();
}

// Tambah ke keranjang
if (isset($_GET['tambah'])) {
    $id = $_GET['tambah'];
    $query = "SELECT * FROM tb_barang WHERE id_barang = '$id'";
    $result = $db->query($query);
    $barang = $result->fetch_assoc();
    if ($barang) {
        if (!isset($_SESSION['keranjang'])) $_SESSION['keranjang'] = [];

        if (isset($_SESSION['keranjang'][$id])) {
            $_SESSION['keranjang'][$id]['jumlah'] += 1;
        } else {
            $_SESSION['keranjang'][$id] = [
                'id' => $barang['id_barang'],
                'nama' => $barang['nama_barang'],
                'harga' => $barang['harga'],
                'jumlah' => 1
            ];
        }

        header("Location: keranjang.php");
        exit;
    }
    
}

// Hapus dari keranjang
if (isset($_GET['hapus'])) {
    unset($_SESSION['keranjang'][$_GET['hapus']]);
    header("Location: keranjang.php");
    exit;
}

// Perbarui jumlah
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update'])) {
    foreach ($_POST['jumlah'] as $id => $jumlah) {
        if (isset($_SESSION['keranjang'][$id])) {
            $_SESSION['keranjang'][$id]['jumlah'] = max(1, (int)$jumlah);
        }
    }
    header("Location: keranjang.php");
    exit;
}

/// Proses checkout
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['checkout'])) {
    $checkout_ids = $_POST['checkout_ids'] ?? [];
    $pesanan = [];
    $total_harga = 0; // Tambahkan variabel total harga

    foreach ($_POST['jumlah'] as $id => $jumlah) {
    if (isset($_SESSION['keranjang'][$id])) {
        $_SESSION['keranjang'][$id]['jumlah'] = max(1, (int)$jumlah);
    }
}

    foreach ($checkout_ids as $id) {
        if (isset($_SESSION['keranjang'][$id])) {
            $item = $_SESSION['keranjang'][$id];

            // Hitung subtotal per item
            $subtotal = $item['harga'] * $item['jumlah'];
            $total_harga += $subtotal; // Tambahkan ke total harga

            $pesanan[] = [
                'id_barang' => $item['id'],
                'nama_barang' => $item['nama'],
                'harga' => $item['harga'],
                'jumlah' => $item['jumlah'],
                'catatan' => ''
            ];

            // Simpan ke tabel riwayat
            $username = $_SESSION['username']; // ambil username dari session
            $catatan = ''; // jika tidak ada input dari form
            $total = $item['harga'] * $item['jumlah'];
            $stmt = $db->prepare("INSERT INTO riwayat (username, id_barang, nama_barang, jumlah, harga, total, catatan, waktu) VALUES (?, ?, ?, ?, ?, ?, ?, NOW())");
            $stmt->bind_param("sisiiis", $username, $item['id'], $item['nama'], $item['jumlah'], $item['harga'], $total, $catatan);
            $stmt->execute();


            // Opsional: hapus dari keranjang setelah checkout
            unset($_SESSION['keranjang'][$id]);
        }
    }

    $_SESSION['pesanan_terakhir'] = $pesanan;
    $_SESSION['total_harga_terakhir'] = $total_harga; // Simpan total harga ke session

    header("Location: detailpesanan.php");
    exit;
}


?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Keranjang Belanja</title>
  <style>
    body { font-family: Arial, sans-serif; padding: 20px; }
    h1 { text-align: center; }
    table { width: 100%; border-collapse: collapse; margin-top: 20px; }
    th, td { padding: 10px; text-align: center; border: 1px solid #ccc; }
    th { background-color: #f9f9f9; }
    input[type=number] { width: 60px; padding: 5px; }
    .btn { padding: 8px 14px; border: none; border-radius: 5px; cursor: pointer; }
    .btn-hapus { background-color: red; color: white; }
    .btn-update { background-color: orange; color: white; }
    .btn-checkout { background-color: green; color: white; }
    .btns { margin-top: 20px; text-align: right; }
  </style>
</head>
<body>
<?php include_once "layout/header.html"; ?>
<h1>Keranjang Belanja</h1>

<?php if (!empty($_SESSION['keranjang'])): ?>
  <form method="post" action="keranjang.php">
    <table>
      <tr>
        <th>Pilih</th>
        <th>No</th>
        <th>Nama Produk</th>
        <th>Harga</th>
        <th>Jumlah</th>
        <th>Aksi</th>
      </tr>
      <?php
      $no = 1;
      foreach ($_SESSION['keranjang'] as $id => $item):
      ?>
      <tr>
        <td><input type="checkbox" name="checkout_ids[]" value="<?= $id ?>"></td>
        <td><?= $no++ ?></td>
        <td><?= htmlspecialchars($item['nama']) ?></td>
        <td>Rp <?= number_format($item['harga'], 0, ',', '.') ?></td>
        <td><input type="number" name="jumlah[<?= $id ?>]" min="1" value="<?= $item['jumlah'] ?>"></td>
        <td><a href="keranjang.php?hapus=<?= $id ?>" class="btn btn-hapus">Hapus</a></td>
      </tr>
      <?php endforeach; ?>
    </table>

    <div class="btns">
      <button type="submit" name="update" class="btn btn-update">Update Keranjang</button>
      <button type="submit" name="checkout" class="btn btn-checkout">Pesan</button>
    </div>
  </form>
<?php else: ?>
  <p style="text-align:center;">Keranjang kosong.</p>
<?php endif; ?>

</body>
</html>
