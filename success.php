<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pendaftaran Berhasil - PPDB SMK XYZ</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
</head>
<body>
<nav class="navbar navbar-dark bg-primary">
    <div class="container">
        <a class="navbar-brand fw-bold" href="index.php"><i class="bi bi-building-fill-check me-2"></i>PPDB SMK XYZ</a>
    </div>
</nav>

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-lg text-center border-success">
                <div class="card-body p-5">
                    <i class="bi bi-check-circle-fill display-1 text-success mb-3"></i>
                    <h2 class="card-title display-5 fw-bold">Pendaftaran Berhasil!</h2>

                    <?php
                    if (isset($_GET['id']) && !empty($_GET['id'])) {
                        $id_pendaftaran = htmlspecialchars($_GET['id']);
                        echo "<p class='lead'>Terima kasih telah mendaftar. Nomor pendaftaran Anda adalah:</p>";
                        echo "<h1 class='display-4 my-3 bg-light p-3 rounded'>" . $id_pendaftaran . "</h1>";
                        echo "<p class='fw-bold'>Mohon simpan dan catat nomor pendaftaran ini untuk melakukan pengecekan status.</p>";

                        include_once 'db_config.php';
                        $stmt = $conn->prepare("SELECT p.nama_siswa, p.nisn, p.pilih_jurusan, d.status_verifikasi 
                            FROM pendaftaran p 
                            LEFT JOIN dokumen d ON p.id_pendaftaran = d.id_pendaftaran 
                            WHERE p.id_pendaftaran = ?");
                        $stmt->bind_param("i", $id_pendaftaran);
                        $stmt->execute();
                        $result = $stmt->get_result();

                        if ($result && $result->num_rows > 0) {
                            $data = $result->fetch_assoc();

                            echo "<div class='table-responsive mt-4'>";
                            echo "<table class='table table-bordered'>";
                            echo "<tr><th>Nama Siswa</th><td>{$data['nama_siswa']}</td></tr>";
                            echo "<tr><th>NISN</th><td>{$data['nisn']}</td></tr>";
                            echo "<tr><th>Jurusan Pilihan</th><td>{$data['pilih_jurusan']}</td></tr>";

                            $verifikasi = $data['status_verifikasi'] ?? 'Menunggu';
                            $badgeClass = ($verifikasi === 'Diverifikasi') ? 'success' : (($verifikasi === 'Ditolak') ? 'danger' : 'secondary');
                            echo "<tr><th>Status Verifikasi Dokumen</th><td><span class='badge bg-$badgeClass'>$verifikasi</span></td></tr>";

                            echo "</table>";
                            echo "</div>";
                        }
                        $stmt->close();

                        echo "<div class='alert alert-info mt-4 text-start'>";
                        echo "<h5><i class='bi bi-info-circle-fill'></i> Informasi Login</h5>";
                        echo "<p>Anda dapat login ke sistem untuk melihat status pendaftaran Anda dengan:</p>";
                        echo "<ul>";
                        echo "<li><strong>Username:</strong> NISN Anda (<code>{$data['nisn']}</code>)</li>";
                        echo "<li><strong>Password:</strong> Tanggal lahir dalam format <code>ddmmyyyy</code></li>";
                        echo "</ul>";
                        echo "</div>";
                    } else {
                        echo "<p class='lead'>Terima kasih telah mendaftar. Silakan tunggu informasi selanjutnya.</p>";
                    }
                    ?>

                    <a href="index.php" class="btn btn-primary btn-lg mt-4"><i class="bi bi-house-door me-1"></i>Kembali ke Beranda</a>
                </div>
            </div>
        </div>
    </div>
</div>

<footer class="py-4 mt-5 bg-light text-center">
    <p class="mb-0 text-muted small">&copy; <?php echo date("Y"); ?> PPDB SMK XYZ.</p>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
