<?php 
include("../config.php"); 
// cek apakah tombol daftar sudah diklik atau blum? 
if(isset($_POST['daftarA'])){ 
 
    // ambil data dari formulir 
    $STRA = $_POST['STRA']; 
    $nama_lengkap = $_POST['nama_lengkap'];
    $password = $_POST['password'];
    $alamat = $_POST['alamat']; 
    $no_telp = $_POST['no_telp']; 
 
    // buat query 
    $sql = "INSERT INTO apoteker (STRA, nama_lengkap, password, alamat, no_telp) VALUE ('$STRA', '$nama_lengkap', '$password', '$alamat', '$no_telp')"; 
    $query = mysqli_query($db, $sql); 
 
    // apakah query simpan berhasil? 
    if( $query ) { 
        // kalau berhasil alihkan ke halaman index.php dengan status=sukses 
        header('Location: ../index.php?statusA=suksesA'); 
    } else { 
        // kalau gagal alihkan ke halaman indek.php dengan status=gagal
        header('Location: ../index.php?statusA=gagalA'); 
    } 
 } else {
    die("Akses dilarang...");
 }