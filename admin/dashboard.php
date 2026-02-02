<?php
session_start();
require_once("../config.php");

// Cek apakah user adalah admin
if (!isset($_SESSION['STRA'])) {
    header("Location: ../admin/login.php");
    exit();
}

// Ambil nama admin
$STRA = $_SESSION['STRA'] ?? 'STRA';
// Hitung statistik
$total_produk = $db->query("SELECT COUNT(*) FROM produk")->fetch_row()[0];
$total_obat = $db->query("SELECT COUNT(*) FROM produk WHERE kategori='obat'")->fetch_row()[0];
$total_vitamin = $db->query("SELECT COUNT(*) FROM produk WHERE kategori='vitamin'")->fetch_row()[0];
$total_herbal = $db->query("SELECT COUNT(*) FROM produk WHERE kategori='herbal'")->fetch_row()[0];
$total_susu = $db->query("SELECT COUNT(*) FROM produk WHERE kategori='susu'")->fetch_row()[0];
$stok_kosong = $db->query("SELECT COUNT(*) FROM produk WHERE stok <= 0")->fetch_row()[0];
$produk_terbaru = $db->query("SELECT * FROM produk ORDER BY tanggal DESC LIMIT 5");

?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin - Apotek Nur Jaya</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://unpkg.com/aos@2.3.1/dist/aos.css">
    <style>
        :root {
            --primary: #4CAF50;
            --light: #C8E6C9;
            --dark: #388E3C;
            --success: #8BC34A;
            --warning: #FFCA28;
            --danger: #FF5252;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, #32f54fff 0%, #20fa32ff 100%);
            min-height: 100vh;
        }

        .sidebar {
            background: linear-gradient(180deg, #ff0000ff 0%, #d82c2cff 100%);
            min-height: 100vh;
            padding: 2rem 1.5rem;
            position: fixed;
            width: 280px;
            box-shadow: 5px 0 15px rgba(0, 0, 0, 0.1);
            z-index: 1000;
        }

        .sidebar .logo {
            font-size: 2rem;
            font-weight: 700;
            color: white;
            text-align: center;
            margin-bottom: 2rem;
        }

        .nav-link {
            color: white;
            padding: 14px 20px;
            border-radius: 12px;
            margin: 8px 0;
            transition: all 0.3s;
            font-weight: 500;
        }

        .nav-link.active {
            background: rgba(255, 255, 255, 0.2) !important;
            border-radius: 12px;
            color: white !important;
            font-weight: 600;
        }

        .nav-link i {
            width: 30px;
            font-size: 1.2rem;
        }

        .main-content {
            margin-left: 280px;
            padding: 2rem;
        }

        .header {
            background: white;
            padding: 1.5rem 2rem;
            border-radius: 16px;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.08);
            margin-bottom: 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .stats-card {
            background: white;
            border-radius: 16px;
            padding: 1.8rem;
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
            transition: all 0.3s;
            border-left: 5px solid var(--primary);
        }

        .stats-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.15);
        }

        .stats-icon {
            width: 70px;
            height: 70px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2rem;
            color: white;
            margin-bottom: 1rem;
        }

        .card-obat {
            background: linear-gradient(135deg, #4CAF50, #66BB6A);
        }

        .card-vitamin {
            background: linear-gradient(135deg, #8BC34A, #9CCC65);
        }

        .card-herbal {
            background: linear-gradient(135deg, #FFCA28, #FFB300);
        }

        .card-susu {
            background: linear-gradient(135deg, #42A5F5, #1E88E5);
        }

        .card-kosong {
            background: linear-gradient(135deg, #FF5252, #F44336);
        }

        .table {
            background: white;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
        }

        .btn-action {
            padding: 8px 16px;
            border-radius: 50px;
            font-size: 0.9rem;
        }

        .badge-stok {
            padding: 6px 12px;
            border-radius: 50px;
            font-size: 0.8rem;
        }
    </style>
</head>

<body>

    <!-- SIDEBAR -->
    <div class="sidebar">
        <div class="logo">Apotek NUR JAYA</div>
        <a href="../admin/dashboard.php" class="nav-link"><i class="bi bi-house"></i> Dashboard</a>
        <a href="../admin/kelola_produk.php" class="nav-link"><i class="bi bi-box-seam"></i> Kelola Produk</a>
        <a href="../admin/pesanan.php" class="nav-link active"><i class="bi bi-receipt"></i> Pesanan</a>
        <a href="../admin/settings.php" class="nav-link"><i class="bi bi-person-plus"></i> Settings</a>
        <a href="../logout.php" class="nav-link"><i class="bi bi-box-arrow-left"></i> Logout</a>
    </div>

    <!-- MAIN CONTENT -->
    <div class="main-content">
        <div class="header" data-aos="fade-down">
            <div>
                <h3 class="mb-0">Dashboard Admin</h3>
                <p class="text-muted mb-0">Selamat datang kembali, <strong><?= htmlspecialchars($STRA) ?></strong></p>
            </div>
            <div class="text-end">
                <div class="fs-4"><?= date('l, d F Y') ?></div>
                <div id="clock" class="text-muted"></div>
            </div>
        </div>

        <!-- STATISTIK -->
        <div class="row g-4 mb-5">
            <div class="col-md-4" data-aos="fade-up" data-aos-delay="100">
                <div class="stats-card text-center">
                    <div class="stats-icon card-obat"><i class="bi bi-capsule"></i></div>
                    <h2 class="mb-0"><?= $total_obat ?></h2>
                    <p class="text-muted">Total Obat</p>
                </div>
            </div>
            <div class="col-md-4" data-aos="fade-up" data-aos-delay="200">
                <div class="stats-card text-center">
                    <div class="stats-icon card-vitamin"><i class="bi bi-heart-pulse"></i></div>
                    <h2 class="mb-0"><?= $total_vitamin ?></h2>
                    <p class="text-muted">Vitamin & Suplemen</p>
                </div>
            </div>
            <div class="col-md-4" data-aos="fade-up" data-aos-delay="300">
                <div class="stats-card text-center">
                    <div class="stats-icon card-herbal"><i class="bi bi-flower1"></i></div>
                    <h2 class="mb-0"><?= $total_herbal + $total_susu ?></h2>
                    <p class="text-muted">Herbal & Susu</p>
                </div>
            </div>
        </div>

        <div class="row g-4">
            <!-- TOTAL PRODUK & STOK KOSONG -->
            <div class="col-md-6" data-aos="fade-right">
                <div class="stats-card text-center">
                    <div class="stats-icon mx-auto" style="background: linear-gradient(135deg, #2196F3, #1976D2);">
                        <i class="bi bi-boxes"></i>
                    </div>
                    <h2 class="mb-0"><?= $total_produk ?></h2>
                    <p class="text-muted">Total Semua Produk</p>
                </div>
            </div>
            <div class="col-md-6" data-aos="fade-left">
                <div class="stats-card text-center">
                    <div class="stats-icon card-kosong mx-auto">
                        <i class="bi bi-exclamation-triangle"></i>
                    </div>
                    <h2 class="mb-0"><?= $stok_kosong ?></h2>
                    <p class="text-danger fw-bold">Stok Habis!</p>
                </div>
            </div>
        </div>

        <!-- PRODUK TERBARU -->
        <div class="mt-5" data-aos="fade-up">
            <h4 class="mb-4"><i class="bi bi-stars"></i> Produk Terbaru</h4>
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead style="background: var(--light);">
                        <tr>
                            <th width="200">Gambar</th>
                            <th>Produk</th>
                            <th>Kategori</th>
                            <th>Harga</th>
                            <th>Stok</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($p = $produk_terbaru->fetch_assoc()): ?>
                            <tr>
                                <td><img src="/../image/produk/<?= $p['gambar'] ?>" width="50" class="rounded"></td>
                                <td><strong><?= htmlspecialchars($p['nama_produk']) ?></strong></td>
                                <td>
                                    <span class="badge bg-success"><?= ucfirst($p['kategori']) ?></span>
                                </td>
                                <td>Rp <?= number_format($p['harga']) ?></td>
                                <td><?= $p['stok'] ?></td>
                                <td>
                                    <?php if ($p['stok'] > 0): ?>
                                        <span class="badge bg-success badge-stok">Tersedia</span>
                                    <?php else: ?>
                                        <span class="badge bg-danger badge-stok">Habis</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                        <i class="bi bi-trash"></i>
                                    </a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- QUICK ACTION -->
        <div class="text-center mt-5" data-aos="zoom-in">
            <a href="../admin/kelola_produk.php" class="btn btn-success btn-lg px-5 py-3"
                style="border-radius: 50px; font-size: 1.1rem;">
                <i class="bi bi-plus-circle-fill"></i> Tambah Produk Baru
            </a>
        </div>
    </div>

    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <!-- Di <head> -->
    <script>

        // NAVIGASI SIDEBAR - UPDATE ACTIVE CLASS
        document.querySelectorAll('.nav-link').forEach(link => {
            link.addEventListener('click', function (e) {
                // Hapus active dari semua
                document.querySelectorAll('.nav-link').forEach(l => l.classList.remove('active'));

                // Tambah active ke yang diklik
                this.classList.add('active');
            });
        });

        // SET ACTIVE BERDASARKAN HALAMAN SAAT LOAD
        document.addEventListener('DOMContentLoaded', () => {
            const currentPage = window.location.pathname.split('/').pop();
            const menuMap = {
                'dashboard.php': 'dashboard',
                'kelola_produk.php': 'produk',
                'pesanan.php': 'pesanan',
                'settings.php': 'settings',
                'logout.php': 'logout'
            };

            const pageKey = Object.keys(menuMap).find(file => currentPage.includes(file));
            if (pageKey) {
                const activeLink = document.querySelector(`[href*="${menuMap[pageKey]}"]`);
                if (activeLink) {
                    activeLink.classList.add('active');
                }
            }
        });

        const savedTheme = localStorage.getItem('theme') || 'light';
        if (savedTheme === 'dark') {
            document.documentElement.setAttribute('data-theme', 'dark');
        }

        AOS.init({ duration: 800, easing: 'ease-out-cubic' });

        // Jam real-time
        function updateClock() {
            const now = new Date();
            const options = { hour: '2-digit', minute: '2-digit', second: '2-digit' };
            document.getElementById('clock').textContent = now.toLocaleTimeString('id-ID', options);
        }
        setInterval(updateClock, 1000);
        updateClock();
    </script>
</body>

</html>