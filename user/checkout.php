<?php
session_start();
include("../config.php");

if (!isset($_POST['checkout'])) {
    header("Location: ../user/cart.php");
    exit();
}

$username = $_SESSION['username'];
$cart = json_decode($_POST['cart_data'], true);
$alamat = $db->real_escape_string($_POST['alamat']);
$catatan = $db->real_escape_string($_POST['catatan'] ?? '');

// Validasi cart tidak kosong
if (empty($cart)) {
    echo "<script>alert('Keranjang kosong!'); location.href='../user/beranda.php';</script>";
    exit();
}

// Ambil user_id
$user = $db->query("SELECT id FROM user WHERE username = '$username'")->fetch_assoc();
if (!$user) {
    echo "<script>alert('User tidak ditemukan!'); location.href='../user/beranda.php';</script>";
    exit();
}
$user_id = $user['id'];

// VALIDASI SEMUA PRODUK ADA DI DATABASE + CEK STOK
$valid_cart = [];
$total = 0;

foreach ($cart as $item) {
    $pid = (int)$item['id'];
    $qty = (int)$item['qty'];
    $harga = (float)$item['harga'];

    if ($qty <= 0) continue;

    // Cek produk ada
    $produk = $db->query("SELECT id, nama_produk, kategori, harga, stok FROM produk WHERE id = $pid")->fetch_assoc();
    if (!$produk) {
        echo "<script>alert('Produk ID $pid tidak ditemukan!');</script>";
        exit();
    }

    // Cek stok cukup
    if ($produk['stok'] < $qty) {
        echo "<script>alert('Stok {$produk['nama']} tidak cukup! Tersedia: {$produk['stok']}'); location.href='../user/beranda.php';</script>";
        exit();
    }

    $subtotal = $harga * $qty;
    $total += $subtotal;

    $valid_cart[] = [
        'id' => $pid,
        'qty' => $qty,
        'harga' => $harga,
        'subtotal' => $subtotal
    ];
}

// Simpan pesanan
$stmt = $db->prepare("INSERT INTO pesanan (user_id, total_harga, alamat_pengiriman, catatan) VALUES (?, ?, ?, ?)");
$stmt->bind_param("idss", $user_id, $total, $alamat, $catatan);
$pesanan_id = $db->insert_id;
$stmt->close();

// Simpan detail + kurangi stok (PAKAI TRANSACTION!)
$db->autocommit(FALSE); // Mulai transaksi

try {
    foreach ($valid_cart as $item) {
        $pid = $item['id'];
        $qty = $item['qty'];
        $harga = $item['harga'];
        $subtotal = $item['subtotal'];

        // Insert detail
        $stmt = $db->prepare("INSERT INTO detail_pesanan (pesanan_id, produk_id, jumlah, harga_satuan, subtotal) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("iiidd", $pesanan_id, $pid, $qty, $harga, $subtotal);
        $stmt->execute();
        $stmt->close();

        // Kurangi stok
        $stmt = $db->prepare("UPDATE produk SET stok = stok - ? WHERE id = ?");
        $stmt->bind_param("ii", $qty, $pid);
        $stmt->execute();
        $stmt->close();
    }

    $db->commit(); // Semua berhasil
    $db->autocommit(TRUE);

    // Simpan ID pesanan
    $_SESSION['pesanan_id'] = $pesanan_id;

    // Kosongkan keranjang
    echo "<script>
        localStorage.removeItem('cart');
        alert('Pesanan berhasil! Total: Rp " . number_format($total) . "');
        location.href='../user/beranda.php';
    </script>";

} catch (Exception $e) {
    $db->rollback();
    $db->autocommit(false);
    echo "<script>alert('Gagal menyimpan pesanan!'); location.href='../user/beranda.php';</script>";
}
?>