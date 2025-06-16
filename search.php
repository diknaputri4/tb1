<?php
include "service/database.php";
session_start();
if (isset($_SESSION["is_menu"])) {
    header("location: menu.php");
}

$keyword = $_GET['keyword'] ?? '';

$sql = "SELECT * FROM tb_barang WHERE nama_barang LIKE '%$keyword%'";
$result = $db->query($sql);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Hasil Pencarian</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            padding: 20px;
        }

        .item {
            display: flex;
            flex-wrap: wrap;
            border: 1px solid #ddd;
            margin-bottom: 20px;
            border-radius: 8px;
            background: #f8f8f8;
            padding: 15px;
        }

        .kiri {
            width: 40%;
            padding: 10px;
        }

        .kiri h3 {
            text-align: center;
        }

        .kiri img {
            display: block;
            margin: 10px auto;
            max-width: 100%;
            height: auto;
            border-radius: 8px;
        }

        .kanan {
            width: 60%;
            padding: 10px;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .kanan p {
            margin: 10px 0;
        }

        .kanan .harga {
            font-weight: bold;
            color:rgb(12, 12, 12);
        }

        .btn-group {
            margin-top: 15px;
            display: flex;
            gap: 10px;
        }

        .btn {
            padding: 10px 15px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-weight: bold;
        }

        .btn.pesan {
            background-color: #45a049;
            color: black;
        }

        .btn.keranjang {
            background-color:rgb(242, 245, 248);
            color: white;
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .fa-shopping-cart {
        font-size: 18px;
        margin-right: 5px;
        color: black;
}


        @media (max-width: 768px) {
            .item {
                flex-direction: column;
            }

            .kiri, .kanan {
                width: 100%;
            }

            .btn-group {
                flex-direction: column;
            }

            .btn {
                width: 100%;
            }
        }
    </style>
</head>
<body>
<?php include_once "layout/header.html"; ?>
    <h2>Hasil pencarian untuk: <?= htmlspecialchars($keyword) ?></h2>

    <?php if ($result && $result->num_rows > 0): ?>
        <?php while ($row = $result->fetch_assoc()): ?>
            <div class="item">
                <div class="kiri">
                    <h3><?= htmlspecialchars($row['nama_barang']) ?></h3>
                    <img src="images/<?= htmlspecialchars($row['gambar']) ?>" alt="<?= htmlspecialchars($row['nama_barang']) ?>">
                </div>
                <div class="kanan">
                    <p><?= nl2br(htmlspecialchars($row['deskripsi'])) ?></p>
                    <p class="harga">Rp <?= number_format($row['harga'], 0, ',', '.') ?></p>
                    <div class="btn-group">
                        <form action="pesan.php" method="get" >
                            <input type="hidden" name="id_barang" value="<?= $row['id_barang'] ?>">
                            <button class="btn pesan" type="submit">Pesan</button>
                        </form>
                <form action="keranjang.php" method="post">
                    <input type="hidden" name="id_barang" value="<?= $row['id_barang'] ?>">
                    <button class="btn keranjang" type="submit">
                        <i class="fas fa-shopping-cart"></i> Tambah ke Keranjang
                    </button>
                </form>

                    </div>
                </div>
            </div>
        <?php endwhile; ?>
    <?php else: ?>
        <p>Tidak ada hasil ditemukan.</p>
    <?php endif; ?>
    
</body>
</html>
