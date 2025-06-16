<?php
session_start();
$register_message = "";
include "service/database.php";

// Jika sudah login, langsung arahkan ke halaman sesuai role
if (isset($_SESSION["is_login"])) {
    if ($_SESSION["role"] == "penjual") {
        header("Location: homep.php");
    }  else {
        header("Location: home.php");
    }
    exit;
}

// Proses registrasi
if (isset($_POST['register'])) {
    $username = $_POST["username"];
    $password_input = $_POST["password"];

    $nama_pembeli = $_POST["nama_pembeli"] ?? "";
    $alamat_pembeli = $_POST["alamat_pembeli"] ?? "";
    $no_telepon = $_POST["no_telepon"] ?? "";
    $email = $_POST["email"] ?? "";

    $kode_penjual = "penjual123";
    
    $role = "pembeli"; // default
    $kode_penjual_value = null;

    // Tentukan role berdasarkan akhiran password
    if (str_ends_with($password_input, $kode_penjual)) {
        $role = "penjual";
        $password_asli = substr($password_input, 0, -strlen($kode_penjual));
        $kode_penjual_value = uniqid("PJ-");
    } else {
        $password_asli = $password_input;
    }

    $password_hashed = password_hash($password_asli, PASSWORD_DEFAULT);

    // Cek apakah username sudah ada
    $stmt = $db->prepare("SELECT * FROM tb_pembeli WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $register_message = "Username sudah terdaftar.";
    } else {
    // Validasi tambahan untuk pembeli
    if ($role === "pembeli") {
        if (empty($nama_pembeli) || empty($alamat_pembeli) || empty($no_telepon) || empty($email)) {
            $register_message = "Semua data pembeli wajib diisi.";
        }
    }

    // Hanya lanjut jika tidak ada pesan error
    if (empty($register_message)) {
        // Simpan ke tb_pembeli
        if ($role === "penjual") {
            $stmt = $db->prepare("INSERT INTO tb_pembeli (username, password, role, kode_penjual) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("ssss", $username, $password_hashed, $role, $kode_penjual_value);
        } else {
            $stmt = $db->prepare("INSERT INTO tb_pembeli (username, password, role) VALUES (?, ?, ?)");
            $stmt->bind_param("sss", $username, $password_hashed, $role);
        }

        if ($stmt->execute()) {
            $id_pembeli = $stmt->insert_id;

            if ($role === "pembeli") {
                $stmt_detail = $db->prepare("INSERT INTO tb_detail_pembeli (id_pembeli, nama_pembeli, alamat_pembeli, no_telepon, email) VALUES (?, ?, ?, ?, ?)");
                $stmt_detail->bind_param("issss", $id_pembeli, $nama_pembeli, $alamat_pembeli, $no_telepon, $email);
                $stmt_detail->execute();
            }

            // Set sesi login
            $_SESSION["is_login"] = true;
            $_SESSION["username"] = $username;
            $_SESSION["role"] = $role;

            header("Location: " . ($role === "penjual" ? "homep.php" : "home.php"));
            exit;
        } else {
            $register_message = "Gagal menyimpan ke database.";
        }
    }
}

}
?>


<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrasi</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #eef2f3;
            margin: 0;
            padding: 0;
            height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
        }
        header {
            width: 100%;
            background-color: #333;
            color: white;
            text-align: center;
            padding: 15px 0;
            font-size: 15px;
        }
        form {
            background-color: #fff;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 500px;
            margin-top: 40px;
        }
        h3 {
            text-align: center;
            font-size: 26px;
            margin-bottom: 20px;
            font-weight: bold;
            color: #333;
        }
        i {
            display: block;
            text-align: center;
            color: red;
            margin-bottom: 10px;
            font-size: 14px;
        }
        label {
            font-weight: bold;
            font-size: 14px;
            display: block;
            margin-bottom: 6px;
        }
        input[type="text"], input[type="password"], input[type="email"] {
            width: 100%;
            padding: 12px;
            border-radius: 6px;
            border: 1px solid #ccc;
            margin-bottom: 18px;
            font-size: 16px;
            box-sizing: border-box;
            transition: border 0.3s;
        }
        input[type="text"]:focus, input[type="password"]:focus, input[type="email"]:focus {
            border-color: #4CAF50;
            outline: none;
        }
        button {
            width: 100%;
            padding: 14px;
            background-color: #4CAF50;
            color: white;
            border: none;
            border-radius: 6px;
            font-size: 16px;
            cursor: pointer;
            transition: background-color 0.3s;
        }
        button:hover {
            background-color: #45a049;
        }
        p {
            text-align: center;
            margin-top: 10px;
        }
    </style>
</head>

<body>
    <?php include "layout/header.html"; ?>
    <form action="registrasi.php" method="POST">
        <h3>DAFTAR AKUN</h3>
        <i><?= $register_message ?></i>

        <label for="username">Username:</label>
        <input type="text" id="username" name="username" placeholder="Masukkan username" required />

        <label for="password">Password:</label>
        <input type="password" id="password" name="password" placeholder="Masukkan password" required />

        <div class="form-group">
    <label for="nama_pembeli">Nama Lengkap:</label>
    <input type="text" id="nama_pembeli" name="nama_pembeli" placeholder="Masukkan nama lengkap" />
</div>

<div class="form-group">
    <label for="alamat_pembeli">Alamat:</label>
    <input type="text" id="alamat_pembeli" name="alamat_pembeli" placeholder="Masukkan alamat" />
</div>

<div class="form-group">
    <label for="no_telepon">No Telepon:</label>
    <input type="text" id="no_telepon" name="no_telepon" placeholder="Masukkan no telepon" />
</div>

<div class="form-group">
    <label for="email">Email:</label>
    <input type="text" id="email" name="email" placeholder="Masukkan email" />
</div>


        <button type="submit" name="register">Daftar</button>
    </form>
    <script>
    const passwordInput = document.getElementById("password");
    const detailFields = [
        document.getElementById("nama_pembeli"),
        document.getElementById("alamat_pembeli"),
        document.getElementById("no_telepon"),
        document.getElementById("email")
    ];

    function checkRole() {
        const isPenjual = passwordInput.value.endsWith("penjual123");

        detailFields.forEach(field => {
            field.disabled = isPenjual;
            field.parentElement.style.display = isPenjual ? "none" : "block";
        });
    }

    passwordInput.addEventListener("input", checkRole);

    // ✅ Jalankan saat halaman dimuat
    window.addEventListener("DOMContentLoaded", checkRole);
</script>


</body>
</html>
