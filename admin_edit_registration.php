<?php
session_start();
if (!isset($_SESSION['id_user']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}
include 'db_config.php';

// Validasi dan ambil ID pendaftaran dari URL
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: admin_dashboard.php?error=ID pendaftar tidak valid.");
    exit();
}
$id_pendaftaran = (int)$_GET['id'];

// Query untuk mengambil semua data pendaftar yang akan diedit (tanpa dokumen)
$sql = "SELECT p.*, jk.jeniskelamin, ag.nama_agama,
               do.nama_ayah, do.pekerjaan_ayah, do.nama_ibu, do.pekerjaan_ibu, do.alamat AS alamat_ortu
        FROM pendaftaran p
        JOIN jenis_kelamin jk ON p.id_jk = jk.id_jk
        JOIN agama ag ON p.id_agama = ag.id_agama
        LEFT JOIN data_ortu do ON p.id_pendaftaran = do.id_pendaftaran
        WHERE p.id_pendaftaran = ?";

$stmt = $conn->prepare($sql);
if (false === $stmt) {
    die("Prepare statement gagal: " . $conn->error);
}
$stmt->bind_param("i", $id_pendaftaran);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows == 0) {
    header("Location: admin_dashboard.php?error=Data pendaftar tidak ditemukan.");
    exit();
}
$data = $result->fetch_assoc();
$stmt->close();
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Data Pendaftar - <?php echo htmlspecialchars($data['nama_siswa']); ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
            font-family: 'Poppins', sans-serif;
        }

        .form-container {
            max-width: 850px;
            margin: 40px auto;
        }

        .card-header h4 i {
            margin-right: 10px;
        }

        .form-label {
            font-weight: 600;
        }

        .form-text {
            font-size: 0.85em;
        }
    </style>
</head>

<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark sticky-top shadow-sm">
        <div class="container-fluid">
            <a class="navbar-brand fw-bold" href="admin_dashboard.php"><i class="bi bi-shield-lock-fill me-2"></i>Admin PPDB</a>
            <ul class="navbar-nav ms-auto">
                <li class="nav-item">
                    <a class="nav-link" href="admin_view_detail.php?id=<?php echo $id_pendaftaran; ?>"><i class="bi bi-arrow-left-circle me-1"></i>Kembali ke Detail Siswa</a>
                </li>
            </ul>
        </div>
    </nav>

    <div class="container form-container">
        <div class="text-center mb-4">
            <h1 class="display-6 fw-bold">Edit Data Pendaftar</h1>
            <p class="lead text-muted">Perbarui data untuk siswa: <strong class="text-primary"><?php echo htmlspecialchars($data['nama_siswa']); ?></strong></p>
        </div>

        <?php if (isset($_GET['update_success'])): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="bi bi-check-circle-fill me-2"></i>Data berhasil diperbarui!
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>
        <?php if (isset($_GET['update_error'])): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="bi bi-exclamation-triangle-fill me-2"></i>Gagal memperbarui data: <?php echo htmlspecialchars(urldecode($_GET['update_error'])); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <form action="admin_process_edit_registration.php" method="POST" class="needs-validation" novalidate>
            <input type="hidden" name="id_pendaftaran" value="<?php echo $data['id_pendaftaran']; ?>">

            <div class="card mb-4 shadow-sm">
                <div class="card-header bg-info text-white">
                    <h4><i class="bi bi-person-fill"></i>Data Calon Siswa</h4>
                </div>
                <div class="card-body p-4">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="nisn" class="form-label">NISN</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-person-vcard"></i></span>
                                <input type="text" class="form-control form-control-lg" id="nisn" name="nisn" value="<?php echo htmlspecialchars($data['nisn']); ?>" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label for="nama_siswa" class="form-label">Nama Lengkap Siswa</label>
                            <input type="text" class="form-control form-control-lg" id="nama_siswa" name="nama_siswa" value="<?php echo htmlspecialchars($data['nama_siswa']); ?>" required>
                        </div>
                        <?php
                        // Pisahkan nilai tempat_tgl_lahir menjadi tempat dan tanggal
                        $split = explode(',', $data['tempat_tgl_lahir']);
                        $tempat = isset($split[0]) ? trim($split[0]) : '';
                        $tanggal = isset($split[1]) ? date('Y-m-d', strtotime(trim($split[1]))) : '';
                        ?>
                        <div class="col-md-6">
                            <label for="tempat_lahir" class="form-label">Tempat Lahir</label>
                            <input type="text" class="form-control form-control-lg" id="tempat_lahir" name="tempat_lahir" value="<?php echo htmlspecialchars($tempat); ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label for="tanggal_lahir" class="form-label">Tanggal Lahir</label>
                            <input type="date" class="form-control form-control-lg" id="tanggal_lahir" name="tanggal_lahir" value="<?php echo htmlspecialchars($tanggal); ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label for="id_jk" class="form-label">Jenis Kelamin</label>
                            <select class="form-select form-select-lg" id="id_jk" name="id_jk" required>
                                <?php
                                $sql_jk = "SELECT id_jk, jeniskelamin FROM jenis_kelamin ORDER BY jeniskelamin";
                                $result_jk = $conn->query($sql_jk);
                                while ($row_jk = $result_jk->fetch_assoc()) {
                                    $selected = ($data['id_jk'] == $row_jk["id_jk"]) ? "selected" : "";
                                    echo "<option value='" . $row_jk["id_jk"] . "' " . $selected . ">" . $row_jk["jeniskelamin"] . "</option>";
                                }
                                ?>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="id_agama" class="form-label">Agama</label>
                            <select class="form-select form-select-lg" id="id_agama" name="id_agama" required>
                                <?php
                                $sql_agama = "SELECT id_agama, nama_agama FROM agama ORDER BY nama_agama";
                                $result_agama = $conn->query($sql_agama);
                                while ($row_agama = $result_agama->fetch_assoc()) {
                                    $selected = ($data['id_agama'] == $row_agama["id_agama"]) ? "selected" : "";
                                    echo "<option value='" . $row_agama["id_agama"] . "' " . $selected . ">" . $row_agama["nama_agama"] . "</option>";
                                }
                                ?>
                            </select>
                        </div>
                        <div class="col-12">
                            <label for="alamat_siswa" class="form-label">Alamat Siswa</label>
                            <textarea class="form-control form-control-lg" id="alamat_siswa" name="alamat_siswa" rows="3" required><?php echo htmlspecialchars($data['alamat']); ?></textarea>
                        </div>
                        <div class="col-md-6">
                            <label for="asal_sekolah" class="form-label">Asal Sekolah</label>
                            <input type="text" class="form-control form-control-lg" id="asal_sekolah" name="asal_sekolah" value="<?php echo htmlspecialchars($data['asal_sekolah']); ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label for="pilih_jurusan" class="form-label">Pilihan Jurusan</label>
                            <select class="form-select form-select-lg" id="pilih_jurusan" name="pilih_jurusan" required>
                                <?php
                                $jurusan_options = [
                                    "Teknik Komputer dan Jaringan",
                                    "Bisnis Daring",
                                    "Otomisasi Dan Tata Kelola Perkantoran",
                                    "Akuntansi dan Keuangan Lembaga"
                                ];
                                ?>
                                <?php foreach ($jurusan_options as $opt): ?>
                                    <option value="<?php echo $opt; ?>" <?php echo ($data['pilih_jurusan'] == $opt) ? "selected" : ""; ?>>
                                        <?php echo $opt; ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="no_handphone" class="form-label">No. Handphone Siswa</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-whatsapp"></i></span>
                                <input type="tel" class="form-control form-control-lg" id="no_handphone" name="no_handphone" value="<?php echo htmlspecialchars($data['no_handphone']); ?>" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label for="email" class="form-label">Email Siswa</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-envelope-at-fill"></i></span>
                                <input type="email" class="form-control form-control-lg" id="email" name="email" value="<?php echo htmlspecialchars($data['email']); ?>" required>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card mb-4 shadow-sm">
                <div class="card-header bg-warning text-dark">
                    <h4><i class="bi bi-people-fill"></i>Data Orang Tua/Wali</h4>
                </div>
                <div class="card-body p-4">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="nama_ayah" class="form-label">Nama Ayah</label>
                            <input type="text" class="form-control form-control-lg" id="nama_ayah" name="nama_ayah" value="<?php echo htmlspecialchars($data['nama_ayah'] ?? ''); ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label for="pekerjaan_ayah" class="form-label">Pekerjaan Ayah</label>
                            <input type="text" class="form-control form-control-lg" id="pekerjaan_ayah" name="pekerjaan_ayah" value="<?php echo htmlspecialchars($data['pekerjaan_ayah'] ?? ''); ?>">
                        </div>
                        <div class="col-md-6">
                            <label for="nama_ibu" class="form-label">Nama Ibu</label>
                            <input type="text" class="form-control form-control-lg" id="nama_ibu" name="nama_ibu" value="<?php echo htmlspecialchars($data['nama_ibu'] ?? ''); ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label for="pekerjaan_ibu" class="form-label">Pekerjaan Ibu</label>
                            <input type="text" class="form-control form-control-lg" id="pekerjaan_ibu" name="pekerjaan_ibu" value="<?php echo htmlspecialchars($data['pekerjaan_ibu'] ?? ''); ?>">
                        </div>
                        <div class="col-12">
                            <label for="alamat_ortu" class="form-label">Alamat Orang Tua</label>
                            <textarea class="form-control form-control-lg" id="alamat_ortu" name="alamat_ortu" rows="3" required><?php echo htmlspecialchars($data['alamat_ortu'] ?? ''); ?></textarea>
                        </div>
                    </div>
                </div>
            </div>

            <div class="d-grid gap-2">
                <button type="submit" class="btn btn-primary btn-lg fw-bold py-3">
                    <i class="bi bi-save-fill me-2"></i>Simpan Perubahan
                </button>
            </div>
        </form>
    </div>

    <footer class="py-4 mt-5 bg-light text-center">
        <p class="mb-0 text-muted small">&copy; <?php echo date("Y"); ?> PPDB SMK XYZ Admin Panel</p>
    </footer>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Script validasi Bootstrap
        (function() {
            'use strict'
            var forms = document.querySelectorAll('.needs-validation')
            Array.prototype.slice.call(forms).forEach(function(form) {
                form.addEventListener('submit', function(event) {
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
<?php $conn->close(); ?>