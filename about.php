<?php
// About Page
include_once "service/database.php";
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>BAJA HITAM - About</title>

  <!-- Font Awesome CDN Link -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" />

  <!-- Custom CSS -->
  <style>
    body {
      font-family: Arial, sans-serif;
      margin: 0;
      padding: 0;
      background-color: rgb(241, 231, 231);
      color: black;
    }
    .about-section {
      text-align: center;
      padding: 80px 20px;
      background-image: url('images/background.jpg');
      background-size: cover;
      background-position: center;
      color: black;
    }
    .about-section h1 {
      font-size: 32px;
      font-weight: bold;
      text-transform: uppercase;
    }
    .about-section p {
      font-size: 18px;
      max-width: 800px;
      margin: 20px auto;
      line-height: 1.6;
    }
    .about-content {
      max-width: 1000px;
      margin: 40px auto;
      padding: 20px;
      background: white;
      border-radius: 10px;
      box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
    }
  </style>
</head>
<body>
  <?php include_once "layout/header.html"; ?>

  <!-- About Section -->
  <section class="about-section">
    <h1>Tentang Kami</h1>
    <p>
      Baja Hitam adalah tempat terbaik untuk menemukan material berkualitas tinggi dengan harga terjangkau.
      Kami menawarkan berbagai produk berkualitas seperti semen, seng, pintu, dan lainnya.
    </p>
  </section>

  <div class="about-content">
    <h2>Visi & Misi</h2>
    <p>
      Kami berkomitmen untuk memberikan pelayanan terbaik dan menyediakan produk dengan kualitas terbaik
      demi kepuasan pelanggan.
    </p>
    <h2>Kontak Kami</h2>
    <p>
      Alamat: Jl. Baja Sejahtera No. 10, Kota Industri<br>
      Telepon: 0812-3456-7890<br>
      Email: info@bajahitam.com
    </p>
  </div>

  <!-- Custom JS File Link -->
  <script src="js/script.js"></script>
</body>
</html>
