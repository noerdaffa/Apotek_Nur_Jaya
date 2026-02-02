<?php
include("../config.php");
// cek apakah tombol daftar sudah diklik atau blum? 
if (isset($_POST['daftarU'])) {

    // ambil data dari formulir 
    $username = $_POST['username'];
    $password = $_POST['password'];
    $telpon = $_POST['no_telp'];
    $alamat = $_POST['alamat'];

    // buat query 
    $sql = "INSERT INTO user (username , password, no_telp, alamat) VALUES ('$username', '$password', '$telpon', '$alamat')";
    $query = mysqli_query($db, $sql);

    // apakah query simpan berhasil? 
    if ($query) {
        // kalau berhasil alihkan ke halaman index.php dengan status=sukses \
        header('Location: ../index.php?statusU=suksesU');
    } else {
        // kalau gagal alihkan ke halaman indek.php dengan status=gagal 
        header('Location: ../index.php?statusU=gagalU');
    }
}