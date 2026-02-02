<?php
session_start();
require_once("../config.php");

// Cek login admin
if (!isset($_SESSION['STRA'])) {
    header("Location: ../admin/login.php");
    exit();
}

$admin_name = $_SESSION['STRA'];

// Update status
if (isset($_POST['update_status'])) {
    $id = (int) $_POST['id'];
    $status = $db->real_escape_string($_POST['status']);
    $db->query("UPDATE pesanan SET status = '$status' WHERE id = $id");
    echo "<script>
    alert('Status diperbarui!');
    </script>";
}

$pesanan = $db->query("
    SELECT p.*, u.username 
    FROM pesanan p 
    JOIN user u ON p.user_id = u.id 
    ORDER BY p.tanggal_pesan DESC
");
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pesanan - Admin</title>
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
            margin: 0;
        }

        /* SIDEBAR */
        .sidebar {
            background: linear-gradient(180deg, #ff0000ff 0%, #d82c2cff 100%);
            min-height: 100vh;
            padding: 2rem 1.5rem;
            position: fixed;
            width: 280px;
            box-shadow: 5px 0 15px rgba(0, 0, 0, 0.1);
            z-index: 1000;
            color: white;
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
            display: flex;
            align-items: center;
            gap: 12px;
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

        /* MAIN CONTENT */
        .main-content {
            margin-left: 280px;
            padding: 2rem;
        }

        /* NAVBAR ATAS (HIJAU) */
        .top-navbar {
            background: white;
            padding: 1.5rem 2rem;
            border-radius: 16px;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.08);
            margin-bottom: 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .top-navbar h3 {
            margin: 0;
            color: var(--dark);
            font-weight: 600;
        }

        .top-navbar .user-info {
            display: flex;
            align-items: center;
            gap: 12px;
            color: #555;
        }

        .top-navbar .date {
            font-size: 1.1rem;
            font-weight: 500;
        }

        .top-navbar .clock {
            font-family: monospace;
            font-size: 1rem;
        }

        /* CARD PESANAN */
        .card-pesanan {
            background: white;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
        }

        .card-header-custom {
            background: linear-gradient(135deg, #4CAF50, #66BB6A);
            color: white;
            padding: 1.5rem 2rem;
            font-weight: 600;
            font-size: 1.3rem;
        }

        .table {
            margin: 0;
        }

        .status-select {
            width: 120px;
            font-size: 0.9rem;
        }
        
        /* Hapus definisi badge khusus, gunakan badge Bootstrap 5 */

        .btn-detail {
            font-size: 0.85rem;
            padding: 6px 12px;
        }
    </style>
</head>

<body>

    <div class="sidebar">
        <div class="logo">Apotek NUR JAYA</div>
        <a href="../admin/dashboard.php" class="nav-link"><i class="bi bi-house"></i> Dashboard</a>
        <a href="../admin/kelola_produk.php" class="nav-link"><i class="bi bi-box-seam"></i> Kelola Produk</a>
        <a href="../admin/pesanan.php" class="nav-link active"><i class="bi bi-receipt"></i> Pesanan</a>
        <a href="../admin/settings.php" class="nav-link"><i class="bi bi-person-plus"></i> Settings</a>
        <a href="../logout.php" class="nav-link"><i class="bi bi-box-arrow-left"></i> Logout</a>
    </div>

    <div class="main-content">

        <div class="top-navbar" data-aos="fade-down">
            <div>
                <h3>Daftar Pesanan</h3>
                <p class="text-muted mb-0">Kelola semua pesanan pelanggan</p>
            </div>
            <div class="user-info text-end">
                <div>
                    <div class="date"><?= date('l, d F Y') ?></div>
                    <div class="clock" id="clock"></div>
                </div>
            </div>
        </div>

        <div class="card-pesanan" data-aos="fade-up">
            <div class="card-header-custom">
                <i class="bi bi-list-ul"></i> Daftar Pesanan
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-4">No</th>
                                <th>User</th>
                                <th>Tanggal</th>
                                <th>Total</th>
                                <th>Alamat</th>
                                <th>Status</th>
                                <th class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $no = 1;
                            while ($p = $pesanan->fetch_assoc()): ?>
                                <tr>
                                    <td class="ps-4"><?= $no++ ?></td>
                                    <td><strong><?= htmlspecialchars($p['username']) ?></strong></td>
                                    <td><?= date('d/m/Y H:i', strtotime($p['tanggal_pesan'])) ?></td>
                                    <td><strong>Rp <?= number_format($p['total_harga']) ?></strong></td>
                                    <td><?= substr(htmlspecialchars($p['alamat_pengiriman']), 0, 30) ?>...</td>
                                    <td>
                                        <?php 
                                            $badge_class = '';
                                            switch($p['status']) {
                                                case 'pending': $badge_class = 'bg-warning text-dark'; break;
                                                case 'diproses': $badge_class = 'bg-info'; break;
                                                case 'dikirim': $badge_class = 'bg-primary'; break;
                                                case 'selesai': $badge_class = 'bg-success'; break;
                                                default: $badge_class = 'bg-secondary';
                                            }
                                        ?>
                                        <span class="badge <?= $badge_class ?>">
                                            <?= ucfirst(str_replace('_', ' ', $p['status'])) ?>
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <form method="POST" class="d-inline">
                                            <input type="hidden" name="id" value="<?= $p['id'] ?>">
                                            <select name="status" class="form-select form-select-sm status-select d-inline"
                                                onchange="this.form.submit()">
                                                <option value="pending" <?= $p['status'] == 'pending' ? 'selected' : '' ?>>
                                                    Pending
                                                </option>
                                                <option value="diproses" <?= $p['status'] == 'diproses' ? 'selected' : '' ?>>
                                                    Diproses</option>
                                                <option value="dikirim" <?= $p['status'] == 'dikirim' ? 'selected' : '' ?>>
                                                    Dikirim
                                                </option>
                                                <option value="selesai" <?= $p['status'] == 'selesai' ? 'selected' : '' ?>>
                                                    Selesai
                                                </option>
                                            </select>
                                            <input type="hidden" name="update_status">
                                        </form>
                                        <button class="btn btn-info btn-sm btn-detail mt-1"
                                            onclick="lihatDetail(<?= $p['id'] ?>)">
                                            <i class="bi bi-eye"></i> Detail
                                        </button>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>

    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
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

        // Lihat detail
        function lihatDetail(id) {
            window.open('../admin/detail_pesanan.php?id=' + id, '_blank', 'width=900,height=700');
        }
    </script>
</body>

</html>