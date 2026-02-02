<?php
session_start();
require_once("../config.php");

// Pastikan data POST ada
if (!isset($_POST['STRA']) || !isset($_POST['password'])) {
    header("Location:../admin/login.php?error=empty");
    exit();
}

$STRA = trim($_POST['STRA']);
$password = $_POST['password'];

// Validasi input kosong
if ($STRA === '' || $password === '') {
    header("Location:../admin/login.php?error=empty");
    exit();
}

// Gunakan prepared statement dengan benar
$sql = "SELECT * FROM apoteker WHERE STRA = ? LIMIT 1";
$stmt = $db->prepare($sql);

if (!$stmt) {
    // Jika prepare gagal
    header("Location:../admin/login.php?error=system");
    exit();
}

// Bind parameter (SELALU pastikan 1 variabel untuk 1 placeholder)
$stmt->bind_param("s", $STRA);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) {
    // STRA tidak ditemukan
    header("Location:../admin/login.php?error=notfound");
    exit();
}

$user = $result->fetch_assoc();

// Verifikasi password (asumsi masih plain text)
if ($password !== $user['password']) {
    header("Location:../admin/login.php?error=wrongpass");
    exit();
}

// === LOGIN BERHASIL ===
// Simpan session
$_SESSION['STRA'] = $user['STRA'];
$_SESSION['nama'] = $user['nama'] ?? $user['username'] ?? 'User';
$_SESSION['username'] = $user['username'] ?? '';
$_SESSION['level'] = $user['level'] ?? 'user'; // default user jika kolom level belum ada

// Redirect berdasarkan level
if ($_SESSION['level'] === 'STRA') {
    header("Location:../admin/dashboard.php");
} else {
    header("Location: ../admin/dashboard.php");
}
exit();
?>