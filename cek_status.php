<?php
// PHP logic for fetching status remains the same from previous version
$nama_siswa_display = '';
$id_pendaftaran_display = '';
$jurusan_display = '';
$current_status_display = 'Data tidak ditemukan.';
$status_card_class = 'border-warning'; // default
$status_alert_class = 'alert-warning';
$detailed_message = '<p>Pastikan Nomor Pendaftaran dan NISN/Email yang Anda masukkan sudah benar.</p>';

if (isset($_POST['cek_status_submit'])) {
    include 'db_config.php';
    $id_pendaftaran_input = $_POST['id_pendaftaran'];
    $nisn_email_input = $_POST['nisn_email'];

    if (empty($id_pendaftaran_input) || empty($nisn_email_input)) {
        $current_status_display = 'Input Tidak Lengkap';
        $detailed_message = "<p>Nomor Pendaftaran dan NISN/Email wajib diisi.</p>";
    } else {
        $id_pendaftaran = filter_var($id_pendaftaran_input, FILTER_SANITIZE_NUMBER_INT);
        $id_pendaftaran_display = htmlspecialchars($id_pendaftaran_input);

        $stmt = $conn->prepare("SELECT nama_siswa, nisn, email, status_pendaftaran, pilih_jurusan FROM pendaftaran WHERE id_pendaftaran = ? AND (nisn = ? OR email = ?)");
        if (false === $stmt) {
            $current_status_display = 'Error Sistem';
            $detailed_message = "<p>Terjadi kesalahan pada sistem. Silakan coba lagi nanti.</p>";
            error_log("Prepare failed in cek_status: (" . $conn->errno . ") " . $conn->error);
        } else {
            $stmt->bind_param("iss", $id_pendaftaran, $nisn_email_input, $nisn_email_input);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                $data = $result->fetch_assoc();
                $current_status_raw = $data['status_pendaftaran'] ?? 'Belum Ada Status';
                $current_status_display = htmlspecialchars($current_status_raw);
                $nama_siswa_display = htmlspecialchars($data['nama_siswa']);
                $jurusan_display = htmlspecialchars($data['pilih_jurusan']);

                // Determine card and alert classes based on status
                switch (strtolower(str_replace(' ', '-', $current_status_raw))) {
                    case 'accepted':
                        $status_card_class = 'border-success shadow';
                        $status_alert_class = 'alert-success';
                        $detailed_message  = "<p class='fw-bold'>Selamat! Anda telah diterima di SMK ArdsentrA jurusan " . $jurusan_display . ".</p>";
                        $detailed_message .= "<p>Langkah selanjutnya adalah melakukan proses <strong>Daftar Ulang</strong>. Harap perhatikan jadwal dan persyaratan berikut:</p>";
                        $detailed_message .= "<ul><li>Jadwal Daftar Ulang: <strong>15 Juli " . date("Y") . " - 20 Juli " . date("Y") . "</strong></li><li>Tempat: Ruang Tata Usaha SMK ArdsentrA.</li><li>Membawa bukti pendaftaran ini.</li></ul>";
                        break;
                    case 'rejected':
                        $status_card_class = 'border-danger shadow';
                        $status_alert_class = 'alert-danger';
                        $detailed_message = "<p class='fw-bold'>Mohon maaf, Anda belum dapat kami terima saat ini.</p><p>Jangan berkecil hati dan tetap semangat! Terima kasih atas partisipasinya.</p>";
                        break;
                    case 'documents-incomplete':
                        $status_card_class = 'border-warning shadow';
                        $status_alert_class = 'alert-warning';
                        $detailed_message = "<p class='fw-bold'>Status dokumen Anda belum lengkap atau perlu diverifikasi ulang.</p><p>Mohon segera menghubungi panitia SPMB SMK ArdsentrA. Kontak: Telp (021) 123456 / Email: spmb@smkardsentra.sch.id. Batas waktu melengkapi: <strong>10 Juli " . date("Y") . "</strong>.</p>";
                        break;
                    case 'waiting-list':
                        $status_card_class = 'border-info shadow';
                        $status_alert_class = 'alert-info';
                        $detailed_message = "<p class='fw-bold'>Anda saat ini berada dalam daftar tunggu untuk jurusan " . $jurusan_display . ".</p><p>Keputusan final akan diumumkan setelah proses daftar ulang selesai, sekitar tanggal <strong>25 Juli " . date("Y") . "</strong>. Harap memantau informasi secara berkala.</p>";
                        break;
                    case 'documents-verified':
                        $status_card_class = 'border-primary shadow';
                        $status_alert_class = 'alert-primary';
                        $detailed_message = "<p class='fw-bold'>Dokumen Anda telah diverifikasi.</p><p>Pendaftaran Anda sedang dalam proses seleksi akhir. Pengumuman hasil seleksi: <strong>10 Juli " . date("Y") . "</strong>. Harap bersabar.</p>";
                        break;
                    case 'pending-review':
                    default:
                        $status_card_class = 'border-secondary shadow';
                        $status_alert_class = 'alert-secondary';
                        $detailed_message = "<p class='fw-bold'>Pendaftaran Anda telah diterima dan sedang dalam proses peninjauan awal.</p><p>Mohon tunggu informasi selanjutnya mengenai verifikasi dokumen dan jadwal seleksi.</p>";
                        // Tambahan: Ambil status verifikasi dokumen
                        $stmt_dokumen = $conn->prepare("SELECT status_verifikasi, catatan_admin FROM dokumen WHERE id_pendaftaran = ?");
                        $stmt_dokumen->bind_param("i", $id_pendaftaran);
                        $stmt_dokumen->execute();
                        $res_dokumen = $stmt_dokumen->get_result();

                        if ($res_dokumen && $res_dokumen->num_rows > 0) {
                            $dok = $res_dokumen->fetch_assoc();
                            $status_dokumen = $dok['status_verifikasi'] ?? 'Menunggu';
                            $catatan_dokumen = $dok['catatan_admin'] ?? null;

                            $detailed_message .= "<hr><p class='mb-1'><strong>Status Verifikasi Dokumen:</strong> $status_dokumen</p>";

                            if (!empty($catatan_dokumen) && strtolower($status_dokumen) === 'ditolak') {
                                $detailed_message .= "<div class='mt-2 alert alert-danger p-2'><strong>Catatan Panitia:</strong><br>" . nl2br(htmlspecialchars($catatan_dokumen)) . "</div>";
                            }
                        }
                        $stmt_dokumen->close();
                        break;
                }
            } else {
                // Data tidak ditemukan, $detailed_message sudah di set di awal
            }
            if (isset($stmt)) $stmt->close();
        }
        if (isset($conn)) $conn->close();
    }
}
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cek Status Pendaftaran - SPMB SMK ArdsentrA</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            background-color: #f4f7f6;
            font-family: 'Poppins', sans-serif;
        }

        /* === CSS Kustom untuk Navbar Oranye === */
        .navbar-custom-orange {
            background-color: #fd7e14 !important;
            /* Warna oranye kustom */
        }

        /* === Akhir CSS Kustom === */

        .status-check-container {
            max-width: 650px;
            margin: 50px auto;
        }

        .status-card {
            border-width: 3px !important;
        }

        /* Agar border lebih terlihat */
        .print-button {
            transition: all 0.2s ease;
        }

        .print-button:hover {
            transform: scale(1.05);
        }

        @media print {
            body * {
                visibility: hidden;
            }

            #statusPrintArea,
            #statusPrintArea * {
                visibility: visible;
            }

            #statusPrintArea {
                position: absolute;
                left: 0;
                top: 0;
                width: 100%;
            }

            .no-print {
                display: none !important;
            }

            .card {
                box-shadow: none !important;
                border: 1px solid #dee2e6 !important;
            }
        }
    </style>
</head>

<body>
    <nav class="navbar navbar-expand-lg navbar-dark navbar-custom-orange shadow-sm no-print">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center" href="index.php">
                <img src="img/logo.png" alt="Logo SMK ArdsentrA" height="40" class="me-2">
                <span class="fw-bold text-white">SPMB SMK ArdsentrA</span>
            </a>
            <ul class="navbar-nav ms-auto">
                <li class="nav-item"><a class="nav-link" href="index.php"><i class="bi bi-house-door me-1"></i>Beranda</a></li>
                <li class="nav-item"><a class="nav-link" href="tentang_sekolah.php"><i class="bi bi-file-person"></i>Tentang Kami</a></li>
                <li class="nav-item"><a class="nav-link" href="galeri.php"><i class="bi bi-images me-1"></i>Galeri</a></li>
                <li class="nav-item"><a class="nav-link" href="register.php"><i class="bi bi-person-plus me-1"></i>Daftar</a></li>
                <li class="nav-item"><a class="nav-link active" aria-current="page" href="cek_status.php"><i class="bi bi-patch-check me-1"></i>Cek Status</a></li>
                <li class="nav-item"><a class="nav-link" href="login.php"><i class="bi bi-box-arrow-in-right me-1"></i>Login</a></li>
            </ul>
        </div>
    </nav>

    <div class="container status-check-container">
        <div class="card shadow-lg no-print">
            <div class="card-body p-lg-5 p-4">
                <h2 class="text-center mb-4 fw-bold text-warning">Cek Status Pendaftaran Anda</h2>
                <form method="POST" action="cek_status.php">
                    <div class="mb-3">
                        <label for="id_pendaftaran" class="form-label">Nomor Pendaftaran <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-person-vcard"></i></span>
                            <input type="text" class="form-control form-control-lg" id="id_pendaftaran" name="id_pendaftaran" required value="<?php echo isset($_POST['id_pendaftaran']) ? htmlspecialchars($_POST['id_pendaftaran']) : ''; ?>">
                        </div>
                    </div>
                    <div class="mb-4">
                        <label for="nisn_email" class="form-label">NISN atau Email (yang didaftarkan) <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-key-fill"></i></span>
                            <input type="text" class="form-control form-control-lg" id="nisn_email" name="nisn_email" required value="<?php echo isset($_POST['nisn_email']) ? htmlspecialchars($_POST['nisn_email']) : ''; ?>">
                        </div>
                    </div>
                    <button type="submit" name="cek_status_submit" class="btn btn-success btn-lg w-100 py-2"><i class="bi bi-search me-2"></i>Cek Status</button>
                </form>
            </div>
        </div>

        <?php if (isset($_POST['cek_status_submit'])): ?>
            <div id="statusPrintArea" class="mt-4">
                <div class="card status-card <?php echo $status_card_class; ?>">
                    <div class="card-header bg-transparent text-center py-3">
                        <h3 class="mb-0 text-dark">
                            <?php if (!empty($nama_siswa_display)): ?>
                                Status Pendaftaran: <?php echo $nama_siswa_display; ?>
                            <?php else: ?>
                                Hasil Pengecekan Status
                            <?php endif; ?>
                        </h3>
                    </div>
                    <div class="card-body p-4">
                        <?php if (!empty($id_pendaftaran_display)): ?>
                            <p class="fs-5">Nomor Pendaftaran: <strong class="text-dark"><?php echo $id_pendaftaran_display; ?></strong></p>
                        <?php endif; ?>
                        <?php if (!empty($jurusan_display)): ?>
                            <p class="fs-5">Jurusan Pilihan: <strong class="text-dark"><?php echo $jurusan_display; ?></strong></p>
                        <?php endif; ?>
                        <hr class="my-3">
                        <p class="display-6 mb-1">Status: <span class="fw-bolder"><?php echo $current_status_display; ?></span></p>

                        <div class="alert <?php echo $status_alert_class; ?> mt-3 mb-0">
                            <h5 class="alert-heading fw-semibold"><i class="bi bi-info-circle-fill me-2"></i>Informasi Tambahan:</h5>
                            <?php echo $detailed_message; ?>
                        </div>
                    </div>
                </div>
            </div>
            <div class='print-button-container mt-4 text-center no-print'>
                <button onclick='window.print();' class='btn btn-lg btn-outline-secondary print-button'>
                    <i class="bi bi-printer-fill me-2"></i>Cetak Halaman Ini
                </button>
            </div>
        <?php endif; ?>
        <div class="text-center mt-5 no-print">
            <a href="index.php" class="btn btn-link text-secondary"><i class="bi bi-arrow-left-circle me-1"></i>Kembali ke Beranda</a>
        </div>
    </div>

    <footer class="py-4 mt-5 bg-light text-center no-print">
        <p class="mb-0 text-muted small">&copy; <?php echo date("Y"); ?> SPMB SMK ArdsentrA. All Rights Reserved.</p>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>