<?php
session_start();
include "service/database.php";

// Cek apakah user sudah login
if (!isset($_SESSION["is_login"])) {
    header("Location: login.php");
    exit();
}

$username = $_SESSION["username"]; // Mengambil username dari session

// Query untuk mengambil data pengguna berdasarkan username
$sql = "SELECT * FROM tb_pembeli WHERE username = ?";
$stmt = $db->prepare($sql);
$stmt->bind_param("s", $username); // "s" untuk string
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

if (!$user) {
    echo "Data pengguna tidak ditemukan.";
    exit();
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Akun Saya</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            padding: 20px;
        }
        .profile {
            max-width: 500px;
            margin: auto;
            border: 1px solid #ccc;
            border-radius: 10px;
            padding: 100px;
            background-color: #f9f9f9;
        }
        .profile h2 {
            text-align: center;
        }
        .profile p {
            font-size: 16px;
            margin: 10px 0;
        }
        .nama{
            text-align: center;
            font-weight: bold;
        }
        .btn-logout {
            display: block;
            text-align: center;
            margin-top: 20px;
        }
        .btn-logout a {
            background-color: #e74c3c;
            color: white;
            padding: 10px 15px;
            text-decoration: none;
            border-radius: 5px;
        }
    </style>
</head>
<body>
<?php include "layout/header.html"?>
<div class="profile">
    <h2>Akun Saya</h2>
    <p>selamat datang di toko bangunan " Baja Hitam " kami</p>
    <p>ini adalah akun kamu dan gunakan aku tersebut dengan bijak</p>
    <p class="nama"><strong></strong> <?= htmlspecialchars($user['username']) ?></p>

    <div class="btn-logout">
        <a href="logout.php">Logout</a>
    </div>
</div>

</body>
</html>
