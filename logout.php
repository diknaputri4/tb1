<?php
// File logout.php
session_start();

// Proses logout
session_unset();   // Menghapus semua data sesi
session_destroy(); // Menghancurkan sesi

// Redirect ke halaman home
header('Location: home.php');
exit;
?>
