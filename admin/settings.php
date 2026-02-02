<?php
session_start();
require_once("../config.php");

if (!isset($_SESSION['STRA'])) {
    header("Location: ../admin/login.php");
    exit();
}

$admin_name = $_SESSION['STRA'];

// Update Profile
if (isset($_POST['update_profile'])) {
    $new_name = $db->real_escape_string($_POST['nama']);
    $new_password = !empty($_POST['password']) ? password_hash($_POST['password'], PASSWORD_DEFAULT) : null;

    $query = "UPDATE apoteker SET STRA = '$new_name'";
    if ($new_password) {
        $query .= ", password = '$new_password'";
    }
    $query .= " WHERE nama_lengkap = '" . $_SESSION['nama_lengkap'] . "'";

    if ($db->query($query)) {
        $_SESSION['STRA'] = $new_name;
        $success = "Profile berhasil diperbarui!";
    } else {
        $error = "Gagal update: " . $db->error;
    }   
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Settings - Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://unpkg.com/aos@2.3.1/dist/aos.css">
    <style>
        :root {
            --primary: #4CAF50;
            --light: #C8E6C9;
            --dark: #388E3C;
            --danger: #FF5252;
            --bg: #ffffff;
            --text: #222222;
            --card-bg: #ffffff;
            --shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
        }

        /* MODE GELAP */
        [data-theme="dark"] {
            --bg: #1a1a1a;
            --text: #f0f0f0;
            --card-bg: #2d2d2d;
            --shadow: 0 8px 25px rgba(0, 0, 0, 0.3);
        }

        body {
            font-family: 'Poppins', sans-serif;
            background: var(--bg);
            color: var(--text);
            min-height: 100vh;
            margin: 0;
            transition: background 0.5s ease, color 0.5s ease;
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
            transition: background 0.5s ease;
        }

        .top-navbar {
            background: var(--card-bg);
            padding: 1.5rem 2rem;
            border-radius: 16px;
            box-shadow: var(--shadow);
            margin-bottom: 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            color: var(--text);
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
            color: var(--text);
        }

        .card-settings {
            background: var(--card-bg);
            border-radius: 16px;
            padding: 2rem;
            box-shadow: var(--shadow);
            max-width: 600px;
            margin: 0 auto;
            color: var(--text);
            transition: all 0.5s ease;
        }

        .form-control,
        .form-select {
            background: var(--card-bg);
            border: 1px solid #444;
            color: var(--text);
            border-radius: 12px;
            padding: 12px 16px;
            font-size: 1rem;
        }

        .form-control:focus {
            background: var(--card-bg);
            color: var(--text);
            border-color: var(--primary);
            box-shadow: 0 0 0 0.2rem rgba(76, 175, 80, 0.25);
        }

        .btn-save {
            background: linear-gradient(135deg, #4CAF50, #66BB6A);
            color: white;
            border: none;
            padding: 12px 30px;
            border-radius: 50px;
            font-weight: 600;
            font-size: 1rem;
            transition: all 0.3s;
        }

        .btn-save:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 20px rgba(76, 175, 80, 0.3);
        }

        .alert {
            border-radius: 12px;
            padding: 1rem 1.5rem;
            margin-bottom: 1.5rem;
        }

        /* TOGGLE MODE */
        .theme-toggle {
            background: var(--card-bg);
            border: 2px solid #444;
            width: 60px;
            height: 30px;
            border-radius: 50px;
            position: relative;
            cursor: pointer;
            transition: all 0.4s ease;
            box-shadow: inset 0 2px 5px rgba(0, 0, 0, 0.2);
        }

        .theme-toggle::before {
            content: '';
            position: absolute;
            top: 3px;
            left: 3px;
            width: 24px;
            height: 24px;
            background: #fff;
            border-radius: 50%;
            transition: all 0.4s ease;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
        }

        .theme-toggle.active {
            background: var(--primary);
            border-color: var(--primary);
        }

        .theme-toggle.active::before {
            transform: translateX(30px);
            background: white;
        }

        .theme-icon {
            position: absolute;
            top: 50%;
            transform: translateY(-50%);
            font-size: 0.9rem;
            color: #aaa;
            transition: all 0.4s ease;
        }

        .theme-icon.sun {
            left: 8px;
        }

        .theme-icon.moon {
            right: 8px;
            opacity: 0;
        }

        .theme-toggle.active .theme-icon.sun {
            opacity: 0;
        }

        .theme-toggle.active .theme-icon.moon {
            opacity: 1;
            color: white;
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

        <!-- NAVBAR -->
        <div class="top-navbar" data-aos="fade-down">
            <div>
                <h3>Pengaturan Akun</h3>
                <p class="text-muted mb-0">Kelola profil dan tema</p>
            </div>
            <div class="user-info text-end">
                <div>
                    <div class="date"><?= date('l, d F Y') ?></div>
                    <div class="clock" id="clock"></div>
                </div>
            </div>
        </div>

        <!-- CARD SETTINGS -->
        <div class="card-settings" data-aos="fade-up">
            <div class="text-center mb-4">
                <div class="bg-light rounded-circle mx-auto d-inline-flex align-items-center justify-content-center"
                    style="width: 90px; height: 90px; font-size: 2.5rem; color: var(--primary);">
                    <i class="bi bi-person-circle"></i>
                </div>
                <h4 class="mt-3 mb-1"><?= htmlspecialchars($admin_name) ?></h4>
                <p class="text-muted">Admin Apotek</p>
            </div>

            <!-- TOGGLE MODE GELAP -->
            <div class="d-flex justify-content-between align-items-center mb-4 p-3 rounded"
                style="background: rgba(0,0,0,0.05);">
                <div>
                    <h6 class="mb-0">Mode Gelap</h6>
                    <small class="text-muted">Aktifkan tema gelap</small>
                </div>
                <div class="theme-toggle" id="themeToggle">
                    <i class="bi bi-sun-fill theme-icon sun"></i>
                    <i class="bi bi-moon-fill theme-icon moon"></i>
                </div>
            </div>

            <?php if (isset($success)): ?>
                <div class="alert alert-success"><i class="bi bi-check-circle"></i> <?= $success ?></div>
            <?php endif; ?>
            <?php if (isset($error)): ?>
                <div class="alert alert-danger"><i class="bi bi-x-circle"></i> <?= $error ?></div>
            <?php endif; ?>

            <form method="POST">
                <div class="mb-3">
                    <label class="form-label fw-semibold">STRA Anda</label>
                    <input type="text" name="nama" class="form-control" value="<?= htmlspecialchars($admin_name) ?>"
                        required>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold">Password Baru <small class="text-muted">(kosongkan jika tidak
                            diganti)</small></label>
                    <input type="password" name="password" class="form-control" placeholder="Minimal 6 karakter">
                </div>
                <div class="text-center">
                    <button type="submit" name="update_profile" class="btn btn-save">
                        <i class="bi bi-check-lg"></i> Simpan Perubahan
                    </button>
                </div>
            </form>
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

        AOS.init({ duration: 800, easing: 'ease-out-cubic' });

        // JAM REAL-TIME
        function updateClock() {
            const now = new Date();
            const options = { hour: '2-digit', minute: '2-digit', second: '2-digit' };
            document.getElementById('clock').textContent = now.toLocaleTimeString('id-ID', options);
        }
        setInterval(updateClock, 1000);
        updateClock();

        // TEMA GELAP / TERANG
        const toggle = document.getElementById('themeToggle');
        const html = document.documentElement;

        // Cek tema dari localStorage
        const savedTheme = localStorage.getItem('theme') || 'light';
        if (savedTheme === 'dark') {
            html.setAttribute('data-theme', 'dark');
            toggle.classList.add('active');
        }

        // Toggle klik
        toggle.addEventListener('click', () => {
            if (html.getAttribute('data-theme') === 'dark') {
                html.removeAttribute('data-theme');
                toggle.classList.remove('active');
                localStorage.setItem('theme', 'light');
            } else {
                html.setAttribute('data-theme', 'dark');
                toggle.classList.add('active');
                localStorage.setItem('theme', 'dark');
            }
        });
    </script>
</body>

</html>