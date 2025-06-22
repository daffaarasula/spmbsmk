<?php
// admin_view_detail.php
session_start();

if (!isset($_SESSION['id_user']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

include 'db_config.php';

if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: admin_dashboard.php?error=No registration ID specified.");
    exit();
}

$id_pendaftaran = filter_var($_GET['id'], FILTER_SANITIZE_NUMBER_INT);

// Query untuk mengambil semua data yang dibutuhkan
$sql = "SELECT 
            p.*, 
            jk.jeniskelamin, 
            ag.nama_agama,
            do.nama_ayah, do.pekerjaan_ayah, do.nama_ibu, do.pekerjaan_ibu, do.alamat AS alamat_ortu
        FROM pendaftaran p
        JOIN jenis_kelamin jk ON p.id_jk = jk.id_jk
        JOIN agama ag ON p.id_agama = ag.id_agama
        LEFT JOIN data_ortu do ON p.id_pendaftaran = do.id_pendaftaran
        WHERE p.id_pendaftaran = ?";
// Ambil data dokumen
$sql_dokumen = "SELECT pasfoto, ijazah, ktp, kk, status_verifikasi, catatan_admin 
                FROM dokumen WHERE id_pendaftaran = ?";
$stmt_dokumen = $conn->prepare($sql_dokumen);
$stmt_dokumen->bind_param("i", $id_pendaftaran);
$stmt_dokumen->execute();
$hasil_dokumen = $stmt_dokumen->get_result();
$dokumen = $hasil_dokumen->fetch_assoc();
$stmt_dokumen->close();

$stmt = $conn->prepare($sql);
if (false === $stmt) {
    die("Prepare failed: " . $conn->error);
}

$stmt->bind_param("i", $id_pendaftaran);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) {
    header("Location: admin_dashboard.php?error=Registration not found.");
    exit();
}
$data = $result->fetch_assoc();
$stmt->close();

// Array untuk dropdown status
$possible_statuses = ['Pending Review', 'Accepted', 'Rejected', 'Waiting List'];

// Tentukan warna badge berdasarkan status
$status_class_map = [
    'Accepted' => 'success',
    'Rejected' => 'danger',
    'Pending Review' => 'warning',
    'Waiting List' => 'dark',
];
$status_badge_class = $status_class_map[$data['status_pendaftaran']] ?? 'secondary';
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Pendaftar - <?php echo htmlspecialchars($data['nama_siswa']); ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <style>
        body {
            background-color: #f8f9fa;
        }

        .profile-card .card-body {
            display: flex;
            flex-direction: column;
            align-items: center;
            text-align: center;
        }

        .profile-pic {
            width: 150px;
            height: 150px;
            border-radius: 50%;
            object-fit: cover;
            border: 4px solid #fff;
            box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
        }

        .list-group-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-left: 0;
            border-right: 0;
        }

        .list-group-item .label {
            font-weight: 600;
            color: #6c757d;
        }

        .list-group-item .value {
            font-weight: 500;
            text-align: right;
        }

        .document-link {
            transition: all 0.2s ease;
        }

        .document-link:hover {
            transform: translateY(-2px);
            box-shadow: 0 0.25rem 0.5rem rgba(0, 0, 0, 0.1);
        }
    </style>
</head>

<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark sticky-top shadow-sm">
        <div class="container-fluid">
            <a class="navbar-brand fw-bold" href="admin_dashboard.php"><i class="bi bi-shield-lock-fill me-2"></i>Admin PPDB</a>
            <ul class="navbar-nav ms-auto">
                <li class="nav-item">
                    <a class="nav-link" href="admin_dashboard.php"><i class="bi bi-arrow-left-circle me-1"></i>Kembali ke Dashboard</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="admin_logout.php"><i class="bi bi-box-arrow-right me-1"></i>Logout</a>
                </li>
            </ul>
        </div>
    </nav>

    <div class="container-fluid mt-4">
        <div class="row">
            <div class="col-lg-4 mb-4">
                <div class="card shadow-sm profile-card">
                    <div class="card-body">
                        <img src="<?php echo (!empty($dokumen['pasfoto']) && file_exists($dokumen['pasfoto'])) ? htmlspecialchars($dokumen['pasfoto']) : 'https://via.placeholder.com/150?text=No+Photo'; ?>"
                            alt="Pas Foto Siswa" class="profile-pic mb-3">
                        <h4 class="card-title fw-bold mb-1"><?php echo htmlspecialchars($data['nama_siswa']); ?></h4>
                        <p class="text-muted mb-2">ID Pendaftaran: <?php echo $data['id_pendaftaran']; ?></p>
                        <span class="badge fs-6 rounded-pill bg-<?php echo $status_badge_class; ?>"><?php echo htmlspecialchars($data['status_pendaftaran'] ?? 'N/A'); ?></span>
                    </div>
                </div>

                <div class="card shadow-sm mt-4">
                    <div class="card-header bg-light">
                        <h5 class="mb-0"><i class="bi bi-person-rolodex me-2"></i>Info Kontak</h5>
                    </div>
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item">
                            <span class="label">NISN</span>
                            <span class="value"><?php echo htmlspecialchars($data['nisn']); ?></span>
                        </li>
                        <li class="list-group-item">
                            <span class="label">Pilihan Jurusan</span>
                            <span class="value"><?php echo htmlspecialchars($data['pilih_jurusan']); ?></span>
                        </li>
                        <li class="list-group-item">
                            <span class="label"><i class="bi bi-envelope-fill me-2"></i>Email</span>
                            <span class="value"><?php echo htmlspecialchars($data['email']); ?></span>
                        </li>
                        <li class="list-group-item">
                            <span class="label"><i class="bi bi-telephone-fill me-2"></i>No. Handphone</span>
                            <span class="value"><?php echo htmlspecialchars($data['no_handphone']); ?></span>
                        </li>
                    </ul>
                </div>
            </div>

            <div class="col-lg-8">
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-light d-flex justify-content-between align-items-center">
                        <h5 class="mb-0"><i class="bi bi-gear-fill me-2"></i>Aksi & Status</h5>
                        <a href="admin_edit_registration.php?id=<?php echo $data['id_pendaftaran']; ?>" class="btn btn-warning btn-sm">
                            <i class="bi bi-pencil-square me-1"></i>Edit Data Lengkap
                        </a>
                    </div>
                    <div class="card-body">
                        <div class="card shadow-sm mb-4">
                            <div class="card-header bg-light">
                                <h5 class="mb-0"><i class="bi bi-file-earmark-text me-2"></i>Verifikasi Dokumen</h5>
                            </div>
                            <div class="card-body">
                                <?php if (!$dokumen): ?>
                                    <div class="alert alert-warning">Dokumen belum tersedia untuk pendaftar ini.</div>
                                <?php else: ?>
                                    <ul class="list-group list-group-flush mb-3">
                                        <li class="list-group-item"><a class="document-link" href="<?= $dokumen['pasfoto'] ?>" target="_blank"><i class="bi bi-image me-2"></i>Pasfoto</a></li>
                                        <li class="list-group-item"><a class="document-link" href="<?= $dokumen['ijazah'] ?>" target="_blank"><i class="bi bi-file-earmark-pdf me-2"></i>Ijazah</a></li>
                                        <li class="list-group-item"><a class="document-link" href="<?= $dokumen['ktp'] ?>" target="_blank"><i class="bi bi-card-heading me-2"></i>KTP Orang Tua</a></li>
                                        <li class="list-group-item"><a class="document-link" href="<?= $dokumen['kk'] ?>" target="_blank"><i class="bi bi-journal-text me-2"></i>Kartu Keluarga</a></li>
                                    </ul>

                                    <form action="proses_verifikasi_dokumen.php" method="POST">
                                        <input type="hidden" name="id_pendaftaran" value="<?= $id_pendaftaran ?>">
                                        <div class="mb-3">
                                            <label for="status_verifikasi" class="form-label">Status Verifikasi</label>
                                            <select name="status_verifikasi" class="form-select" required>
                                                <option value="Menunggu" <?= $dokumen['status_verifikasi'] === 'Menunggu' ? 'selected' : '' ?>>Menunggu</option>
                                                <option value="Diverifikasi" <?= $dokumen['status_verifikasi'] === 'Diverifikasi' ? 'selected' : '' ?>>Diverifikasi</option>
                                                <option value="Ditolak" <?= $dokumen['status_verifikasi'] === 'Ditolak' ? 'selected' : '' ?>>Ditolak</option>
                                            </select>
                                        </div>
                                        <div class="mb-3">
                                            <label for="catatan_admin" class="form-label">Catatan (jika ditolak)</label>
                                            <textarea name="catatan_admin" rows="3" class="form-control"><?= htmlspecialchars($dokumen['catatan_admin']) ?></textarea>
                                        </div>
                                        <button type="submit" class="btn btn-primary"><i class="bi bi-shield-check me-1"></i>Simpan Verifikasi</button>
                                    </form>
                                <?php endif; ?>
                            </div>
                        </div>
                        <?php if (isset($_GET['status_success'])): ?>
                            <div class="alert alert-success"><i class="bi bi-check-circle-fill me-2"></i>Status berhasil diperbarui!</div>
                        <?php endif; ?>
                        <?php if (isset($_GET['status_error'])): ?>
                            <div class="alert alert-danger"><i class="bi bi-exclamation-triangle-fill me-2"></i>Gagal memperbarui status: <?php echo htmlspecialchars($_GET['status_error']); ?></div>
                        <?php endif; ?>

                        <form action="admin_update_status.php" method="POST" class="row g-3 align-items-center">
                            <input type="hidden" name="id_pendaftaran" value="<?php echo $data['id_pendaftaran']; ?>">
                            <div class="col-md-8">
                                <label for="status_pendaftaran" class="form-label fw-semibold">Ubah Status Menjadi:</label>
                                <select name="status_pendaftaran" id="status_pendaftaran" class="form-select form-select-lg" required>
                                    <?php foreach ($possible_statuses as $status): ?>
                                        <option value="<?php echo htmlspecialchars($status); ?>" <?php echo ($data['status_pendaftaran'] == $status) ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($status); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-4 mt-auto">
                                <button type="submit" class="btn btn-success btn-lg w-100"><i class="bi bi-save-fill me-1"></i>Update</button>
                            </div>
                        </form>
                    </div>
                </div>

                <div class="card shadow-sm">
                    <div class="card-body">
                        <ul class="nav nav-tabs" id="myTab" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link active" id="siswa-tab" data-bs-toggle="tab" data-bs-target="#siswa-tab-pane" type="button" role="tab" aria-controls="siswa-tab-pane" aria-selected="true"><i class="bi bi-person-fill me-1"></i>Data Diri Siswa</button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="ortu-tab" data-bs-toggle="tab" data-bs-target="#ortu-tab-pane" type="button" role="tab" aria-controls="ortu-tab-pane" aria-selected="false"><i class="bi bi-people-fill me-1"></i>Data Orang Tua</button>
                            </li>
                        </ul>
                        <div class="tab-content pt-3" id="myTabContent">
                            <div class="tab-pane fade show active" id="siswa-tab-pane" role="tabpanel" aria-labelledby="siswa-tab" tabindex="0">
                                <h5 class="mb-3">Informasi Pribadi Siswa</h5>
                                <ul class="list-group list-group-flush">
                                    <li class="list-group-item"><span class="label">TTL</span> <span class="value"><?php echo htmlspecialchars($data['tempat_tgl_lahir']); ?></span></li>
                                    <li class="list-group-item"><span class="label">Jenis Kelamin</span> <span class="value"><?php echo htmlspecialchars($data['jeniskelamin']); ?></span></li>
                                    <li class="list-group-item"><span class="label">Agama</span> <span class="value"><?php echo htmlspecialchars($data['nama_agama']); ?></span></li>
                                    <li class="list-group-item"><span class="label">Asal Sekolah</span> <span class="value"><?php echo htmlspecialchars($data['asal_sekolah']); ?></span></li>
                                    <li class="list-group-item"><span class="label">Alamat Siswa</span> <span class="value"><?php echo nl2br(htmlspecialchars($data['alamat'])); ?></span></li>
                                </ul>
                            </div>
                            <div class="tab-pane fade" id="ortu-tab-pane" role="tabpanel" aria-labelledby="ortu-tab" tabindex="0">
                                <h5 class="mb-3">Informasi Orang Tua / Wali</h5>
                                <ul class="list-group list-group-flush">
                                    <li class="list-group-item"><span class="label">Nama Ayah</span> <span class="value"><?php echo htmlspecialchars($data['nama_ayah'] ?? 'N/A'); ?></span></li>
                                    <li class="list-group-item"><span class="label">Pekerjaan Ayah</span> <span class="value"><?php echo htmlspecialchars($data['pekerjaan_ayah'] ?? 'N/A'); ?></span></li>
                                    <li class="list-group-item"><span class="label">Nama Ibu</span> <span class="value"><?php echo htmlspecialchars($data['nama_ibu'] ?? 'N/A'); ?></span></li>
                                    <li class="list-group-item"><span class="label">Pekerjaan Ibu</span> <span class="value"><?php echo htmlspecialchars($data['pekerjaan_ibu'] ?? 'N/A'); ?></span></li>
                                    <li class="list-group-item"><span class="label">Alamat Orang Tua</span> <span class="value"><?php echo nl2br(htmlspecialchars($data['alamat_ortu'] ?? 'N/A')); ?></span></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <footer class="py-4 bg-light mt-auto">
        <div class="container-fluid px-4">
            <hr class="my-3" style="border-color: #4A5A6A;">
            <p class="text-center small mb-0">&copy; <?php echo date("Y"); ?> SPMB SMK ArdsentrA. Hak Cipta Dilindungi.</p>
        </div>
    </footer>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
<?php $conn->close(); ?>