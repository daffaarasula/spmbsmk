<?php
session_start();
if (!isset($_SESSION['id_user']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}
include 'db_config.php';
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Admin - SPMB SMK ArdsentrA</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
            font-family: 'Poppins', sans-serif;
        }

        .form-container {
            max-width: 700px;
            margin: 50px auto;
        }

        .table-responsive {
            overflow-x: auto;
        }

        .card-header {
            font-weight: 600;
        }

        footer {
            position: fixed;
            bottom: 0;
            width: 100%;
        }
    </style>
</head>

<body>
    <nav class="navbar navbar-dark bg-dark shadow-sm">
        <div class="container-fluid">
            <a class="navbar-brand fw-bold" href="admin_dashboard.php">
                <i class="bi bi-shield-lock-fill me-2"></i>Admin SPMB
            </a>
            <span class="text-white">Hai, Admin  <?php echo htmlspecialchars($_SESSION['username']); ?></span>
            <a class="btn btn-outline-light btn-sm ms-3" href="admin_logout.php">Logout</a>
        </div>
    </nav>

    <div class="container form-container">
        <div class="card shadow">
            <div class="card-header bg-primary text-white">
                <h4 class="mb-0"><i class="bi bi-person-plus-fill me-2"></i>Kelola Admin</h4>
            </div>
            <div class="card-body">
                <!-- Notifikasi -->
                <?php
                if (isset($_SESSION['success'])) {
                    echo '<div class="alert alert-success alert-dismissible fade show">
                            <i class="bi bi-check-circle-fill me-2"></i>'
                            . htmlspecialchars($_SESSION['success']) .
                            '<button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                          </div>';
                    unset($_SESSION['success']);
                }

                if (isset($_SESSION['error'])) {
                    echo '<div class="alert alert-danger alert-dismissible fade show">
                            <i class="bi bi-exclamation-triangle-fill me-2"></i>'
                            . htmlspecialchars($_SESSION['error']) .
                            '<button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                          </div>';
                    unset($_SESSION['error']);
                }
                ?>
                <!-- Form Tambah Admin -->
                <form action="admin_add_user.php" method="POST" class="needs-validation" novalidate>
                    <div class="row g-3">
                        <div class="col-md-12">
                            <label for="new_username" class="form-label">Username <span class="text-danger">*</span></label>
                            <input type="text" name="new_username" id="new_username" class="form-control" minlength="5" required>
                            <div class="invalid-feedback">Minimal 5 karakter.</div>
                        </div>

                        <div class="col-md-6">
                            <label for="new_password" class="form-label">Password <span class="text-danger">*</span></label>
                            <input type="password" name="new_password" id="new_password" class="form-control" minlength="8" required>
                            <div class="form-text small">Gunakan kombinasi huruf, angka, simbol.</div>
                            <div class="invalid-feedback">Minimal 8 karakter.</div>
                        </div>

                        <div class="col-md-6">
                            <label for="confirm_password" class="form-label">Konfirmasi Password <span class="text-danger">*</span></label>
                            <input type="password" name="confirm_password" id="confirm_password" class="form-control" required>
                            <div class="invalid-feedback">Harus sama dengan password.</div>
                        </div>

                        <div class="col-md-12">
                            <label for="status_user" class="form-label">Status</label>
                            <select name="status_user" id="status_user" class="form-select" required>
                                <option value="aktif" selected>Aktif</option>
                                <option value="tidak aktif">Tidak Aktif</option>
                            </select>
                        </div>

                        <div class="col-12">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-person-plus-fill me-1"></i> Tambah Admin
                            </button>
                        </div>
                    </div>
                </form>

                <!-- Daftar Admin -->
                <hr class="my-4">
                <h5 class="mb-3"><i class="bi bi-people-fill me-1"></i>Daftar Admin</h5>
                <div class="table-responsive">
                    <table class="table table-striped table-bordered table-hover">
                        <thead class="table-dark">
                            <tr>
                                <th width="10%">ID</th>
                                <th>Username</th>
                                <th width="15%">Status</th>
                                <th width="15%">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $result = mysqli_query($conn, "SELECT * FROM user WHERE role = 'admin' ORDER BY id_user ASC");
                            if (mysqli_num_rows($result) > 0) {
                                while ($row = mysqli_fetch_assoc($result)) {
                                    $status_class = ($row['status_user'] == 'aktif') ? 'text-success' : 'text-danger';
                                    echo "<tr>
                                    <td>{$row['id_user']}</td>
                                    <td>{$row['username']}</td>
                                    <td><span class='{$status_class}'>{$row['status_user']}</span></td>
                                    <td>";

                                    if ($row['id_user'] != $_SESSION['id_user']) {
                                        echo "<a href='admin_delete_user.php?id={$row['id_user']}' 
                                        class='btn btn-danger btn-sm' 
                                        onclick='return confirm(\"Yakin ingin menghapus admin ini?\")'>
                                        <i class='bi bi-trash'></i>
                                    </a>";
                                    } else {
                                        echo "<span class='text-muted'>-</span>";
                                    }
                                    echo "</td></tr>";
                                }
                            } else {
                                echo "<tr><td colspan='4' class='text-center'>Tidak ada data admin</td></tr>";
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
                <a href="admin_dashboard.php" class="btn btn-outline-secondary mt-3">
                    <i class="bi bi-arrow-left me-1"></i> Kembali ke Dashboard
                </a>
            </div>
        </div>
    </div>

    <footer class="text-center text-muted small py-3 bg-light">
        &copy; <?php echo date("Y"); ?> SPMB SMK ArdsentrA
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        (function() {
            'use strict';

            // Form validation
            var forms = document.querySelectorAll('.needs-validation');

            Array.prototype.slice.call(forms).forEach(function(form) {
                form.addEventListener('submit', function(event) {
                    var password = document.getElementById('new_password');
                    var confirmPassword = document.getElementById('confirm_password');

                    if (password.value !== confirmPassword.value) {
                        confirmPassword.setCustomValidity('Konfirmasi password tidak cocok.');
                    } else {
                        confirmPassword.setCustomValidity('');
                    }

                    if (!form.checkValidity()) {
                        event.preventDefault();
                        event.stopPropagation();
                    }

                    form.classList.add('was-validated');
                }, false);
            });
        })();
    </script>
</body>

</html>