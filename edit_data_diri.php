<?php
session_start();

// Koneksi database
require_once 'db_config.php'; // pastikan ini membuat $conn (mysqli)

if (!isset($_SESSION['id_user']) || $_SESSION['role'] !== 'pendaftar') {
    header("Location: login.php");
    exit();
}

$username = $_SESSION['username'];
$message = '';
$message_type = '';

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

// Ambil data untuk dropdown
$jenis_kelamin = $conn->query("SELECT * FROM jenis_kelamin ORDER BY jeniskelamin");
$agama = $conn->query("SELECT * FROM agama ORDER BY nama_agama");

// Proses form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama_siswa = $_POST['nama_siswa'];
    $tempat_tgl_lahir = $_POST['tempat_tgl_lahir'];
    $id_jk = $_POST['id_jk'];
    $id_agama = $_POST['id_agama'];
    $alamat = $_POST['alamat'];
    $asal_sekolah = $_POST['asal_sekolah'];
    $pilih_jurusan = $_POST['pilih_jurusan'];
    $no_handphone = $_POST['no_handphone'];
    $email = $_POST['email'];

    // Data orang tua
    $nama_ayah = $_POST['nama_ayah'];
    $pekerjaan_ayah = $_POST['pekerjaan_ayah'];
    $nama_ibu = $_POST['nama_ibu'];
    $pekerjaan_ibu = $_POST['pekerjaan_ibu'];

    // Validasi email
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $message = "Format email tidak valid!";
        $message_type = "danger";
    } else {
        // Begin transaction
        $conn->begin_transaction();

        try {
            // Update data pendaftaran
            $stmt = $conn->prepare("
                UPDATE pendaftaran 
                SET nama_siswa = ?, tempat_tgl_lahir = ?, id_jk = ?, id_agama = ?, 
                    alamat = ?, asal_sekolah = ?, pilih_jurusan = ?, no_handphone = ?, email = ? 
                WHERE id_pendaftaran = ?
            ");
            $stmt->bind_param(
                "ssiisssssi",
                $nama_siswa,
                $tempat_tgl_lahir,
                $id_jk,
                $id_agama,
                $alamat,
                $asal_sekolah,
                $pilih_jurusan,
                $no_handphone,
                $email,
                $id_pendaftaran
            );
            $stmt->execute();
            $stmt->close();

            // Cek apakah data orang tua sudah ada
            $stmt_check = $conn->prepare("SELECT id_data_ortu FROM data_ortu WHERE id_pendaftaran = ?");
            $stmt_check->bind_param("i", $id_pendaftaran);
            $stmt_check->execute();
            $result_check = $stmt_check->get_result();
            $ortu_exists = $result_check->fetch_assoc();
            $stmt_check->close();

            if ($ortu_exists) {
                // Update data orang tua yang sudah ada
                $stmt_ortu = $conn->prepare("
                    UPDATE data_ortu 
                    SET nama_ayah = ?, pekerjaan_ayah = ?, nama_ibu = ?, pekerjaan_ibu = ? 
                    WHERE id_pendaftaran = ?
                ");
                $stmt_ortu->bind_param("ssssi", $nama_ayah, $pekerjaan_ayah, $nama_ibu, $pekerjaan_ibu, $id_pendaftaran);
            } else {
                // Insert data orang tua baru
                $stmt_ortu = $conn->prepare("
                    INSERT INTO data_ortu (id_pendaftaran, nama_ayah, pekerjaan_ayah, nama_ibu, pekerjaan_ibu) 
                    VALUES (?, ?, ?, ?, ?)
                ");
                $stmt_ortu->bind_param("issss", $id_pendaftaran, $nama_ayah, $pekerjaan_ayah, $nama_ibu, $pekerjaan_ibu);
            }

            $stmt_ortu->execute();
            $stmt_ortu->close();

            // Commit transaction
            $conn->commit();

            $message = "Data berhasil diperbarui!";
            $message_type = "success";
        } catch (Exception $e) {
            // Rollback transaction
            $conn->rollback();
            $message = "Terjadi kesalahan: " . $e->getMessage();
            $message_type = "danger";
        }
    }
}

// Ambil data pendaftar untuk ditampilkan di form
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

// Ambil data orang tua
$stmt_ortu = $conn->prepare("SELECT * FROM data_ortu WHERE id_pendaftaran = ?");
$stmt_ortu->bind_param("i", $id_pendaftaran);
$stmt_ortu->execute();
$result_ortu = $stmt_ortu->get_result();
$data_ortu = $result_ortu->fetch_assoc();
$stmt_ortu->close();
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Data Diri - SPMB SMK ArdsentrA</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
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
            max-width: 1200px;
            margin: 0 auto;
        }

        .card-header {
            background-color: #3498db;
            color: white;
        }

        .required {
            color: red;
        }

        .form-section {
            background-color: #f8f9fa;
            padding: 1.5rem;
            border-radius: 0.5rem;
            margin-bottom: 1.5rem;
        }

        .section-title {
            color: #2c3e50;
            border-bottom: 2px solid #3498db;
            padding-bottom: 0.5rem;
            margin-bottom: 1rem;
        }
    </style>
</head>

<body>
    <!-- Navbar -->
    <nav class="navbar navbar-custom">
        <div class="container-fluid">
            <h5 class="welcome-text">
                <i class="fas fa-edit me-2"></i>
                Edit Data Diri - <?= htmlspecialchars($pendaftar['nama_siswa']) ?>
            </h5>
            <div>
                <a href="pendaftar_dashboard.php" class="btn btn-outline-light btn-sm me-2">
                    <i class="fas fa-arrow-left me-1"></i>
                    Kembali ke Dashboard
                </a>
                <a href="?logout=1" class="btn btn-outline-light btn-sm">
                    <i class="fas fa-sign-out-alt me-1"></i>
                    Logout
                </a>
            </div>
        </div>
    </nav>

    <!-- Content Area -->
    <div class="content-area">
        <!-- Alert Message -->
        <?php if ($message): ?>
            <div class="alert alert-<?= $message_type ?> alert-dismissible fade show" role="alert">
                <strong><?= $message_type == 'success' ? 'Berhasil!' : 'Error!' ?></strong> <?= $message ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="fas fa-user-edit me-2"></i>
                    Edit Data Diri dan Orang Tua
                </h5>
            </div>
            <div class="card-body">
                <form method="POST" id="editForm">
                    <!-- Data Diri Siswa -->
                    <div class="form-section">
                        <h6 class="section-title">
                            <i class="fas fa-user me-2"></i>
                            Data Diri Siswa
                        </h6>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="nisn" class="form-label">NISN <span class="required">*</span></label>
                                    <input type="text" class="form-control" id="nisn"
                                        value="<?= htmlspecialchars($pendaftar['nisn']) ?>" readonly>
                                    <small class="form-text text-muted">NISN tidak dapat diubah</small>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="nama_siswa" class="form-label">Nama Lengkap <span class="required">*</span></label>
                                    <input type="text" class="form-control" id="nama_siswa" name="nama_siswa"
                                        value="<?= htmlspecialchars($pendaftar['nama_siswa']) ?>" required>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="tempat_tgl_lahir" class="form-label">Tempat, Tanggal Lahir <span class="required">*</span></label>
                                    <input type="text" class="form-control" id="tempat_tgl_lahir" name="tempat_tgl_lahir"
                                        value="<?= htmlspecialchars($pendaftar['tempat_tgl_lahir']) ?>"
                                        placeholder="Contoh: Jakarta, 15 Agustus 2005" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="id_jk" class="form-label">Jenis Kelamin <span class="required">*</span></label>
                                    <select class="form-select" id="id_jk" name="id_jk" required>
                                        <option value="">Pilih Jenis Kelamin</option>
                                        <?php
                                        $jenis_kelamin->data_seek(0);
                                        while ($jk = $jenis_kelamin->fetch_assoc()):
                                        ?>
                                            <option value="<?= $jk['id_jk'] ?>"
                                                <?= $jk['id_jk'] == $pendaftar['id_jk'] ? 'selected' : '' ?>>
                                                <?= htmlspecialchars($jk['jeniskelamin']) ?>
                                            </option>
                                        <?php endwhile; ?>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="id_agama" class="form-label">Agama <span class="required">*</span></label>
                                    <select class="form-select" id="id_agama" name="id_agama" required>
                                        <option value="">Pilih Agama</option>
                                        <?php
                                        $agama->data_seek(0);
                                        while ($ag = $agama->fetch_assoc()):
                                        ?>
                                            <option value="<?= $ag['id_agama'] ?>"
                                                <?= $ag['id_agama'] == $pendaftar['id_agama'] ? 'selected' : '' ?>>
                                                <?= htmlspecialchars($ag['nama_agama']) ?>
                                            </option>
                                        <?php endwhile; ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="no_handphone" class="form-label">No. Handphone <span class="required">*</span></label>
                                    <input type="tel" class="form-control" id="no_handphone" name="no_handphone"
                                        value="<?= htmlspecialchars($pendaftar['no_handphone']) ?>"
                                        placeholder="Contoh: 08123456789" required>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="email" class="form-label">Email <span class="required">*</span></label>
                                    <input type="email" class="form-control" id="email" name="email"
                                        value="<?= htmlspecialchars($pendaftar['email']) ?>"
                                        placeholder="contoh@email.com" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="asal_sekolah" class="form-label">Asal Sekolah <span class="required">*</span></label>
                                    <input type="text" class="form-control" id="asal_sekolah" name="asal_sekolah"
                                        value="<?= htmlspecialchars($pendaftar['asal_sekolah']) ?>"
                                        placeholder="Nama SMP/MTs asal" required>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="pilih_jurusan" class="form-label">Pilihan Jurusan <span class="required">*</span></label>
                                    <select class="form-select" id="pilih_jurusan" name="pilih_jurusan" required>
                                        <option value="">Pilih Jurusan</option>
                                        <option value="Bisnis Daring" <?= $pendaftar['pilih_jurusan'] == 'Bisnis Daring' ? 'selected' : '' ?>>Bisnis Daring</option>
                                        <option value="Otomisasi Dan Tata Kelola Perkantoran" <?= $pendaftar['pilih_jurusan'] == 'Otomisasi Dan Tata Kelola Perkantoran' ? 'selected' : '' ?>>Otomisasi Dan Tata Kelola Perkantoran (OTKP)</option>
                                        <option value="Akuntansi dan Keuangan Lembaga" <?= $pendaftar['pilih_jurusan'] == 'Akuntansi dan Keuangan Lembaga' ? 'selected' : '' ?>>Akuntansi dan Keuangan Lembaga (AKL)</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="alamat" class="form-label">Alamat Lengkap <span class="required">*</span></label>
                                    <textarea class="form-control" id="alamat" name="alamat" rows="3"
                                        placeholder="Masukkan alamat lengkap" required><?= htmlspecialchars($pendaftar['alamat']) ?></textarea>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Data Orang Tua -->
                    <div class="form-section">
                        <h6 class="section-title">
                            <i class="fas fa-users me-2"></i>
                            Data Orang Tua
                        </h6>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="nama_ayah" class="form-label">Nama Ayah <span class="required">*</span></label>
                                    <input type="text" class="form-control" id="nama_ayah" name="nama_ayah"
                                        value="<?= $data_ortu ? htmlspecialchars($data_ortu['nama_ayah']) : '' ?>" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="pekerjaan_ayah" class="form-label">Pekerjaan Ayah <span class="required">*</span></label>
                                    <input type="text" class="form-control" id="pekerjaan_ayah" name="pekerjaan_ayah"
                                        value="<?= $data_ortu ? htmlspecialchars($data_ortu['pekerjaan_ayah']) : '' ?>" required>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="nama_ibu" class="form-label">Nama Ibu <span class="required">*</span></label>
                                    <input type="text" class="form-control" id="nama_ibu" name="nama_ibu"
                                        value="<?= $data_ortu ? htmlspecialchars($data_ortu['nama_ibu']) : '' ?>" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="pekerjaan_ibu" class="form-label">Pekerjaan Ibu <span class="required">*</span></label>
                                    <input type="text" class="form-control" id="pekerjaan_ibu" name="pekerjaan_ibu"
                                        value="<?= $data_ortu ? htmlspecialchars($data_ortu['pekerjaan_ibu']) : '' ?>" required>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Tombol Submit -->
                    <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                        <a href="pendaftar_dashboard.php" class="btn btn-secondary me-md-2">
                            <i class="fas fa-times me-2"></i>
                            Batal
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-2"></i>
                            Simpan Perubahan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Validasi form
        document.getElementById('editForm').addEventListener('submit', function(e) {
            const requiredFields = this.querySelectorAll('[required]');
            let isValid = true;

            requiredFields.forEach(field => {
                if (!field.value.trim()) {
                    field.classList.add('is-invalid');
                    isValid = false;
                } else {
                    field.classList.remove('is-invalid');
                }
            });

            // Validasi email
            const email = document.getElementById('email');
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (email.value && !emailRegex.test(email.value)) {
                email.classList.add('is-invalid');
                isValid = false;
            }

            // Validasi nomor handphone
            const hp = document.getElementById('no_handphone');
            const hpRegex = /^[0-9+\-\s]{10,15}$/;
            if (hp.value && !hpRegex.test(hp.value)) {
                hp.classList.add('is-invalid');
                isValid = false;
            }

            if (!isValid) {
                e.preventDefault();
                alert('Mohon lengkapi semua field yang wajib diisi dengan benar!');
            }
        });

    </script>
</body>

</html>