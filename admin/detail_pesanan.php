<?php
include("../config.php");
$id = $_GET['id'];
$pesanan = $db->query("SELECT * FROM pesanan WHERE id = $id")->fetch_assoc();
$detail_pesanan = $db->query("SELECT d.*, p.nama_produk FROM detail_pesanan d JOIN produk p ON d.produk_id = p.id WHERE d.pesanan_id = $id");
?>
<!DOCTYPE html>
<html>

<head>
    <title>Detail Pesanan #<?= $id ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="p-4">
    <h4>Pesanan #<?= $id ?> - <?= $pesanan['id'] ?></h4>
    <p><strong>Produk:</strong> <?= $pesanan['produk_id'] ?></p>
    <p><strong>Alamat :</strong> <?= $pesanan['alamat_pengiriman'] ?></p>
    <p><strong>Status:</strong> <?= $pesanan['status'] ?></p>
    <p><strong>Tanggal:</strong> <?= $pesanan['tanggal_pesan'] ?></p>

    <table class="table">
        <tbody>
            <?php while ($d = $detail->fetch_assoc()): ?>
                <tr>
                    <td>produk<?= $id['produk_id'] ?></td>
                    <td>Jumlah<?= $id['jumlah'] ?></td>
                    <td>Rp <?= number_format($pesanan['harga_satuan']) ?></td>
                    <td>Rp <?= number_format($pesanan['subtotal']) ?></td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>

    <h5>Total: Rp <?= number_format($pesanan['total_harga']) ?></h5>
    <script>
        const savedTheme = localStorage.getItem('theme') || 'light';
        if (savedTheme === 'dark') {
            document.documentElement.setAttribute('data-theme', 'dark');
        }
    </script>
</body>

</html>