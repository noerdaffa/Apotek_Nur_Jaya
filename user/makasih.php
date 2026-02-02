<?php
session_start();
include("../config.php");

if (!isset($_SESSION['last_pesanan_id'])) {
    header("Location: ../user/pembayaran.php");
    exit();
}

$pesanan_id = $_SESSION['last_pesanan_id'];
$username = $_SESSION['username'];

// Ambil data pesanan
$stmt = $db->prepare("
    SELECT p.*, u.username, u.no_telp 
    FROM pesanan p 
    JOIN user u ON p.user_id = u.id 
    WHERE p.id = ? AND u.username = ?
");
$stmt->bind_param("is", $pesanan_id, $username);
$stmt->execute();
$pesanan = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$pesanan)
    die("Pesanan tidak ditemukan!");

// Hapus session
unset($_SESSION['last_pesanan_id']);
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Terima Kasih - Apotek NUR JAYA</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #f8f9fa, #e8f5e8);
            font-family: 'Poppins', sans-serif;
        }

        .success-card {
            background: white;
            border-radius: 20px;
            padding: 2.5rem;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
            text-align: center;
        }

        .check-circle {
            width: 80px;
            height: 80px;
            background: #4CAF50;
            color: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1.5rem;
            font-size: 2.5rem;
        }

        .order-id {
            font-size: 1.5rem;
            font-weight: 700;
            color: #4CAF50;
        }

        .info-box {
            background: #f8f9fa;
            padding: 1rem;
            border-radius: 12px;
            text-align: left;
            margin: 1rem 0;
        }

        .btn-home {
            background: #4CAF50;
            color: white;
            padding: 12px 30px;
            border-radius: 50px;
            font-weight: 600;
        }

        .btn-home:hover {
            background: #388E3C;
            transform: translateY(-3px);
        }
    </style>
</head>

<body class="py-5">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-7">
                <div class="success-card">
                    <div class="check-circle">
                        <i class="bi bi-check-lg"></i>
                    </div>
                    <h2 class="mb-3">Pesanan Berhasil!</h2>
                    <p class="text-muted mb-4">Terima kasih telah berbelanja di Apotek NUR JAYA</p>

                    <div class="order-id">#<?= str_pad($pesanan_id, 5, '0', STR_PAD_LEFT) ?></div>

                    <div class="info-box">
                        <strong>Metode Pembayaran:</strong>
                        <span class="text-success">
                            <?= ucfirst(str_replace('_', ' ', $pesanan['metode_pembayaran'])) ?>
                        </span>
                    </div>

                    <?php if ($pesanan['metode_pembayaran'] !== 'cod'): ?>
                        <div class="alert alert-warning">
                            <strong>Transfer ke:</strong><br>
                            <strong>BCA 123-456-789 a.n. Apotek NUR JAYA</strong><br>
                            <small>Total: <strong>Rp <?= number_format($pesanan['total_harga']) ?></strong></small>
                        </div>
                    <?php else: ?>
                        <div class="alert alert-info">
                            <i class="bi bi-info-circle"></i>
                            Bayar saat kurir tiba. Siapkan uang pas.
                        </div>
                    <?php endif; ?>

                    <div class="mt-4">
                        <a href="beranda.php" class="btn btn-home">
                            Kembali ke Beranda
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>

</html>