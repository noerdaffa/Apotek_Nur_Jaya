<?php
session_start();
include("../config.php");

// Ambil hanya produk kategori "obat"
$stmt = $db->prepare("SELECT * FROM produk WHERE kategori = 'obat' ORDER BY tanggal DESC");
$stmt->execute();
$obat_result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Obat - Apotek NUR JAYA</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap"
        rel="stylesheet">
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
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

        .pagination {
            margin-top: 2rem;
            justify-content: center;
        }

        .pagination .page-link {
            border-radius: 50px;
            margin: 0 4px;
            color: var(--dark);
            border: 1px solid #ddd;
        }

        .pagination .page-item.active .page-link {
            background: var(--primary);
            border-color: var(--primary);
            color: white;
        }
    </style>
</head>

<body>

    <div class="container py-4">

        <!-- HEADER -->
        <div class="page-header" data-aos="fade-down">
            <h1 class="page-title">
                Selamat Berbelanja Obat
            </h1>
            <div class="search-box" data-aos="fade-up" data-aos-delay="100">
                <i class="bi bi-search"></i>
                <input type="text" class="form-control" id="searchInput" placeholder="Cari obat…">
            </div>
             <button class="back-btn" onclick="history.back()">
                Kembali
            </button>
        </div>


        <!-- GRID OBAT -->
        <div class="row g-4" id="obatGrid">
            <?php if ($obat_result->num_rows > 0): ?>
                <?php while ($obat = $obat_result->fetch_assoc()): ?>
                    <div class="col-md-6 col-lg-4 mb-4 obat-item" data-nama="<?= strtolower($obat['nama_produk']) ?>">
                        <div class="product-card" data-aos="fade-up" data-aos-delay="150">
                            <img src="<?= htmlspecialchars($obat['gambar']) ?>" class="product-img w-100"
                                alt="<?= htmlspecialchars($obat['nama_produk']) ?>">
                            <div class="product-body">
                                <h6 class="product-name"><?= htmlspecialchars($obat['nama_produk']) ?></h6>
                                <p class="product-desc">
                                    <?= htmlspecialchars($obat['deskripsi'] ?: 'Obat sesuai resep dokter') ?>
                                </p>
                                <div class="product-price">Rp <?= number_format($obat['harga']) ?></div>
                                <button class="btn-add-cart w-100"
                                    onclick="addToCart(<?= $obat['id'] ?>, '<?= addslashes($obat['nama_produk']) ?>', <?= $obat['harga'] ?>, '<?= $obat['gambar'] ?>')">
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
                        <p class="mt-3">Belum ada obat tersedia.</p>
                    </div>
                </div>
            <?php endif; ?>
        </div>

    </div>

    <!-- AOS & JS -->
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script>
        AOS.init({ duration: 600, easing: 'ease-out-quart' });

        // KERANJANG (SAMA SEPERTI BERANDA)
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

        // PENCARIAN REAL-TIME
        document.getElementById('searchInput').addEventListener('input', function () {
            const query = this.value.toLowerCase();
            document.querySelectorAll('.obat-item').forEach(item => {
                const nama = item.getAttribute('data-nama');
                item.style.display = nama.includes(query) ? 'flex' : 'none';
            });
        });
    </script>
</body>

</html>