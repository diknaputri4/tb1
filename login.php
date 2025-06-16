<?php
include "service/database.php";
session_start();

$login_message = "";

if (isset($_SESSION["is_login"])) {
    if ($SESSION["role"] == "penjual"){
    header("location: homep.php");
    } else {
        header("location: home.php");
    }
    exit;
}
if (isset($_POST['login'])) {
    $username = $_POST["username"];
    $password = $_POST["password"];

    // Gunakan prepared statement untuk keamanan
    $stmt = $db->prepare("SELECT * FROM tb_pembeli WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    // Jika username ditemukan
    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();

        // Verifikasi password
        if (password_verify($password, $user["password"])) {
            $_SESSION["is_login"] = true;
            $_SESSION["username"] = $user["username"];
            $_SESSION["role"] = $user["role"];
            $_SESSION["kode_penjual"] = $user["kode_penjual"] ?? null;

            $_SESSION["id"] = $user["id"];
            
            if ($user["role"] === "penjual") {
                header("Location: homep.php");
            } else {
                header("Location: home.php");
            }
            exit;
        } else {
            $login_message = "Password salah!";
        }
    } else {
        $login_message = "Username tidak ditemukan.";
    }
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="css/style.css" />
    <link rel="stylesheet" href="folder/css/style.css" />
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            padding: 20px;
            padding: 90px;
        }

        .login {
            width: 300px;
            margin: 50px auto;
            padding: 20px;
            border: 1px solid #ccc;
            border-radius: 10px;
            background-color: #fff;
        }

        .login h3 {
            text-align: center;
            margin-bottom: 20px;
        }

        .login input[type="text"],
        .login input[type="password"] {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border-radius: 5px;
            border: 1px solid #ccc;
        }

        .login button {
            width: 100%;
            padding: 10px;
            border: none;
            border-radius: 5px;
            background-color: #4CAF50;
            color: white;
            font-size: 16px;
        }

        .login button:hover {
            background-color: #45a049;
        }

        .login i {
            display: block;
            text-align: center;
            margin-bottom: 10px;
            color: red;
        }

        .login p {
            text-align: center;
            margin-top: 10px;
        }

        .login a {
            color: blue;
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <?php include "layout/header.html"; ?> 

    <form class="login" action="login.php" method="POST">
        <h3>MASUK AKUN</h3>
        <i><?= $login_message ?></i>
        <table>
            <tr>
                <td colspan="2"><label for="username">Username:</label></td>
            </tr>
            <tr>
                <td colspan="2"><input type="text" id="username" name="username" placeholder="Masukkan username" required></td>
            </tr>
            <tr>
                <td colspan="2"><label for="password">Password:</label></td>
            </tr>
            <tr>
                <td colspan="2"><input type="password" id="password" name="password" placeholder="Masukkan password" required></td>
            </tr>
            <tr>
                <td colspan="2" style="text-align: center;">
                    <button type="submit" name="login">Masuk</button>
                </td>
            </tr>
        </table>
        <!-- Tambahan pesan untuk registrasi -->
        <p style="text-align: center; margin-top: 10px;">
            Belum punya akun? <a href="registrasi.php" style="color: blue; text-decoration: underline;">Registrasi di sini</a>.
        </p>
    </form>

    <!-- <?php include "layout/footer.html"; ?> -->
</body>
</html>
