<?php include('config.php'); ?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Apotek Nur Jaya</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="index.css">

    <style>
        .hero-title {
            font-size: 2.8rem;
            font-weight: 800;
            line-height: 1.2;
            letter-spacing: -0.5px;
        }

        .hero-subtitle {
            font-size: 1.15rem;
            line-height: 1.7;
            color: #555;
            max-width: 800px;
            margin: 0 auto;
        }

        .section-title {
            font-size: 1.4rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1.5px;
            color: #2c3e50;
            margin-bottom: 2.5rem;
        }

        @media (max-width: 768px) {
            .hero-title { font-size: 2.2rem; }
            .hero-subtitle { font-size: 1rem; }
            .section-title { font-size: 1.2rem; }
        }
    </style>
</head>

<body>
    <div class="container my-5 py-4">
        <!-- Header Selamat Datang -->
        <div class="text-center" data-aos="fade-up" data-aos-duration="1000">
            <h1 class="hero-title text-primary mb-4">Selamat Datang di Apotek Nur Jaya</h1>
            <p class="hero-subtitle" data-aos="fade-up" data-aos-delay="200">
                Memperkenalkan <strong>Apotek Nur Jaya</strong>, aplikasi ini hadir untuk merevolusi cara Anda
                mengelola kebutuhan kesehatan dan farmasi. Kami memahami bahwa kesehatan adalah prioritas utama.
                Apotek Nur Jaya bukan hanya apotek online — ini adalah <strong>pendamping kesehatan pribadi Anda</strong>
                yang selalu siaga 24/7.
            </p>
        </div>

        <!-- Pilihan Role -->
        <div class="text-center">
            <h3 class="section-title" data-aos="fade-up" data-aos-delay="400">
                Pilih Peran Anda untuk Bergabung
            </h3>
        </div>

        <div class="d-flex justify-content-center gap-4 mt-4 flex-wrap" data-aos="fade-up" data-aos-delay="600">
            <!-- Dropdown User -->
            <div class="dropdown">
                <button class="btn btn-outline-primary btn-lg px-5 py-3 shadow-sm" type="button"
                    data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="bi bi-person me-2"></i> User
                </button>
                <ul class="dropdown-menu shadow">
                    <li><a class="dropdown-item" href="user/daftar.php">
                            <i class="bi bi-person-plus"></i> Registrasi
                        </a></li>
                    <li><a class="dropdown-item" href="user/login.php">
                            <i class="bi bi-box-arrow-in-right"></i> Login
                        </a></li>
                </ul>
            </div>

            <!-- Dropdown Admin -->
            <div class="dropdown">
                <button class="btn btn-outline-danger btn-lg px-5 py-3 shadow-sm" type="button"
                    data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="bi bi-shield-lock me-2"></i> Admin
                </button>
                <ul class="dropdown-menu shadow">
                    <li><a class="dropdown-item" href="admin/registrasi.php">
                            <i class="bi bi-person-plus"></i> Registrasi
                        </a></li>
                    <li><a class="dropdown-item" href="admin/login.php">
                            <i class="bi bi-box-arrow-in-right"></i> Login
                        </a></li>
                </ul>
            </div>
        </div>

        <!-- Notifikasi User -->
        <?php if (isset($_GET['statusU'])): ?>
            <div class="alert <?= ($_GET['statusU'] == 'suksesU') ? 'alert-success' : 'alert-danger'; ?> 
                         alert-dismissible fade show mt-4" role="alert" data-aos="fade-up" data-aos-delay="800">
                <strong><?= ($_GET['statusU'] == 'suksesU') ? 'Sukses!' : 'Gagal!'; ?></strong>
                <?= ($_GET['statusU'] == 'suksesU') ? 'Pendaftaran akun baru berhasil!' : 'Pendaftaran gagal!'; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <!-- Notifikasi Admin -->
        <?php if (isset($_GET['statusA'])): ?>
            <div class="alert <?= ($_GET['statusA'] == 'suksesA') ? 'alert-success' : 'alert-danger'; ?> 
                         alert-dismissible fade show mt-3" role="alert" data-aos="fade-up" data-aos-delay="800">
                <strong><?= ($_GET['statusA'] == 'suksesA') ? 'Sukses!' : 'Gagal!'; ?></strong>
                <?= ($_GET['statusA'] == 'suksesA') ? 'Pendaftaran Apoteker berhasil!' : 'Pendaftaran gagal!'; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script>
        AOS.init({
            once: true,
            duration: 1000,
            easing: 'ease-out'
        });
    </script>
</body>

</html>