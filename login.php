<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - SPMB SMK ArdsentrA</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #eef1f5;
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            padding: 20px;
        }
        .login-card {
            width: 100%;
            max-width: 450px;
            border: none;
            border-radius: 0.75rem;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
        }
        .login-card .card-header {
            background-color: #fd7e14;
            color: white;
            text-align: center;
            padding: 1.5rem;
            border-top-left-radius: 0.75rem;
            border-top-right-radius: 0.75rem;
        }
        .btn-login-custom {
            background-color: #fd7e14;
            border-color: #fd7e14;
            color: white;
            transition: all 0.3s ease;
        }
        .btn-login-custom:hover {
            background-color: #e86100;
            border-color: #dd5a00;
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(253, 126, 20, 0.4);
        }
        .footer-link a {
            text-decoration: none;
            color: #fd7e14;
            font-weight: 500;
        }
        .footer-link a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="login-card card">
        <div class="card-header">
            <div class="icon-wrapper">
                <i class="bi bi-box-arrow-in-right" style="font-size: 3rem;"></i>
            </div>
            <h3>Login Pengguna</h3>
            <p class="mb-0 small">Admin & Pendaftar - SPMB SMK ArdsentrA</p>
        </div>
        <div class="card-body">
            <?php
                if (isset($_GET['error'])) {
                    echo '<div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="bi bi-exclamation-triangle-fill me-2"></i>' . htmlspecialchars(urldecode($_GET['error'])) . '
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                          </div>';
                }
                if (isset($_GET['message'])) {
                     echo '<div class="alert alert-success alert-dismissible fade show" role="alert">
                            <i class="bi bi-check-circle-fill me-2"></i>' . htmlspecialchars(urldecode($_GET['message'])) . '
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                          </div>';
                }
            ?>
            <form action="login_auth.php" method="POST">
                <div class="mb-3">
                    <label for="username" class="form-label fw-semibold">Username</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-person-fill"></i></span>
                        <input type="text" class="form-control form-control-lg" id="username" name="username" placeholder="Masukkan username (NISN/Username)" required autofocus>
                    </div>
                </div>
                <div class="mb-4">
                    <label for="password" class="form-label fw-semibold">Password</label>
                    <div class="input-group">
                         <span class="input-group-text"><i class="bi bi-key-fill"></i></span>
                        <input type="password" class="form-control form-control-lg" id="password" name="password" placeholder="Masukkan password/tanggal lahir anda" required>
                    </div>
                </div>
                <div class="d-grid">
                    <button type="submit" class="btn btn-login-custom btn-lg fw-bold py-2">
                        <i class="bi bi-box-arrow-in-right me-2"></i>Login
                    </button>
                </div>
            </form>
        </div>
        <div class="card-footer text-center bg-light py-3 footer-link">
            <a href="index.php"><i class="bi bi-arrow-left-circle me-1"></i>Kembali ke Halaman Utama</a>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
