<?php
session_start();
if (!isset($_SESSION['username'])) {
    header('location:../user/login.php');
    exit();
}
include("../config.php");
$username = $_SESSION['username'];

// CEK TABEL WAJIB ADA (UNTUK DEBUG)
$required_tables = ['user', 'produk', 'pesanan', 'detail_pesanan'];
foreach ($required_tables as $table) {
    $check = $db->query("SHOW TABLES LIKE '$table'");
    if ($check->num_rows == 0) {
        die("Tabel <strong>$table</strong> TIDAK ADA! Jalankan SQL di phpMyAdmin.");
    }
}

// AMBIL DATA USER
$stmt = $db->prepare("SELECT * FROM user WHERE username = ?");
$stmt->bind_param("s", $username);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();
if (!$user)
    die("User tidak ditemukan!");

// AMBIL PRODUK (FIX: pakai produk_obat)
$produk = $db->query("SELECT * FROM produk ORDER BY tanggal DESC");

// AMBIL HISTORY (FIX: pakai prepare/bind, alias pes → pesanan)
$stmt_history = $db->prepare("
    SELECT pesanan.*, pesanan.tanggal_pesan, pesanan.status, pesanan.total_harga 
    FROM pesanan 
    JOIN user u ON pesanan.user_id = u.id 
    WHERE u.username = ? 
    ORDER BY pesanan.tanggal_pesan DESC
");
if (!$stmt_history) {
    die("Prepare gagal: " . $db->error);
}
$stmt_history->bind_param("s", $username);
$stmt_history->execute();
$history = $stmt_history->get_result();
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Apotek Digital</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap"
        rel="stylesheet">
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    <link rel="stylesheet" href="../user/beranda.css">
    <style>
        :root {
            --primary: #4CAF50;
            --light: #C8E6C9;
            --dark: #388E3C;
        }

        .profile-box {
            background: white;
            border-radius: 16px;
            padding: 1.5rem;
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
        }

        .info-row {
            display: flex;
            justify-content: space-between;
            padding: 0.75rem 0;
            border-bottom: 1px solid #eee;
        }

        .info-label {
            color: #555;
            font-weight: 600;
            font-size: 0.9rem;
        }

        .info-value {
            color: #222;
            font-weight: 500;
        }

        .modal-content {
            border-radius: 16px;
        }

        .cart-item {
            border-bottom: 1px solid #eee;
            padding: 1rem 0;
        }

        .qty-btn {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            background: var(--primary);
            color: white;
        }

        .history-table th {
            background: var(--light);
        }

        .badge-status {
            padding: 5px 12px;
            border-radius: 50px;
            font-size: 0.8rem;
        }

        .gejala-item {
            background: white;
            border-radius: 12px;
            padding: 1rem;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
            margin-bottom: 1rem;
        }
    </style>
</head>

<body>
    <div class="app-container">
        <!-- SIDEBAR -->
        <nav class="sidebar" id="sidebar">
            <div class="app-header">
                <div class="app-title">Apotik Nur Jaya</div>
                <div class="app-subtitle">Hi, <?= htmlspecialchars($username) ?></div>
            </div>
            <ul class="nav-menu">
                <li class="nav-item"><a href="#" class="nav-link" data-page="profile">👤 Profile</a></li>
                <li class="nav-item"><a href="#" class="nav-link active" data-page="home">🏠 Home</a></li>
                <li class="nav-item"><a href="#" class="nav-link cart-indicator" data-page="cart">🛒 Cart <span
                            class="cart-badge" id="cartBadge">0</span></a></li>
                <li class="nav-item"><a href="#" class="nav-link" data-page="history">⌚ History</a></li>
                <li class="nav-item"><a href="#" class="nav-link" data-page="gejala">🤒 Gejala</a></li>
                <li class="nav-item"><a href="#" class="nav-link" data-page="log out">↩️ Log out</a></li>
            </ul>
        </nav>

        <!-- MAIN CONTENT -->
        <main class="main-content">
            <div class="top-bar">
                <div class="welcome-text">Selamat Datang </div>
                <div class="datetime" id="datetime"></div>
            </div>

            <div class="content-area">

                <!-- HOME -->
                <div id="home-page" class="page-content active">
                    <div class="category-grid">
                        <a href="../user/daftar_obat.php" class="category-card text-decoration">
                            <img src="../image/obat.jpg" alt="Obat">
                            <div class="category-name">OBAT</div>
                            <div class="category-description">Obat-obatan</div>
                        </a>
                        <div class="category-card">
                            <a href="../user/daftar_vitamin.php">
                                <img src="../image/Vitamin.png" alt="Vitamin">
                                <div class="category-name">Vitamin</div>
                                <div class="category-description">Suplemen vitamin dan mineral</div>
                            </a>
                        </div>
                        <div class="category-card">
                            <a href="../user/daftar_herbal.php">
                                <img src="../image/Herbal.png" alt="Herbal">
                                <div class="category-name">Herbal</div>
                                <div class="category-description">Obat herbal tradisional dan alami</div>
                            </a>
                        </div>
                        <div class="category-card">
                            <a href="../user/daftar_susu.php">
                                <img src="../image/susu.jpeg" alt="Susu">
                                <div class="category-name">Susu</div>
                                <div class="category-description">Susu formula dan suplemen nutrisi</div>
                            </a>
                        </div>
                    </div>
                </div>
    <!-- -->
                <!-- PROFILE -->
                <div id="profile-page" class="page-content">
                    <div class="profile-box" data-aos="fade-up">
                        <h5 class="mb-4">Profil Anda</h5>
                        <div class="info-row">
                            <span class="info-label">Username :</span>
                            <span class="info-value"><?= htmlspecialchars($user['username']) ?></span>
                        </div>
                        <div class="info-row">
                            <span class="info-label">Password :</span>
                            <div class="d-flex align-items-center">
                                <span class="info-value me-2" id="passwordField">********</span>
                                <button type="button" class="btn btn-sm btn-outline-secondary p-1" id="togglePassword">
                                    <i class="bi bi-eye" id="eyeIcon"></i>
                                </button>
                            </div>
                        </div>
                        <div class="info-row">
                            <span class="info-label">No. Telepon :</span>
                            <span class="info-value"><?= htmlspecialchars($user['no_telp']) ?></span>
                        </div>
                        <div class="info-row">
                            <span class="info-label">Alamat :</span>
                            <span
                                class="info-value"><?= nl2br(htmlspecialchars($user['alamat'])) ?></span>
                        </div>
                        <div class="mt-3 text-center">
                            <a href="../user/edit.php" class="btn btn-warning">Edit Profil</a>
                        </div>
                    </div>
                </div>

                <!-- CART -->
                <div id="cart-page" class="page-content">
                    <div class="card p-4">
                        <h5>Keranjang Belanja</h5>
                        <div id="cartItems"></div>
                        <hr>
                        <div class="d-flex justify-content-between mb-3">
                            <strong>Total:</strong>
                            <strong id="totalHarga">Rp 0</strong>
                        </div>
                        <form method="POST" action="../user/checkout.php" id="checkoutForm">
                            <input type="hidden" name="cart_data" id="cartData">
                            <div class="mb-3">
                                <label class="form-label">Alamat Pengiriman</label>
                                <textarea name="alamat" class="form-control" rows="2" required></textarea>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Catatan (opsional)</label>
                                <textarea name="catatan" class="form-control" rows="2"></textarea>
                            </div>
                            <button type="submit" name="checkoutU" class="btn btn-success w-100">
                                Checkout & Pesan
                            </button>
                        </form>
                    </div>
                </div>

                <!-- HISTORY -->
                <div id="history-page" class="page-content">
                    <div class="card p-4">
                        <h5>Riwayat Pesanan</h5>
                        <div class="table-responsive">
                            <table class="table history-table">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Tanggal</th>
                                        <th>Total</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if ($history->num_rows > 0): ?>
                                        <?php $no = 1;
                                        while ($h = $history->fetch_assoc()): ?>
                                            <tr>
                                                <td><?= $no++ ?></td>
                                                <td><?= date('d/m/Y H:i', strtotime($h['tanggal_pesan'])) ?></td>
                                                <td>Rp <?= number_format($h['total_harga']) ?></td>
                                                <td>
                                                    <span
                                                        class="badge-status <?= $h['status'] == 'pending' ? 'bg-warning' : ($h['status'] == 'selesai' ? 'bg-success' : 'bg-info') ?>">
                                                        <?= ucfirst($h['status']) ?>
                                                    </span>
                                                </td>
                                            </tr>
                                        <?php endwhile; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="4" class="text-center text-muted">Belum ada pesanan.</td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- GEJALA -->
                <div id="gejala-page" class="page-content">
                    <div class="p-4">
                        <h5>Cek Gejala Anda</h5>
                        <input type="text" class="form-control mb-3" id="gejalaSearch" placeholder="Cari gejala...">
                        <div id="gejalaList"></div>
                    </div>
                </div>

            </div>
        </main>
    </div>

    <!-- JS -->
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script>
        AOS.init({ duration: 800 });

        // KERANJANG
        let cart = JSON.parse(localStorage.getItem('cart')) || [];
        function addToCart(id, nama, harga, gambar) {
            const item = cart.find(i => i.id == id);
            if (item) item.qty += 1;
            else cart.push({ id, nama, harga, qty: 1, gambar });
            localStorage.setItem('cart', JSON.stringify(cart));
            updateBadge();
            alert(nama + " ditambahkan!");
        }
        function updateBadge() {
            const total = cart.reduce((s, i) => s + i.qty, 0);
            document.getElementById('cartBadge').textContent = total;
        }
        updateBadge();

        // FUNGSI showPage — DIPINDAHKAN KE ATAS!
        function showPage(page) {
            document.querySelectorAll('.page-content').forEach(p => p.classList.remove('active'));
            document.querySelectorAll('.nav-link').forEach(n => n.classList.remove('active'));
            const target = document.getElementById(page + '-page');
            if (target) target.classList.add('active');
            const link = document.querySelector(`[data-page="${page}"]`);
            if (link) link.classList.add('active');
        }

        // NAVIGASI UTAMA — SEMUA HALAMAN DIKENDALIKAN DI SINI
        document.querySelectorAll('.nav-link').forEach(l => {
            l.addEventListener('click', e => {
                const page = l.getAttribute('data-page');

                // LOGOUT → LANGSUNG KE logout.php
                if (page === 'log out') {
                    e.preventDefault();
                    window.location.href = '../logout.php';
                    return;
                }

                e.preventDefault();
                showPage(page);

                // KHUSUS CART → RENDER KERANJANG
                if (page === 'cart') {
                    const container = document.getElementById('cartItems');
                    let html = '', total = 0;
                    if (cart.length === 0) {
                        html = '<p class="text-center text-muted">Keranjang kosong.</p>';
                    } else {
                        cart.forEach((item, i) => {
                            total += item.harga * item.qty;
                            html += `
                        <div class="cart-item d-flex align-items-center">
                            <img src="../image/produk/${item.gambar}" width="50" class="me-3" onerror="this.src='../image/default.jpg'">
                            <div class="flex-grow-1">
                                <div class="fw-bold">${item.nama}</div>
                                <small>Rp ${item.harga.toLocaleString()}</small>
                            </div>
                            <div class="d-flex align-items-center">
                                <button class="btn qty-btn" onclick="updateQty(${i},-1)">-</button>
                                <span class="mx-2">${item.qty}</span>
                                <button class="btn qty-btn" onclick="updateQty(${i},1)">+</button>
                            </div>
                            <button class="btn text-danger ms-2" onclick="removeItem(${i})">Remove</button>
                        </div>`;
                        });
                    }
                    container.innerHTML = html;
                    document.getElementById('totalHarga').textContent = 'Rp ' + total.toLocaleString();
                    document.getElementById('cartData').value = JSON.stringify(cart);
                }

                // LIHAT / SEMBUNYIKAN PASSWORD
                const passwordField = document.getElementById('passwordField');
                const toggleButton = document.getElementById('togglePassword');
                const eyeIcon = document.getElementById('eyeIcon');
                const realPassword = "<?= addslashes($user['password']) ?>"; // Password asli (hashed, tapi user tahu)

                let isPasswordVisible = false;

                toggleButton.addEventListener('click', () => {
                    if (isPasswordVisible) {
                        passwordField.textContent = '********';
                        eyeIcon.classList.replace('bi-eye-slash', 'bi-eye');
                    } else {
                        passwordField.textContent = realPassword;
                        eyeIcon.classList.replace('bi-eye', 'bi-eye-slash');
                    }
                    isPasswordVisible = !isPasswordVisible;
                });

                // KHUSUS GEJALA → RENDER DATA
                if (page === 'gejala') {
                    renderGejala();
                }
            });
        });

        function updateQty(i, c) {
            cart[i].qty = Math.max(1, cart[i].qty + c);
            localStorage.setItem('cart', JSON.stringify(cart));
            location.reload();
        }
        function removeItem(i) {
            cart.splice(i, 1);
            localStorage.setItem('cart', JSON.stringify(cart));
            location.reload();
        }

        // GEJALA — TOTAL 22 GEJALA! (Obat, Vitamin, Herbal, Susu)
        const daftarGejala = [
            { nama: "Batuk", deskripsi: "Batuk kering atau berdahak", rekomendasi: "Obat batuk", kategori: "Obat" },
            { nama: "Demam", deskripsi: "Suhu tubuh > 38°C", rekomendasi: "Paracetamol", kategori: "Obat" },
            { nama: "Sakit Kepala", deskripsi: "Nyeri di kepala", rekomendasi: "Ibuprofen", kategori: "Obat" },
            { nama: "Mual", deskripsi: "Ingin muntah", rekomendasi: "Antasida", kategori: "Obat" },
            { nama: "Pilek", deskripsi: "Hidung meler atau tersumbat", rekomendasi: "Dekongestan", kategori: "Obat" },
            { nama: "Diare", deskripsi: "Buang air besar >3x/hari", rekomendasi: "Oralit", kategori: "Obat" },
            { nama: "Sakit Tenggorokan", deskripsi: "Nyeri saat menelan", rekomendasi: "Permen pelega tenggorokan", kategori: "Obat" },
            { nama: "Nyeri Otot", deskripsi: "Otot terasa kaku atau pegal", rekomendasi: "Relaksan otot", kategori: "Obat" },
            { nama: "Pusing", deskripsi: "Kepala terasa berputar", rekomendasi: "Obat anti pusing", kategori: "Obat" },
            { nama: "Sariawan", deskripsi: "Luka kecil di mulut", rekomendasi: "Obat sariawan", kategori: "Obat" },
            { nama: "Gatal-Gatal", deskripsi: "Kulit gatal tanpa sebab jelas", rekomendasi: "Antihistamin", kategori: "Obat" },
            { nama: "Sesak Napas", deskripsi: "Sulit bernapas", rekomendasi: "Inhaler / Konsultasi dokter", kategori: "Obat" },
            { nama: "Sakit Perut", deskripsi: "Nyeri di area perut", rekomendasi: "Obat maag", kategori: "Obat" },
            { nama: "Jerawat", deskripsi: "Benjolan merah di wajah", rekomendasi: "Krim jerawat", kategori: "Obat" },
            { nama: "Lemas", deskripsi: "Tubuh terasa lemah & tidak bertenaga", rekomendasi: "Vitamin B kompleks", kategori: "Vitamin" },
            { nama: "Insomnia", deskripsi: "Sulit tidur malam", rekomendasi: "Suplemen melatonin", kategori: "Vitamin" },
            { nama: "Masuk Angin", deskripsi: "Perut kembung, mual, pegal", rekomendasi: "Tolak Angin", kategori: "Herbal" },
            { nama: "Batuk Berdahak", deskripsi: "Batuk dengan lendir", rekomendasi: "HerbaKof", kategori: "Herbal" },
            { nama: "Nyeri Haid", deskripsi: "Sakit perut saat menstruasi", rekomendasi: "Kunyit Asam", kategori: "Herbal" },
            { nama: "Kurang Gizi Anak", deskripsi: "Anak kurus, susah makan", rekomendasi: "Susu penambah berat badan", kategori: "Susu" },
            { nama: "Tulang Rapuh", deskripsi: "Mudah patah tulang", rekomendasi: "Susu kalsium tinggi", kategori: "Susu" },
            { nama: "Kurang Energi", deskripsi: "Cepat lelah saat beraktivitas", rekomendasi: "Susu energi", kategori: "Susu" }
        ];

        function renderGejala(data = daftarGejala) {
            const list = document.getElementById('gejalaList');
            list.innerHTML = '';
            if (data.length === 0) {
                list.innerHTML = '<p class="text-center text-muted">Tidak ada gejala ditemukan.</p>';
                return;
            }
            data.forEach(g => {
                list.innerHTML += `
            <div class="gejala-item">
                <h6 class="fw-bold">${g.nama}</h6>
                <p class="text-muted small">${g.deskripsi}</p>
                <div><strong>Rekomendasi:</strong> ${g.rekomendasi} <span class="badge bg-success">${g.kategori}</span></div>
            </div>`;
            });
        }

        // PENCARIAN GEJALA
        const searchInput = document.getElementById('gejalaSearch');
        if (searchInput) {
            searchInput.addEventListener('input', function () {
                const q = this.value.toLowerCase();
                const filtered = daftarGejala.filter(g =>
                    g.nama.toLowerCase().includes(q) ||
                    g.deskripsi.toLowerCase().includes(q)
                );
                renderGejala(filtered);
            });
        }

        // WAKTU
        function updateTime() {
            const now = new Date();
            const options = {
                weekday: 'long',
                year: 'numeric',
                month: 'long',
                day: 'numeric',
                hour: '2-digit',
                minute: '2-digit',
                second: '2-digit'
            };
            document.getElementById('datetime').textContent = now.toLocaleDateString('id-ID', options);
        }
        setInterval(updateTime, 1000);
        updateTime();
    </script>
</body>

</html>