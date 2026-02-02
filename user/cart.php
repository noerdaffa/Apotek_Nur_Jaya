<?php
session_start();
include("../config.php");

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

$username = $_SESSION['username'];
$user = $db->query("SELECT id FROM user WHERE username = '$username'")->fetch_assoc();
$user_id = $user['id'];

// Proses checkout
if (isset($_POST['checkout'])) {
    $alamat = $db->real_escape_string($_POST['alamat']);
    $catatan = $db->real_escape_string($_POST['catatan']);
    $cart = json_decode($_POST['cart_data'], true);
    $total = 0;

    foreach ($cart as $item) {
        $total += $item['harga'] * $item['qty'];
    }

    // Simpan pesanan
    $db->query("INSERT INTO pesanan (user_id, total_harga, alamat_pengiriman, catatan, metode_pembayaran) VALUES ($user_id, $total, '$alamat', '$catatan', 'metode_pembayaran')");
    $pesanan_id = $db->insert_id;

    // Simpan detail + kurangi stok
    foreach ($cart as $item) {
        $pid = $item['id'];
        $qty = $item['qty'];
        $harga = $item['harga'];
        $subtotal = $harga * $qty;

        $db->query("INSERT INTO detail_pesanan (pesanan_id, produk_id, jumlah, harga_satuan, subtotal) 
                    VALUES ($pesanan_id, $pid, $qty, $harga, $subtotal)");
        
        $db->query("UPDATE produk SET stok = stok - $qty WHERE id = $pid");
    }

    echo "<script>alert('Pesanan berhasil!'); localStorage.removeItem('cart'); location.href='beranda.php';</script>";
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Keranjang - Apotek NUR JAYA</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://unpkg.com/aos@2.3.1/dist/aos.css">
    <style>
        :root {--primary:#4CAF50; --light:#C8E6C9; --dark:#388E3C;}
        body {font-family:'Poppins',sans-serif; background:linear-gradient(135deg,#f0f7f1,#e8f5e9); min-height:100vh;}
        .cart-container {max-width:900px; margin:2rem auto; padding:0 1rem;}
        .card {border-radius:16px; box-shadow:0 8px 25px rgba(0,0,0,0.1);}
        .btn-success {background:var(--primary); border:none; border-radius:50px; padding:12px 30px;}
        .btn-success:hover {background:var(--dark);}
        .qty-btn {width:35px; height:35px; border-radius:50%; background:var(--primary); color:white;}
        .cart-item img {width:60px; height:60px; object-fits:cover; border-radius:8px;}
    </style>
</head>
<body>
    <div class="cart-container">
        <div class="card p-4" data-aos="fade-up">
            <h3 class="mb-4"><i class="bi bi-cart"></i> Keranjang Belanja</h3>
            <div id="cartItems"></div>
            <hr>
            <div class="d-flex justify-content-between mb-3">
                <strong>Total:</strong>
                <strong id="totalHarga">Rp 0</strong>
            </div>

            <form method="POST" id="checkoutForm">
                <input type="hidden" name="cart_data" id="cartData">
                <div class="mb-3">
                    <label class="form-label">Alamat Pengiriman</label>
                    <textarea name="alamat" class="form-control" rows="2" required></textarea>
                </div>
                <div class="mb-3">
                    <label class="form-label">Catatan (opsional)</label>
                    <textarea name="catatan" class="form-control" rows="2"></textarea>
                </div>
                <button type="submit" name="checkout" class="btn btn-success w-100">
                    <i class="bi bi-check2"></i> Checkout & Pesan
                </button>
            </form>
            <a href="../user/beranda.php" class="btn btn-outline-secondary w-100 mt-2">Kembali Belanja</a>
        </div>
    </div>

    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script>
        AOS.init();
        let cart = JSON.parse(localStorage.getItem('cart')) || [];
        const container = document.getElementById('cartItems');
        let total = 0;

        if (cart.length === 0) {
            container.innerHTML = '<p class="text-center text-muted">Keranjang kosong.</p>';
        } else {
            cart.forEach((item, index) => {
                total += item.harga * item.qty;
                container.innerHTML += `
                <div class="d-flex align-items-center mb-3 p-3 bg-light rounded" data-aos="fade-up" data-aos-delay="${index*100}">
                    <img src="../image/produk/${item.gambar}" alt="${item.nama}">
                    <div class="flex-grow-1 ms-3">
                        <h6 class="mb-1">${item.nama}</h6>
                        <small class="text-muted">Rp ${item.harga.toLocaleString()}</small>
                    </div>
                    <div class="d-flex align-items-center">
                        <button class="btn qty-btn" onclick="updateQty(${index}, -1)">-</button>
                        <span class="mx-3 fw-bold">${item.qty}</span>
                        <button class="btn qty-btn" onclick="updateQty(${index}, 1)">+</button>
                    </div>
                    <button class="btn text-danger ms-3" onclick="removeItem(${index})">
                        <i class="bi bi-trash"></i>
                    </button>
                </div>`;
            });
        }

        document.getElementById('totalHarga').textContent = 'Rp ' + total.toLocaleString();
        document.getElementById('cartData').value = JSON.stringify(cart);

        function updateQty(index, change) {
            cart[index].qty = Math.max(1, cart[index].qty + change);
            localStorage.setItem('cart', JSON.stringify(cart));
            location.reload();
        }

        function removeItem(index) {
            cart.splice(index, 1);
            localStorage.setItem('cart', JSON.stringify(cart));
            location.reload();
        }

        // Update badge di beranda
        window.addEventListener('storage', () => location.reload());
    </script>
</body>
</html>