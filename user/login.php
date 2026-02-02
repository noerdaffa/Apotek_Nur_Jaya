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
                        <div class="alert alert-danger">
                            Username atau password salah!
                        </div>
                    <?php endif; ?>

                    <?php if (isset($_GET['daftarU'])): ?>
                        <div class="alert alert-success">
                            Pendaftaran berhasil! Silakan login.
                        </div>
                    <?php endif; ?>

                    <form action="../user/proses_login.php" method="post">
                        <div class="mb-3">
                            <label class="form-label">Username</label>
                            <input type="text" name="username" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Password</label>
                            <input type="password" name="password" class="form-control" required>
                        </div>
                        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                            <button type="submit" class="btn btn-primary me-md-2">Login</button>
                            <button type="reset" class="btn btn-secondary">Batal</button>
                        </div>
                    </form>

                    <div class="text-center mt-3">
                        <small>Belum punya akun? <a href="daftar.php"><b>Daftar</b></a></small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>