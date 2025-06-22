<?php
session_start();

// Koneksi database
require_once 'db_config.php'; // pastikan ini membuat $conn (mysqli)

if (!isset($_SESSION['id_user']) || $_SESSION['role'] !== 'pendaftar') {
    header("Location: login.php");
    exit();
}

$username = $_SESSION['username'];

// Ambil id_pendaftaran berdasarkan NISN yang digunakan sebagai username
$stmt = $conn->prepare("SELECT id_pendaftaran FROM pendaftaran WHERE nisn = ?");
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();
$pendaftaranRow = $result->fetch_assoc();
$stmt->close();

if (!$pendaftaranRow) {
    echo "Data pendaftaran tidak ditemukan.";
    exit();
}

$id_pendaftaran = $pendaftaranRow['id_pendaftaran'];

// Ambil data pendaftar
$stmt = $conn->prepare("
    SELECT p.*, jk.jeniskelamin, a.nama_agama 
    FROM pendaftaran p 
    LEFT JOIN jenis_kelamin jk ON p.id_jk = jk.id_jk 
    LEFT JOIN agama a ON p.id_agama = a.id_agama 
    WHERE p.id_pendaftaran = ?
");
$stmt->bind_param("i", $id_pendaftaran);
$stmt->execute();
$result = $stmt->get_result();
$pendaftar = $result->fetch_assoc();
$stmt->close();

// Ambil status dokumen untuk notifikasi
$stmt_dokumen = $conn->prepare("
    SELECT ijazah, pasfoto, ktp, kk, status_verifikasi, catatan_admin 
    FROM dokumen 
    WHERE id_pendaftaran = ?
");
$stmt_dokumen->bind_param("i", $id_pendaftaran);
$stmt_dokumen->execute();
$result_dokumen = $stmt_dokumen->get_result();
$dokumen = $result_dokumen->fetch_assoc();
$stmt_dokumen->close();

// Notifikasi dokumen
$notifikasi = "";
$notifikasi_class = "";

if ($dokumen) {
    switch ($dokumen['status_verifikasi']) {
        case 'Diverifikasi':
            $notifikasi = "✅ Dokumen Anda telah diverifikasi dan diterima!";
            $notifikasi_class = "alert-success";
            break;
        case 'Ditolak':
            $notifikasi = "❌ Dokumen Anda ditolak. Catatan: " . ($dokumen['catatan_admin'] ?: "Silakan periksa kembali dokumen yang diupload.");
            $notifikasi_class = "alert-danger";
            break;
        case 'Menunggu':
        default:
            $notifikasi = "⏳ Dokumen Anda sedang dalam proses review. Mohon tunggu konfirmasi dari admin.";
            $notifikasi_class = "alert-warning";
            break;
    }
} else {
    $notifikasi = "📋 Anda belum mengupload dokumen. Silakan lengkapi dokumen yang diperlukan.";
    $notifikasi_class = "alert-info";
}

// Logout
if (isset($_GET['logout'])) {
    session_destroy();
    header("Location: login.php");
    exit();
}

$current_page = isset($_GET['page']) ? $_GET['page'] : 'data_diri';
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Pendaftar - PPDB SMK</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .sidebar {
            height: 100vh;
            background-color: #2c3e50;
            color: white;
            position: fixed;
            left: 0;
            top: 0;
            width: 250px;
            z-index: 1000;
        }

        .sidebar .nav-link {
            color: #bdc3c7;
            padding: 15px 20px;
            border-bottom: 1px solid #34495e;
        }

        .sidebar .nav-link:hover,
        .sidebar .nav-link.active {
            background-color: #34495e;
            color: white;
        }

        .main-content {
            margin-left: 250px;
            min-height: 100vh;
        }

        .navbar-custom {
            background-color: #3498db;
            padding: 1rem 2rem;
        }

        .welcome-text {
            color: white;
            margin: 0;
        }

        .content-area {
            padding: 2rem;
        }

        .notification-area {
            margin-bottom: 2rem;
        }

        .card-header {
            background-color: #3498db;
            color: white;
        }

        .status-badge {
            font-size: 0.9em;
            padding: 0.5rem 1rem;
        }
    </style>
</head>

<body>
    <!-- Sidebar -->
    <div class="sidebar">
        <div class="p-3 border-bottom border-secondary">
            <h4 class="text-center">
                <i class="fas fa-graduation-cap"></i>
                SPMB SMK ArdsentrA
            </h4>
        </div>
        <nav class="nav flex-column">
            <a class="nav-link <?= $current_page == 'data_diri' ? 'active' : '' ?>" href="?page=data_diri">
                <i class="fas fa-user me-2"></i>
                Data Diri
            </a>
            <a class="nav-link <?= $current_page == 'dokumen' ? 'active' : '' ?>" href="?page=dokumen">
                <i class="fas fa-file-alt me-2"></i>
                Dokumen
            </a>
        </nav>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <!-- Navbar -->
        <nav class="navbar navbar-custom">
            <div class="container-fluid">
                <h5 class="welcome-text">
                    Selamat datang, <?= htmlspecialchars($pendaftar['nama_siswa']) ?>!
                </h5>
                <div>
                    <span class="badge bg-light text-dark me-2">
                        Status: <?= htmlspecialchars($pendaftar['status_pendaftaran']) ?>
                    </span>
                    <a href="?logout=1" class="btn btn-outline-light btn-sm">
                        <i class="fas fa-sign-out-alt me-1"></i>
                        Logout
                    </a>
                </div>
            </div>
        </nav>

        <!-- Content Area -->
        <div class="content-area">
            <!-- Notifikasi -->
            <div class="notification-area">
                <div class="alert <?= $notifikasi_class ?> alert-dismissible fade show" role="alert">
                    <strong>Pemberitahuan:</strong> <?= $notifikasi ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            </div>

            <!-- Dynamic Content -->
            <?php if ($current_page == 'data_diri'): ?>
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="fas fa-user me-2"></i>
                            Data Diri Pendaftar
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <table class="table table-borderless">
                                    <tr>
                                        <td><strong>NISN</strong></td>
                                        <td>: <?= htmlspecialchars($pendaftar['nisn']) ?></td>
                                    </tr>
                                    <tr>
                                        <td><strong>Nama Lengkap</strong></td>
                                        <td>: <?= htmlspecialchars($pendaftar['nama_siswa']) ?></td>
                                    </tr>
                                    <tr>
                                        <td><strong>Tempat, Tanggal Lahir</strong></td>
                                        <td>: <?= htmlspecialchars($pendaftar['tempat_tgl_lahir']) ?></td>
                                    </tr>
                                    <tr>
                                        <td><strong>Jenis Kelamin</strong></td>
                                        <td>: <?= htmlspecialchars($pendaftar['jeniskelamin']) ?></td>
                                    </tr>
                                    <tr>
                                        <td><strong>Agama</strong></td>
                                        <td>: <?= htmlspecialchars($pendaftar['nama_agama']) ?></td>
                                    </tr>
                                </table>
                            </div>
                            <div class="col-md-6">
                                <table class="table table-borderless">
                                    <tr>
                                        <td><strong>Alamat</strong></td>
                                        <td>: <?= htmlspecialchars($pendaftar['alamat']) ?></td>
                                    </tr>
                                    <tr>
                                        <td><strong>Asal Sekolah</strong></td>
                                        <td>: <?= htmlspecialchars($pendaftar['asal_sekolah']) ?></td>
                                    </tr>
                                    <tr>
                                        <td><strong>Pilihan Jurusan</strong></td>
                                        <td>: <?= htmlspecialchars($pendaftar['pilih_jurusan']) ?></td>
                                    </tr>
                                    <tr>
                                        <td><strong>No. Handphone</strong></td>
                                        <td>: <?= htmlspecialchars($pendaftar['no_handphone']) ?></td>
                                    </tr>
                                    <tr>
                                        <td><strong>Email</strong></td>
                                        <td>: <?= htmlspecialchars($pendaftar['email']) ?></td>
                                    </tr>
                                </table>
                            </div>
                        </div>

                        <div class="mt-4">
                            <h6>Status Pendaftaran:</h6>
                            <span class="badge status-badge <?=
                                                            $pendaftar['status_pendaftaran'] == 'Accepted' ? 'bg-success' : ($pendaftar['status_pendaftaran'] == 'Rejected' ? 'bg-danger' : 'bg-warning text-dark')
                                                            ?>">
                                <?= htmlspecialchars($pendaftar['status_pendaftaran']) ?>
                            </span>
                        </div>

                        <?php
                        // Ambil data orang tua jika ada
                        $stmt_ortu = $conn->prepare("SELECT * FROM data_ortu WHERE id_pendaftaran = ?");
                        $stmt_ortu->bind_param("i", $id_pendaftaran);
                        $stmt_ortu->execute();
                        $result_ortu = $stmt_ortu->get_result();
                        $data_ortu = $result_ortu->fetch_assoc();
                        $stmt_ortu->close();

                        if ($data_ortu):
                        ?>
                            <div class="mt-4">
                                <h6>Data Orang Tua:</h6>
                                <div class="row mt-3">
                                    <div class="col-md-6">
                                        <table class="table table-borderless">
                                            <tr>
                                                <td><strong>Nama Ayah</strong></td>
                                                <td>: <?= htmlspecialchars($data_ortu['nama_ayah']) ?></td>
                                            </tr>
                                            <tr>
                                                <td><strong>Pekerjaan Ayah</strong></td>
                                                <td>: <?= htmlspecialchars($data_ortu['pekerjaan_ayah']) ?></td>
                                            </tr>
                                        </table>
                                    </div>
                                    <div class="col-md-6">
                                        <table class="table table-borderless">
                                            <tr>
                                                <td><strong>Nama Ibu</strong></td>
                                                <td>: <?= htmlspecialchars($data_ortu['nama_ibu']) ?></td>
                                            </tr>
                                            <tr>
                                                <td><strong>Pekerjaan Ibu</strong></td>
                                                <td>: <?= htmlspecialchars($data_ortu['pekerjaan_ibu']) ?></td>
                                            </tr>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        <?php endif; ?>

                        <div class="mt-4">
                            <a href="edit_data_diri.php" class="btn btn-primary">
                                <i class="fas fa-edit me-2"></i>
                                Edit Data Diri
                            </a>
                        </div>
                    </div>
                </div>

            <?php elseif ($current_page == 'dokumen'): ?>
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="fas fa-file-alt me-2"></i>
                            Dokumen Pendaftaran
                        </h5>
                    </div>
                    <div class="card-body">
                        <?php if ($dokumen): ?>
                            <div class="alert alert-info">
                                <h6>Status Verifikasi Dokumen:</h6>
                                <span class="badge <?=
                                                    $dokumen['status_verifikasi'] == 'Diverifikasi' ? 'bg-success' : ($dokumen['status_verifikasi'] == 'Ditolak' ? 'bg-danger' : 'bg-warning text-dark')
                                                    ?>">
                                    <?= htmlspecialchars($dokumen['status_verifikasi']) ?>
                                </span>

                                <?php if ($dokumen['catatan_admin']): ?>
                                    <div class="mt-2">
                                        <strong>Catatan Admin:</strong><br>
                                        <?= htmlspecialchars($dokumen['catatan_admin']) ?>
                                    </div>
                                <?php endif; ?>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="card mb-3">
                                        <div class="card-header">
                                            <h6 class="mb-0">Ijazah</h6>
                                        </div>
                                        <div class="card-body">
                                            <?php if ($dokumen['ijazah']): ?>
                                                <p class="text-success">
                                                    <i class="fas fa-check-circle"></i>
                                                    Sudah diupload
                                                </p>
                                                <a href="<?= htmlspecialchars($dokumen['ijazah']) ?>"
                                                    class="btn btn-sm btn-outline-primary" target="_blank">
                                                    <i class="fas fa-eye"></i> Lihat File
                                                </a>
                                            <?php else: ?>
                                                <p class="text-danger">
                                                    <i class="fas fa-times-circle"></i>
                                                    Belum diupload
                                                </p>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="card mb-3">
                                        <div class="card-header">
                                            <h6 class="mb-0">Pas Foto</h6>
                                        </div>
                                        <div class="card-body">
                                            <?php if ($dokumen['pasfoto']): ?>
                                                <p class="text-success">
                                                    <i class="fas fa-check-circle"></i>
                                                    Sudah diupload
                                                </p>
                                                <a href="<?= htmlspecialchars($dokumen['pasfoto']) ?>"
                                                    class="btn btn-sm btn-outline-primary" target="_blank">
                                                    <i class="fas fa-eye"></i> Lihat File
                                                </a>
                                            <?php else: ?>
                                                <p class="text-danger">
                                                    <i class="fas fa-times-circle"></i>
                                                    Belum diupload
                                                </p>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="card mb-3">
                                        <div class="card-header">
                                            <h6 class="mb-0">KTP</h6>
                                        </div>
                                        <div class="card-body">
                                            <?php if ($dokumen['ktp']): ?>
                                                <p class="text-success">
                                                    <i class="fas fa-check-circle"></i>
                                                    Sudah diupload
                                                </p>
                                                <a href="<?= htmlspecialchars($dokumen['ktp']) ?>"
                                                    class="btn btn-sm btn-outline-primary" target="_blank">
                                                    <i class="fas fa-eye"></i> Lihat File
                                                </a>
                                            <?php else: ?>
                                                <p class="text-danger">
                                                    <i class="fas fa-times-circle"></i>
                                                    Belum diupload
                                                </p>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="card mb-3">
                                        <div class="card-header">
                                            <h6 class="mb-0">Kartu Keluarga</h6>
                                        </div>
                                        <div class="card-body">
                                            <?php if ($dokumen['kk']): ?>
                                                <p class="text-success">
                                                    <i class="fas fa-check-circle"></i>
                                                    Sudah diupload
                                                </p>
                                                <a href="<?= htmlspecialchars($dokumen['kk']) ?>"
                                                    class="btn btn-sm btn-outline-primary" target="_blank">
                                                    <i class="fas fa-eye"></i> Lihat File
                                                </a>
                                            <?php else: ?>
                                                <p class="text-danger">
                                                    <i class="fas fa-times-circle"></i>
                                                    Belum diupload
                                                </p>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <?php if ($dokumen['status_verifikasi'] == 'Ditolak'): ?>
                                <div class="alert alert-warning mt-3">
                                    <h6>Dokumen Perlu Diperbaiki</h6>
                                    <p><?= htmlspecialchars($dokumen['catatan_admin']) ?: 'Silakan periksa kembali dokumen Anda.' ?></p>
                                    <a href="upload_ulang_dokumen.php" class="btn btn-warning mt-2">
                                        <i class="fas fa-upload"></i> Upload Ulang Dokumen
                                    </a>
                                </div>
                            <?php endif; ?>

                        <?php else: ?>
                            <div class="alert alert-info">
                                <h6>Belum Ada Dokumen</h6>
                                <p>Anda belum mengupload dokumen apapun. Silakan hubungi admin untuk mengetahui cara mengupload dokumen yang diperlukan.</p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>