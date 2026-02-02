<?php
session_start();
include("../config.php");

// === TAMBAH PRODUK ===
if (isset($_POST['tambah'])) {
    $nama = $_POST['nama_produk'];
    $kategori = $_POST['kategori'];
    $harga = $_POST['harga'];
    $stok = $_POST['stok'];
    $deskripsi = $_POST['deskripsi'];

    // Validasi upload
    if (empty($_FILES['gambar']['name'])) {
        $error = "Pilih gambar produk!";
    } else {
        $gambar = $_FILES['gambar']['name'];
        $tmp_name = $_FILES['gambar']['tmp_name'];
        $ext = strtolower(pathinfo($gambar, PATHINFO_EXTENSION));
        $allowed = ['jpg', 'jpeg', 'png', 'gif'];

        if (!in_array($ext, $allowed)) {
            $error = "File harus JPG, JPEG, PNG, atau GIF!";
        } elseif ($_FILES['gambar']['size'] > 2 * 1024 * 1024) {
            $error = "Ukuran maksimal 2MB!";
        } else {
            // Gunakan path absolut untuk upload
            $upload_dir = $_SERVER['DOCUMENT_ROOT'] . "/image/produk/";

            // Pastikan folder ada
            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0755, true);
            }

            // Nama unik
            $new_filename = uniqid('prod_') . "." . $ext;
            $target = $upload_dir . $new_filename;

            if (move_uploaded_file($tmp_name, $target)) {
                // ...
                // Tidak perlu memasukkan 'tanggal' di query karena database akan mengurusnya
                $sql = "INSERT INTO produk (nama_produk, kategori, harga, stok, gambar, deskripsi) VALUES (?, ?, ?, ?, ?, ?)";
                $stmt = $db->prepare($sql);
                $stmt->bind_param("ssiiis", $nama, $kategori, $harga, $stok, $new_filename, $deskripsi);
                // ...
                if ($stmt->execute()) {
                    $sukses = "Produk berhasil ditambahkan!";
                } else {
                    $error = "Gagal simpan ke database: " . $db->error;
                    @unlink($target);
                }
            } else {
                $error = "Gagal upload gambar. Error: " . $_FILES['gambar']['error'];
                // Debug info
                error_log("Upload failed. tmp_name: $tmp_name, target: $target");
            }
        }
    }
}

// === HAPUS PRODUK ===
if (isset($_GET['hapus'])) {
    $id = (int) $_GET['hapus'];
    $result = $db->query("SELECT gambar FROM produk WHERE id = $id");
    if ($result && $row = $result->fetch_assoc()) {
        $gbr = $row['gambar'];
        $gbr_path = $_SERVER['DOCUMENT_ROOT'] . "/image/produk/" . $gbr;
        if ($gbr && file_exists($gbr_path)) {
            unlink($gbr_path);
        }
    }
    $db->query("DELETE FROM produk WHERE id = $id");
    header("Location: kelola_produk.php");
    exit();
}

// === Ambil semua produk ===
$produk = $db->query("SELECT * FROM produk ORDER BY tanggal DESC");
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Produk - Apotek NUR JAYA</title>
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
            background: rgba(255, 255, 255, 0.2);
            border-radius: 12px;
            color: white;
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

        .card {
            background: white;
            border-radius: 16px;
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }

        .form-control,
        .form-select {
            border-radius: 12px;
            padding: 12px 16px;
        }

        .btn-success {
            background: var(--primary);
            border: none;
            border-radius: 50px;
            padding: 12px 30px;
            font-weight: 600;
        }

        .btn-success:hover {
            background: var(--dark);
        }

        .table thead {
            background: var(--light);
        }

        .badge-stok {
            padding: 6px 12px;
            border-radius: 50px;
            font-size: 0.8rem;
        }

        .product-img {
            width: 60px;
            height: 60px;
            object-fit: cover;
            border-radius: 8px;
        }
    </style>
</head>

<body>

    <div class="sidebar">
        <div class="logo">Apotek NUR JAYA</div>
        <a href="../admin/dashboard.php" class="nav-link"><i class="bi bi-house"></i> Dashboard</a>
        <a href="../admin/kelola_produk.php" class="nav-link active"><i class="bi bi-box-seam"></i> Kelola Produk</a>
        <a href="../admin/pesanan.php" class="nav-link"><i class="bi bi-receipt"></i> Pesanan</a>
        <a href="../admin/settings.php" class="nav-link"><i class="bi bi-person-plus"></i> Settings</a>
        <a href="../logout.php" class="nav-link"><i class="bi bi-box-arrow-left"></i> Logout</a>
    </div>

    <div class="main-content">
        <div class="header" data-aos="fade-down">
            <div>
                <h3 class="mb-0">Kelola Produk</h3>
                <p class="text-muted mb-0">Tambah atau hapus produk</p>
            </div>
            <div class="text-end">
                <div class="fs-4"><?= date('l, d F Y') ?></div>
                <div id="clock" class="text-muted"></div>
            </div>
        </div>

        <?php if (isset($sukses)): ?>
            <div class="alert alert-success alert-dismissible fade show" data-aos="fade-up">
                <?= $sukses ?> <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>
        <?php if (isset($error)): ?>
            <div class="alert alert-danger alert-dismissible fade show" data-aos="fade-up">
                <?= $error ?> <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <div class="card mb-5" data-aos="fade-up" data-aos-delay="100">
            <div class="card-body p-4">
                <h5 class="mb-4">Tambah Produk Baru</h5>
                <form method="POST" enctype="multipart/form-data">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <input type="text" name="nama_produk" class="form-control" placeholder="Nama Produk"
                                required>
                        </div>
                        <div class="col-md-6">
                            <select name="kategori" class="form-select" required>
                                <option value="">Pilih Kategori</option>
                                <option value="obat">Obat</option>
                                <option value="vitamin">Vitamin</option>
                                <option value="herbal">Herbal</option>
                                <option value="susu">Susu</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <input type="number" name="harga" class="form-control" placeholder="Harga (Rp)" required>
                        </div>
                        <div class="col-md-4">
                            <input type="number" name="stok" class="form-control" placeholder="Stok" required>
                        </div>
                        <div class="col-md-4">
                            <input type="file" name="gambar" class="form-control" accept="image/*" required>
                            <small class="text-muted">Format: JPG, PNG, GIF. Maks: 2MB</small>
                        </div>
                        <div class="col-12">
                            <textarea name="deskripsi" class="form-control" rows="2"
                                placeholder="Deskripsi (opsional)"></textarea>
                        </div>
                        <div class="col-12">
                            <button type="submit" name="tambah" class="btn btn-success">
                                <i class="bi bi-plus-circle"></i> Tambah Produk
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <div class="mt-5" data-aos="fade-up" data-aos-delay="200">
            <h4 class="mb-4"><i class="bi bi-stars"></i>Produk Terbaru</h4>
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
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        if ($produk && $produk->num_rows > 0):
                            while ($p = $produk->fetch_assoc()):
                                // PERBAIKAN: Cek apakah file gambar ada
                                $img_path = $_SERVER['DOCUMENT_ROOT'] . "../image/produk/" . $p['gambar'];
                                $img_url = "../image/produk/" . htmlspecialchars($p['gambar']);
                                $img_exists = file_exists($img_path);
                                ?>
                                <td>
                                    <strong><?= htmlspecialchars($p['nama_produk']) ?></strong>
                                    <?php if (!empty($p['deskripsi'])): ?>
                                        <br><small class="text-muted"><?= htmlspecialchars($p['deskripsi']) ?></small>
                                    <?php endif; ?>
                                </td>
                                <td><span class="badge bg-success"><?= ucfirst($p['kategori']) ?></span></td>
                                <td>Rp <?= number_format($p['harga'], 0, ',', '.') ?></td>
                                <td><?= $p['stok'] ?></td>
                                <td>
                                    <span class="badge bg-<?= $p['stok'] > 0 ? 'success' : 'danger' ?> badge-stok">
                                        <?= $p['stok'] > 0 ? 'Tersedia' : 'Habis' ?>
                                    </span>
                                </td>
                                <td>
                                    <a href="?hapus=<?= $p['id'] ?>" class="btn btn-danger btn-sm"
                                        onclick="return confirm('Hapus <?= addslashes($p['nama_produk']) ?>?')">
                                        <i class="bi bi-trash"></i> Hapus
                                    </a>
                                </td>
                                </tr>
                                <?php
                            endwhile;
                        else:
                            ?>
                            <tr>
                                <td colspan="7" class="text-center text-muted py-4">
                                    <i class="bi bi-inbox display-4 d-block mb-2"></i>
                                    Belum ada produk. Tambah sekarang!
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    </div>

    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Active menu
        document.addEventListener('DOMContentLoaded', () => {
            document.querySelectorAll('.nav-link').forEach(l => l.classList.remove('active'));
            const current = window.location.pathname.split('/').pop();
            const activeLink = document.querySelector(`a[href*="${current}"]`);
            if (activeLink) activeLink.classList.add('active');
        });

        AOS.init({ duration: 800, easing: 'ease-out-cubic' });

        // Clock
        function updateClock() {
            const now = new Date();
            document.getElementById('clock').textContent = now.toLocaleTimeString('id-ID', {
                hour: '2-digit', minute: '2-digit', second: '2-digit'
            });
        }
        setInterval(updateClock, 1000);
        updateClock();

        // Preview image sebelum upload
        document.querySelector('input[name="gambar"]').addEventListener('change', function (e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function (e) {
                    // Tampilkan preview kecil
                    console.log('File selected:', file.name, 'Size:', file.size, 'Type:', file.type);
                }
                reader.readAsDataURL(file);
            }
        });
    </script>
</body>
</html>