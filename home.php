<?php
include_once "service/database.php";
session_start();
$query = "SELECT * FROM tb_barang ORDER BY id_barang DESC";
$result = $db->query($query);

?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>BAJA HITAM - Home</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" />
  <style>
    body {
      font-family: Arial, sans-serif;
      margin: 0;
      padding: 0;
      background-color: #f1e7e7;
      color: black;
    }

    .hero {
      display: flex;
      justify-content: center;
      align-items: center;
      height: 60vh;
      background: url('images/toko.jpg') no-repeat center center fixed;
      background-size: cover;
      color: white;
      text-align: center;
      padding: 40px 20px;
    }

    .hero .content {
      max-width: 700px;
    }

    .products {
      padding: 20px;
      background-color: #f9f9f9;
      text-align: center;
    }

    .search-container {
      text-align: center;
      margin: 20px 0;
      padding: 0 15px;
    }

    .search-box {
      padding: 10px;
      width: 70%;
      max-width: 400px;
      border: 1px solid #ccc;
      border-radius: 5px;
      font-size: 16px;
    }

    .product-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
      gap: 20px;
      max-width: 1200px;
      margin: auto;
    }

    .product-item {
      background-color: #fff;
      border: 1px solid #ddd;
      border-radius: 8px;
      padding: 15px;
      transition: transform 0.2s;
    }

    .product-item:hover {
      transform: translateY(-5px);
    }

    .product-item img {
      width: 100%;
      max-height: 200px;
      object-fit: cover;
      border-radius: 4px;
    }

    .btn-pesan {
  display: inline-block;
  padding: 10px 24px;
  background-color: #4CAF50;
  color: white;
  text-decoration: none;
  font-size: 16px;
  border-radius: 6px;
  margin-top: 12px;
  position: relative;
  overflow: hidden;
  z-index: 1;
  transition: background-color 0.3s, transform 0.3s, box-shadow 0.3s;
  box-shadow: 0 0 5px rgba(76, 175, 80, 0.4);
}

.btn-pesan:hover {
  background-color: #45a049;
  transform: translateY(-2px);
  box-shadow: 0 0 12px rgba(76, 175, 80, 0.6), 0 0 18px rgba(76, 175, 80, 0.4);
}

    .btn-pesan:hover {
      background-color: #45a049;
    }

    @media (max-width: 768px) {
      .hero h1 { font-size: 26px; }
      .hero p { font-size: 16px; }
      .search-box { width: 90%; }
      .btn-pesan { font-size: 14px; padding: 8px 16px; }
    }
    .btn-keranjang {
  display: inline-block;
  padding: 10px 16px;
  background-color: #ffffff;
  border: 1px solid #ccc;
  border-radius: 8px;
  color: #555;
  font-size: 18px;
  text-decoration: none;
  transition: all 0.3s ease;
  box-shadow: 0 0 4px rgba(0,0,0,0.1);
  position: relative;
  overflow: hidden;
  margin-top: 10px;
}

.btn-keranjang i {
  color: #555;
}

.btn-keranjang:hover {
  box-shadow: 0 0 10px #aaa, 0 0 20px #ccc, 0 0 30px #eee;
  transform: translateY(-2px);
}

.btn-keranjang::after {
  content: "";
  position: absolute;
  top: -50%;
  left: -50%;
  width: 200%;
  height: 200%;
  background: radial-gradient(circle, rgba(255,255,255,0.3) 10%, transparent 60%);
  animation: glitter 2s infinite linear;
  z-index: 0;
}

@keyframes glitter {
  0% {
    transform: rotate(0deg) translateX(0px);
  }
  100% {
    transform: rotate(360deg) translateX(0px);
  }
}

  </style>
</head>
<body>
  <?php include_once "layout/header.html"; ?>

  <section class="hero">
    <div class="content">
      <h1>Selamat Datang di Baja Hitam</h1>
      <p>Temukan berbagai kebutuhan bangunan terbaik dengan harga bersaing.</p>
    </div>
  </section>

  <!-- <div class="search-container">
    <input type="text" id="searchInput" class="search-box" placeholder="Cari produk..." onkeyup="searchProducts()">
  </div> -->

  <section class="products">
    <div class="product-grid" id="productGrid">
      <?php
      $query = "SELECT * FROM tb_barang";
      $result = $db->query($query);

      if ($result && $result->num_rows > 0):
        while ($row = $result->fetch_assoc()):
      ?>
        <div class="product-item" data-name="<?= strtolower($row['nama_barang']) ?>">
  <a href="detail_produk.php?id_barang=<?= $row['id_barang'] ?>">
    <img src="images/<?= htmlspecialchars($row['gambar']) ?>" alt="<?= htmlspecialchars($row['nama_barang']) ?>">
  </a>
          <h3><?= htmlspecialchars($row['nama_barang']) ?></h3>
          <p>Rp <?= number_format($row['harga'], 0, ',', '.') ?></p>
          <a href="pesan.php?id_barang=<?= $row['id_barang'] ?>" class="btn-pesan">Pesan</a><br>
          <a href="keranjang.php?tambah=<?= $row['id_barang'] ?>" class="btn-keranjang"><i class="fas fa-shopping-cart"></i></a>

        </div>
      <?php
        endwhile;
      else:
        echo "<p>Tidak ada produk ditemukan.</p>";
      endif;
      ?>
    </div>
  </section>

  <script>
    function searchProducts() {
      const input = document.getElementById("searchInput").value.toLowerCase();
      const products = document.querySelectorAll(".product-item");

      products.forEach((product) => {
        const name = product.getAttribute("data-name");
        product.style.display = name.includes(input) ? "block" : "none";
      });
    }
  </script>
</body>
</html>
