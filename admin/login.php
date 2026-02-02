<?php session_start();
require_once("../config.php");
?>
<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | Apotek Nur Jaya</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../user/login.css">
</head>
    <div class="container">
        <div class="row justify-content-center mt-5">
            <div class="col-md-4">
                <div class="card shadow-sm">
                    <div class="card-body p-4">
                        <h3 class="text-center mb-4">Masuk Akun Anda</h3>

                        <?php if (isset($_GET['error'])): ?>
                            <div class="alert alert-danger alert-dismissible fade show" role="alert"
                                style="border-radius: 12px; font-size: 14px;">
                                <strong>
                                    <?php
                                    switch ($_GET['error']) {
                                        case 'empty':
                                            echo 'STRA dan Password harus diisi!';
                                            break;
                                        case 'notfound':
                                            echo 'STRA belum terdaftar!';
                                            break;
                                        case 'wrongpass':
                                            echo 'Password salah!';
                                            break;
                                        case 'system':
                                            echo 'Terjadi kesalahan sistem. Coba lagi nanti.';
                                            break;
                                        default:
                                            echo 'Login gagal. Periksa STRA dan password Anda.';
                                    }
                                    ?>
                                </strong>
                                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert"
                                    aria-label="Close"></button>
                            </div>
                        <?php endif; ?>

                        <?php if (isset($_GET['daftarA'])): ?>
                            <div class="alert alert-success alert-dismissible fade show" role="alert"
                                style="border-radius: 12px;">
                                <i class="fas fa-check-circle"></i> Pendaftaran berhasil! Silakan login.
                                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert"></button>
                            </div>
                        <?php endif; ?>

                        <form action="../admin/proseslogin.php" method="post">
                            <div class="mb-3">
                                <label class="form-label">STRA</label>
                                <input type="number" name="STRA" class="form-control" required autocomplete="off">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Password</label>
                                <input type="password" name="password" class="form-control" required autocomplete="off">
                            </div>
                            <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                                <button type="submit" class="btn btn-primary me-md-2">Login</button>
                                <button type="reset" class="btn btn-secondary">Batal</button>
                            </div>
                        </form>

                        <div class="text-center mt-3">
                            <small>Belum punya akun? <a href="../admin/registrasi.php"><b>Daftar</b></a></small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>