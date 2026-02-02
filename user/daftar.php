<?php include('../config.php');
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Form Pendaftaran</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <link rel="stylesheet" href="../user/daftar.css">
</head>
<body>
<div class="container">
    <div class="card">
        <div class="card-header">
            <h4 class="mb-0">Daftar Akun Baru</h4>
        </div>
        <div class="card-body p-4">
            <form action="../user/prosesdaftar.php" method="post" novalidate>
                <table>
                    <tbody>
                        <tr>
                            <td>Username <span class="text-danger"></span></td>
                            <td>
                                <input type="text" class="form-control" name="username" placeholder="Masukkan username" required>
                            </td>
                        </tr>
                        <tr>
                            <td>Password <span class="text-danger"></span></td>
                            <td>
                                <input type="password" class="form-control" name="password" placeholder="Buat password aman" required minlength="6">
                            </td>
                        </tr>
                        <tr>
                            <td>No. Telepon <span class="text-danger"></span></td>
                            <td>
                                <input type="number" class="form-control" name="no_telp" placeholder="Contoh: 081234567890" required>
                                <div class="form-text text-muted small">12 digit angka</div>
                            </td>
                        </tr>
                        <tr>
                            <td>Alamat <span class="text-danger">   </span></td>
                            <td>
                                <textarea class="form-control" name="alamat" rows="2" placeholder="Masukkan alamat lengkap" required></textarea>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="2" class="text-end">
                                <div class="btn-group">
                                    <button type="submit" name="daftarU" class="btn btn-success">Daftar</button>
                                    <button type="reset" class="btn btn-secondary">Batal</button>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="2" class="text-center pt-3">
                                <small class="text-muted"> Sudah punya akun? <a href="../user/login.php" class="text-link">Login </a>
                                </small>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </form>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
<script>
    // Validasi Bootstrap
    (() => {
        'use strict'
        const forms = document.querySelectorAll('form')
        Array.from(forms).forEach(form => {
            form.addEventListener('submit', event => {
                if (!form.checkValidity()) {
                    event.preventDefault()
                    event.stopPropagation()
                }
                form.classList.add('was-validated')
            }, false)
        })
    })()
</script>
</body>
</html>