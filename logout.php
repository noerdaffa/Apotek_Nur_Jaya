<?php
session_start();

// CEK APAKAH USER ATAU ADMIN
if (isset($_SESSION['username'])) {
    // USER
    $redirect = "index.php";
} elseif (isset($_SESSION['STRA'])) {
    // ADMIN
    $redirect = "index.php";
} else {
    // TIDAK ADA SESSION → KEMBALI KE HOME
    $redirect = "index.php";
}

// HAPUS SEMUA SESSION
session_unset();
session_destroy();

// TAMBAHKAN LOG (opsional, untuk keamanan)
error_log("[LOGOUT] " . ($_SESSION['username'] ?? $_SESSION['STRA'] ??  " - " . date('Y-m-d H:i:s')));

// REDIRECT KE HALAMAN LOGIN SESUAI
header("Location: $redirect");
exit();
?>