<?php
include("../config.php");
session_start();

// Cek login
if (!isset($_SESSION['username'])) {
    header("Location:../user/login.php");
    exit();
}

$username_lama = $_SESSION['username'];

// Ambil data user
$stmt = $db->prepare("SELECT * FROM user WHERE username = ?");
$stmt->bind_param("s", $username_lama);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    die("User tidak ditemukan.");
}

$user = $result->fetch_assoc();
$stmt->close();

$user_id = $user['id']; // Pastikan kolom id ada

// Proses update
$alert = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama_baru = trim($_POST['nama'] ?? '');
    $no_telp   = trim($_POST['no_telp'] ?? '');
    $alamat    = trim($_POST['alamat'] ?? '');
    $password  = $_POST['password'] ?? '';

    if (empty($nama_baru) || empty($no_telp) || empty($alamat)) {
        $alert = '<div class="alert alert-danger">Semua field wajib diisi!</div>';
    } else {
        // TIDAK PAKAI password_hash() → SIMPAN LANGSUNG
        $password_baru = !empty($password) ? $password : $user['password'];

        $sql = "UPDATE user SET 
                username = ?, 
                no_telp = ?, 
                alamat = ?, 
                password = ? 
                WHERE id = ?";

        $stmt = $db->prepare($sql);
        if ($stmt === false) {
            $alert = '<div class="alert alert-danger">Query error: ' . htmlspecialchars($db->error) . '</div>';
        } else {
            $stmt->bind_param("ssssi", $nama_baru, $no_telp, $alamat, $password_baru, $user_id);

            if ($stmt->execute()) {
                if ($stmt->affected_rows > 0 || !empty($password) || $nama_baru !== $user['username']) {
                    $alert = '<div class="alert alert-success alert-dismissible fade show" role="alert">
                                <i class="bi bi-check-circle"></i> 
                                <strong>Berhasil!</strong> Profil Anda telah diperbarui.
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                              </div>';

                    if ($nama_baru !== $user['username']) {
                        $_SESSION['username'] = $nama_baru;
                    }

                    $user['username'] = $nama_baru;
                    $user['no_telp']   = $no_telp;
                    $user['alamat']    = $alamat;
                    $user['password']  = $password_baru; // Update password di memori
                } else {
                    $alert = '<div class="alert alert-info">Tidak ada perubahan data.</div>';
                }
            } else {
                $alert = '<div class="alert alert-danger">Gagal update: ' . htmlspecialchars($stmt->error) . '</div>';
            }
            $stmt->close();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Profil - Apotek Nur Jaya</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    <link rel="stylesheet" href="edit.css">
</head>
<body class="bg-light">

    <div id="loading-overlay" style="position:fixed;top:0;left:0;width:100%;height:100%;background:rgba(226,22,141,0.95);backdrop-filter:blur(10px);display:flex;align-items:center;justify-content:center;z-index:9999;opacity:1;visibility:visible;transition:all 0.6s ease;">
        <div style="text-align:center;animation:pulse 1.5s ease-in-out infinite;">
            <div style="width:70px;height:70px;border:6px solid #e0e7ff;border-top:6px solid #e2168d;border-radius:50%;animation:spin 1s linear infinite;margin:0 auto 1.5rem;"></div>
            <div style="font-weight:600;color:#e2168d;font-size:1.1rem;letter-spacing:1px;">
                <span style="display:inline-block;animation:bounce 1.4s ease-in-out infinite;">M</span>
                <span style="display:inline-block;animation:bounce 1.4s ease-in-out infinite;animation-delay:0.1s;">e</span>
                <span style="display:inline-block;animation:bounce 1.4s ease-in-out infinite;animation-delay:0.2s;">m</span>
                <span style="display:inline-block;animation:bounce 1.4s ease-in-out infinite;animation-delay:0.3s;">u</span>
                <span style="display:inline-block;animation:bounce 1.4s ease-in-out infinite;animation-delay:0.4s;">a</span>
                <span style="display:inline-block;animation:bounce 1.4s ease-in-out infinite;animation-delay:0.5s;">t</span>
                <span style="display:inline-block;animation:bounce 1.4s ease-in-out infinite;animation-delay:0.7s;"> F</span>
                <span style="display:inline-block;animation:bounce 1.4s ease-in-out infinite;animation-delay:0.8s;">o</span>
                <span style="display:inline-block;animation:bounce 1.4s ease-in-out infinite;animation-delay:0.9s;">r</span>
                <span style="display:inline-block;animation:bounce 1.4s ease-in-out infinite;animation-delay:1s;">m</span>
                <span style="display:inline-block;animation:bounce 1.4s ease-in-out infinite;animation-delay:1.1s;">.</span>
                <span style="display:inline-block;animation:bounce 1.4s ease-in-out infinite;animation-delay:1.2s;">.</span>
                <span style="display:inline-block;animation:bounce 1.4s ease-in-out infinite;animation-delay:1.3s;">.</span>
            </div>
        </div>
    </div>

    <div class="container my-5">
        <div class="row justify-content-center">
            <div class="col-lg-8" data-aos="fade-up">
                <div class="card shadow-sm border-0" data-aos="zoom-in" data-aos-delay="200">
                    <div class="card-header bg-primary text-white text-center py-4">
                        <div class="avatar-large mx-auto mb-3">
                            <i class="bi bi-person"></i>
                        </div>
                        <h3 class="mb-0">Edit Profil</h3>
                        <p class="mb-0 opacity-75">Perbarui informasi akun Anda</p>
                    </div>

                    <div class="card-body p-4 p-md-5">
                        <?= $alert ?>

                        <form method="POST" action="">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label">Nama Lengkap</label>
                                    <div class="input-group">
                                        <span class="input-group-text">Person</span>
                                        <input type="text" name="nama" class="form-control"
                                               value="<?= htmlspecialchars($user['username']) ?>" required>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">No. Telepon</label>
                                    <div class="input-group">
                                        <span class="input-group-text">Phone</span>
                                        <input type="text" name="no_telp" class="form-control"
                                               value="<?= htmlspecialchars($user['no_telp']) ?>"
                                               pattern="[0-9]{10,13}" title="10-13 digit angka" required>
                                    </div>
                                    <small class="text-muted">Contoh: 081234567890</small>
                                </div>

                                <div class="col-12">
                                    <label class="form-label">Alamat Lengkap</label>
                                    <textarea name="alamat" class="form-control" rows="3" required
                                              placeholder="Masukkan alamat lengkap"><?= htmlspecialchars($user['alamat']) ?></textarea>
                                </div>

                                <div class="col-12">
                                    <label class="form-label">Password Baru 
                                        <small class="text-muted">(kosongkan jika tidak ingin ganti)</small>
                                    </label>
                                    <div class="input-group">
                                        <span class="input-group-text">Lock</span>
                                        <input type="password" name="password" id="password" class="form-control"
                                               placeholder="Minimal 6 karakter" minlength="6">
                                        <span class="input-group-text password-toggle" onclick="togglePass()">
                                            <i class="bi bi-eye" id="eyeIcon"></i>
                                        </span>
                                    </div>
                                </div>
                            </div>

                            <div class="d-flex justify-content-center gap-3 mt-4">
                                <button type="submit" class="btn btn-primary px-4">
                                    Simpan Perubahan
                                </button>
                                <a href="../user/beranda.php" id="profile-page" class="btn btn-secondary px-4">
                                    <div id="profile-page" class="page-content"></div>
                                    Kembali
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script>
        AOS.init({ once: true, duration: 800, easing: 'ease-out-cubic' });

        window.addEventListener('load', () => {
            const overlay = document.getElementById('loading-overlay');
            setTimeout(() => {
                overlay.style.opacity = '0';
                overlay.style.visibility = 'hidden';
            }, 800);
        });

        function togglePass() {
            const pass = document.getElementById('password');
            const icon = document.getElementById('eyeIcon');
            if (pass.type === 'password') {
                pass.type = 'text';
                icon.classList.replace('bi-eye', 'bi-eye-slash');
            } else {
                pass.type = 'password';
                icon.classList.replace('bi-eye-slash', 'bi-eye');
            }
        }

        const style = document.createElement('style');
        style.textContent = `
            @keyframes spin { to { transform: rotate(360deg); } }
            @keyframes pulse { 50% { transform: scale(1.05); } }
            @keyframes bounce { 50% { transform: translateY(-10px); } }
        `;
        document.head.appendChild(style);
    </script>
</body>
</html>