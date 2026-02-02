<?php
session_start();
include("../config.php");

if (!isset($_SESSION['checkout'])) {
    header("Location: ../user/cart.php");
    exit();
}

$pesanan_id = $_SESSION['pesanan_id'];
$username = $_SESSION['username'];

// Ambil data pesanan (PAKAI PREPARE!)
$stmt = $db->prepare("
    SELECT p.*, u.username AS nama, u.no_telp, p.alamat_pengiriman AS alamat
    FROM pesanan p 
    JOIN user u ON p.user_id = u.id 
    WHERE p.id = ? AND u.username = ?
");
$stmt->bind_param("is", $pesanan_id, $username);
$stmt->execute();
$pesanan = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$pesanan) {
    die("Pesanan tidak ditemukan!");
}

$total = $pesanan['total_harga'];
$metode = $_POST['metode'] ?? '';

// Proses pembayaran
if ($_POST['bayar']) {
    $metode = $db->real_escape_string($_POST['metode']);
    $status = ($metode === 'cod') ? 'menunggu konfirmasi' : 'menunggu pembayaran';

    $db->query("UPDATE pesanan SET metode_pembayaran = '$metode', status = '$status' WHERE id = $pesanan_id");

    // Hapus session pesanan
    unset($_SESSION['pesanan_id']);

    echo "<script>
        alert('Pembayaran dengan $metode berhasil dipilih!');
        location.href='../user/beranda.php?page=history';
    </script>";
    exit();
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pembayaran - Apotek NUR JAYA</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Poppins', sans-serif; background: #f8f9fa; }
        .payment-card { background: white; border-radius: 16px; padding: 2rem; box-shadow: 0 8px 25px rgba(0,0,0,0.1); }
        .method-card { border: 2px solid #eee; border-radius: 12px; padding: 1rem; cursor: pointer; transition: all 0.3s; }
        .method-card:hover { border-color: #4CAF50; }
        .method-card.selected { border-color: #4CAF50; background: #f0fff0; }
        .method-icon { font-size: 2rem; color: #4CAF50; }
        .btn-pay { background: linear-gradient(135deg, #4CAF50, #66BB6A); color: white; border: none; padding: 12px 40px; border-radius: 50px; font-weight: 600; }
        .btn-pay:hover { transform: translateY(-3px); box-shadow: 0 10px 20px rgba(76,175,80,0.3); }
    </style>
</head>
<body>
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="payment-card">
                    <div class="text-center mb-4">
                        <h3>Pilih Metode Pembayaran</h3>
                        <p class="text-muted">Pesanan ID: #<?= str_pad($pesanan_id, 5, '0', STR_PAD_LEFT) ?></p>
                    </div>

                    <div class="row mb-4">
                        <div class="col-md-6">
                            <div class="bg-light p-3 rounded">
                                <strong>Penerima:</strong> <?= htmlspecialchars($pesanan['nama']) ?><br>
                                <strong>No. Telp:</strong> <?= htmlspecialchars($pesanan['no_telp']) ?><br>
                                <strong>Alamat:</strong> <?= nl2br(htmlspecialchars($pesanan['alamat_pengiriman'])) ?>
                            </div>
                        </div>
                        <div class="col-md-6 text-end">
                            <h4>Total Bayar</h4>
                            <h2 class="text-success">Rp <?= number_format($total) ?></h2>
                        </div>
                    </div>

                    <form method="POST" id="paymentForm">
                        <div class="row g-3">
                            <!-- COD -->
                            <div class="col-md-6">
                                <div class="method-card text-center" data-method="cod">
                                    <div class="method-icon mb-2"><i class="bi bi-cash"></i></div>
                                    <h6>COD (Bayar di Tempat)</h6>
                                    <small class="text-muted">Bayar saat barang tiba</small>
                                </div>
                            </div>

                            <!-- Transfer Bank -->
                            <div class="col-md-6">
                                <div class="method-card text-center" data-method="transfer">
                                    <div class="method-icon mb-2"><i class="bi bi-bank"></i></div>
                                    <h6>Transfer Bank</h6>
                                    <small class="text-muted">BCA, BRI, Mandiri</small>
                                </div>
                            </div>

                            <!-- E-Wallet -->
                            <div class="col-md-6">
                                <div class="method-card text-center" data-method="ewallet">
                                    <div class="method-icon mb-2"><i class="bi bi-wallet2"></i></div>
                                    <h6>E-Wallet</h6>
                                    <small class="text-muted">GoPay, OVO, DANA</small>
                                </div>
                            </div>

                            <!-- Kartu Kredit -->
                            <div class="col-md-6">
                                <div class="method-card text-center" data-method="kartu">
                                    <div class="method-icon mb-2"><i class="bi bi-credit-card"></i></div>
                                    <h6>Kartu Kredit</h6>
                                    <small class="text-muted">Visa, MasterCard</small>
                                </div>
                            </div>
                        </div>

                        <input type="hidden" name="metode" id="selectedMethod" required>

                        <div class="text-center mt-4">
                            <button type="submit" name="bayar" class="btn btn-pay" id="payBtn" disabled>
                                <a href="../user/makasih.php"></a>
                                <i class="bi bi-check-lg"></i> Konfirmasi Pembayaran
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        // PILIH METODE
        document.querySelectorAll('.method-card').forEach(card => {
            card.addEventListener('click', () => {
                document.querySelectorAll('.method-card').forEach(c => c.classList.remove('selected'));
                card.classList.add('selected');
                document.getElementById('selectedMethod').value = card.dataset.method;
                document.getElementById('payBtn').disabled = false;
            });
        });
    </script>
</body>
</html>