<?php
session_start();
include("../config.php");

if($_SERVER['REQUEST_METHOD'] === 'POST'){
  $judul = trim($_POST['judul']);
  $deskripsi = trim($_POST['deskripsi']);
  $tanggal = date('Y-m-d H:i:s'); // Biar sesuai tipe DATETIME
  $id_user = $_SESSION['id_user']; // VARCHAR

  // Upload Foto
  $fotoName = '';
  if(isset($_FILES['foto']) && $_FILES['foto']['error'] === UPLOAD_ERR_OK){
    $tmp = $_FILES['foto']['tmp_name'];
    $ext = pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION);

    // Nama file unik agar tidak bentrok
    $fotoName = time() . '_' . bin2hex(random_bytes(5)) . '.' . $ext;

    // Folder penyimpanan (pastikan folder ini SUDAH ADA)
    $folder = __DIR__ . '/../upload/'; // atau ubah ke uploads/berita/

    if(!is_dir($folder)){
        mkdir($folder, 0777, true);
    }

    move_uploaded_file($tmp, $folder . $fotoName);
  }

  // Karena id_user VARCHAR, pakai 'sssss' bukan 'issss'
  $stmt = $koneksi->prepare("INSERT INTO berita (id_user, tanggal, judul, foto, deskripsi) VALUES (?,?,?,?,?)");
  $stmt->bind_param("sssss", $id_user, $tanggal, $judul, $fotoName, $deskripsi);
  $stmt->execute();

  echo "<script>alert('Berita berhasil ditambahkan!'); window.location='list_berita.php';</script>";
  exit;
}
?>