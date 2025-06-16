<?php
include "service/database.php"; // Pastikan koneksi database tersedia

// Query untuk mengambil semua data dari tabel user
$sql = "SELECT * FROM tb_pembeli";
$result = $db->query($sql);

?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>BAJA HITAM</title>

    <!-- font awesome cdn link -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet" />

    <!-- cutom css file link -->
    <link rel="stylesheet" href="css/style.css" />
  </head>
  <body>
    <!-- heder section starts -->

    <?php include "layout/header.html"?>
    <!-- headr section end -->

    <!-- custom js file link -->
    <script src="js/script.js"></script>
  </body>
</html>
