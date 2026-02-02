<?php
session_start();
include("../config.php");

// GANTI KATEGORI DI SINI!
$kategori = 'vitamin'; // ← GANTI: 'obat', 'vitamin', 'herbal', 'susu'
$nama_halaman = 'Vitamin'; // ← GANTI: Obat, Vitamin, Herbal, Susu

// FIX: PAKAI VARIABEL LANGSUNG (TANPA BIND)
$produk_result = $db->query("SELECT * FROM produk WHERE kategori = '$kategori' ORDER BY tanggal DESC");
if (!$produk_result) {
    die("Query gagal: " . $db->error);
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar <?= ucfirst($nama_halaman) ?> - Apotek NUR JAYA</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap"
        rel="stylesheet">
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    <link rel="stylesheet" href="beranda.css">
    <style>
        :root {
            --primary: #4CAF50;
            --light: #C8E6C9;
            --dark: #388E3C;
            --radius: 18px;
            --shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
        }

        body {
            background: #11ff04ff;
            font-family: 'Poppins', sans-serif;
        }

        .page-header {
            background: white;
            padding: 1.5rem 2rem;
            border-radius: var(--radius);
            box-shadow: var(--shadow);
            margin-bottom: 2rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .page-title {
            font-weight: 700;
            color: var(--dark);
            margin: 0;
            font-size: 1.8rem;
        }

        .back-btn {
            background: var(--light);
            color: var(--dark);
            border: none;
            border-radius: 50px;
            padding: 10px 20px;
            font-weight: 600;
            transition: all 0.3s;
        }

        .back-btn:hover {
            background: var(--primary);
            color: white;
        }

        .search-box {
            position: relative;
            margin-bottom: 1.5rem;
        }

        .search-box input {
            padding-left: 45px;
            border-radius: 50px;
            border: 2px solid #e0e0e0;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
            font-size: 1rem;
        }

        .search-box i {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: #999;
        }

        .product-card {
            background: white;
            border-radius: var(--radius);
            overflow: hidden;
            box-shadow: var(--shadow);
            transition: all 0.3s ease;
            height: 100%;
            display: flex;
            flex-direction: column;
        }

        .product-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.12);
        }

        .product-img {
            height: 180px;
            object-fit: cover;
            border-bottom: 1px solid #f0f0f0;
        }

        .product-body {
            padding: 1.25rem;
            flex-grow: 1;
            display: flex;
            flex-direction: column;
        }

        .product-name {
            font-weight: 600;
            color: #222;
            margin-bottom: 0.5rem;
            font-size: 1.1rem;
        }

        .product-desc {
            color: #666;
            font-size: 0.9rem;
            margin-bottom: 0.75rem;
            flex-grow: 1;
        }

        .product-price {
            font-weight: 700;
            color: var(--primary);
            font-size: 1.2rem;
            margin-bottom: 1rem;
        }

        .btn-add-cart {
            background: var(--primary);
            color: white;
            border: none;
            border-radius: 50px;
            padding: 10px 20px;
            font-weight: 600;
            font-size: 0.95rem;
            transition: all 0.3s;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }

        .btn-add-cart:hover {
            background: var(--dark);
            transform: scale(1.03);
        }

        .no-results {
            text-align: center;
            padding: 3rem;
            color: #999;
        }
    </style>
</head>

<body>

    <div class="container py-4">
        <!-- HEADER -->
        <div class="page-header" data-aos="fade-down">
            <h1 class="page-title">
                Selamat Berbelanja Vitamin
            </h1>
            <div class="search-box" data-aos="fade-up" data-aos-delay="100">
                <i class="bi bi-search"></i>
                <input type="text" class="form-control" id="searchInput" placeholder="Cari obat…">
            </div>
             <button class="back-btn" onclick="history.back()">
                Kembali
            </button>
        </div>

        <!-- GRID PRODUK -->
        <div class="row g-4" id="produkGrid">
            <?php if ($produk_result->num_rows > 0): ?>
                <?php while ($p = $produk_result->fetch_assoc()): ?>
                    <div class="col-md-6 col-lg-4 mb-4 produk-item" data-nama="<?= strtolower($p['nama_produk']) ?>">
                        <div class="product-card" data-aos="fade-up" data-aos-delay="150">
                            <img src="../image/produk<?= htmlspecialchars($p['gambar']) ?>" class="product-img w-100"
                                alt="<?= htmlspecialchars($p['nama_produk']) ?>">
                            <div class="product-body">
                                <h6 class="product-name"><?= htmlspecialchars($p['nama_produk']) ?></h6>
                                <p class="product-desc">
                                    <?= htmlspecialchars($p['deskripsi'] ?: ucfirst($kategori) . ' berkualitas') ?></p>
                                <div class="product-price">Rp <?= number_format($p['harga']) ?></div>
                                <button class="btn-add-cart w-100"
                                    onclick="addToCart(<?= $p['id'] ?>, '<?= addslashes($p['nama_produk']) ?>', <?= $p['harga'] ?>, '<?= $p['gambar'] ?>')">
                                    Tambah ke Keranjang
                                </button>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <div class="col-12">
                    <div class="no-results" data-aos="fade-up">
                        <i class="bi bi-inbox display-1 text-muted"></i>
                        <p class="mt-3">Belum ada <?= strtolower($nama_halaman) ?> tersedia.</p>
                    </div>
                </div>
            <?php endif; ?>
        </div>

    </div>

    <!-- JS -->
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script>
        AOS.init({ duration: 600, easing: 'ease-out-quart' });

        // KERANJANG
        let cart = JSON.parse(localStorage.getItem('cart')) || [];
        function addToCart(id, nama, harga, gambar) {
            const item = cart.find(i => i.id == id);
            if (item) item.qty += 1;
            else cart.push({ id, nama, harga, qty: 1, gambar });
            localStorage.setItem('cart', JSON.stringify(cart));
            alert(nama + " ditambahkan ke keranjang!");
            updateBadge();
        }
        function updateBadge() {
            const total = cart.reduce((s, i) => s + i.qty, 0);
            const badge = parent.document.getElementById('cartBadge');
            if (badge) badge.textContent = total;
        }
        updateBadge();

        // PENCARIAN
        document.getElementById('searchInput').addEventListener('input', function () {
            const query = this.value.toLowerCase();
            document.querySelectorAll('.produk-item').forEach(item => {
                const nama = item.getAttribute('data-nama');
                item.style.display = nama.includes(query) ? 'flex' : 'none';
            });
        });
    </script>
</body>

</html>