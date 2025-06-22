<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SPMB SMK ArdsentrA - Selamat Datang!</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            padding-top: 50px;
            font-family: 'Poppins', sans-serif;
            background-color: #f4f7f6;
        }

        .navbar-custom-orange {
            background-color: #fd7e14 !important;
        }

        .navbar-brand img {
            height: 40px;
        }

        .hero-section {
            background: linear-gradient(rgba(0, 86, 179, 0.8), rgba(139, 51, 0, 0.85)), url('https://images.unsplash.com/photo-1580582932707-520aed93a549?ixlib=rb-4.0.3&ixid=MnwxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8&auto=format&fit=crop&w=1920&q=80') no-repeat center center;
            background-size: cover;
            color: white;
            padding: 8rem 2rem;
            margin-bottom: 3rem;
            text-align: center;
            overflow: hidden;
            /* Menyembunyikan teks saat di luar area */
            position: relative;
        }

        .hero-section h1 {
            font-weight: 700;
            font-size: calc(2.5rem + 2vw);
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.5);
        }

        /* === CSS untuk Teks Bergerak === */
        .hero-section h1.scrolling-text {
            animation-name: scroll-left-to-right;
            animation-duration: 10s;
            /* Kecepatan animasi (semakin besar, semakin lambat) */
            animation-timing-function: linear;
            animation-iteration-count: infinite;
            white-space: nowrap;
            display: inline-block;
        }

        @keyframes scroll-left-to-right {
            0% {
                transform: translateX(-100%);
            }

            100% {
                transform: translateX(100%);
            }
        }

        /* === Akhir CSS Teks Bergerak === */

        .hero-section .lead {
            font-size: calc(1rem + 0.5vw);
            margin-bottom: 2.5rem;
            max-width: 700px;
            margin-left: auto;
            margin-right: auto;
            text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.3);
        }

        /* === CSS untuk Tombol Daftar Sekarang === */
        .btn-custom-register {
            background-color: #ffffff;
            color: #fd7e14;
            border: 2px solid #fd7e14;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .btn-custom-register:hover,
        .btn-custom-register:focus,
        .btn-custom-register:active {
            background-color: #fd7e14;
            color: #ffffff;
            border-color: #e86100;
            box-shadow: 0 0 15px rgba(253, 126, 20, 0.5);
        }

        /* === Akhir CSS Tombol === */

        .section-title {
            text-align: center;
            margin-bottom: 3rem;
            font-weight: 700;
            color: #fd7e14;
            /* Warna judul section oranye */
            position: relative;
            padding-bottom: 1rem;
        }

        .section-title::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 50%;
            transform: translateX(-50%);
            width: 80px;
            height: 4px;
            background-color: #fd7e14;
            /* Aksen underline oranye */
            border-radius: 2px;
        }

        .info-card {
            transition: transform .3s ease, box-shadow .3s ease;
            border: 1px solid #e0e0e0;
            border-radius: 0.75rem;
            background-color: #fff;
        }

        .info-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
        }

        .info-card .card-body {
            padding: 2rem;
        }

        .info-card .card-title i {
            margin-right: 10px;
        }

        .info-card .list-unstyled li {
            padding: 0.3rem 0;
        }

        .alur-item {
            transition: background-color 0.3s ease;
            padding: 1.5rem;
            border-radius: 0.5rem;
        }

        .alur-item:hover {
            background-color: #e9ecef;
        }

        .alur-item i {
            display: block;
            margin-bottom: 0.5rem;
        }

        .footer {
            background-color: #2c3e50;
            color: #ecf0f1;
            padding: 3rem 0;
            margin-top: 4rem;
        }

        .footer h5 {
            color: white;
            margin-bottom: 1rem;
        }

        .footer a {
            color: #bdc3c7;
            text-decoration: none;
        }

        .footer a:hover {
            color: #fd7e14;
            text-decoration: underline;
        }

        .footer .social-icons a {
            font-size: 1.5rem;
            margin: 0 0.5rem;
            transition: color 0.3s ease;
        }
    </style>
</head>

<body>
    <nav class="navbar navbar-expand-lg navbar-dark navbar-custom-orange fixed-top shadow-sm">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center" href="index.php">
                <img src="img/logo.png" alt="Logo SMK ArdsentrA" height="40" class="me-2">
                <span class="fw-bold text-white">SPMB SMK ArdsentrA</span>
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto fw-semibold">
                    <li class="nav-item"><a class="nav-link active" aria-current="page" href="index.php"><i class="bi bi-house-door me-1"></i>Beranda</a></li>
                    <li class="nav-item"><a class="nav-link" href="tentang_sekolah.php"><i class="bi bi-file-person"></i>Tentang Kami</a></li>
                    <li class="nav-item"><a class="nav-link" href="galeri.php"><i class="bi bi-images me-1"></i>Galeri</a></li>
                    <li class="nav-item"><a class="nav-link" href="register.php"><i class="bi bi-person-plus me-1"></i>Daftar</a></li>
                    <li class="nav-item"><a class="nav-link" href="#info-pendaftaran"><i class="bi bi-info-circle me-1"></i>Informasi</a></li>
                    <li class="nav-item"><a class="nav-link" href="cek_status.php"><i class="bi bi-patch-check me-1"></i>Cek Status</a></li>
                    <li class="nav-item"><a class="nav-link" href="login.php"><i class="bi bi-box-arrow-in-right me-1"></i>Login</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container-fluid p-0">
        <section class="hero-section">
            <div style="margin: 0 15%; overflow: hidden;">
                <h1 class="display-3 scrolling-text">SPMB Online SMK ArdsentrA <?php echo date("Y"); ?></h1>
            </div>
            <p class="lead">Wujudkan Potensi, Raih Prestasi Bersama Kami! Sekolah pilihan untuk masa depan gemilang.</p>
            <p class="lead">SPP GRATIS 3 TAHUN DAN RUANG KELAS FULL AC</p>

            <a href="register.php" class="btn btn-custom-register btn-lg px-5 py-3 fs-5 shadow-lg">
                <i class="bi bi-pencil-square me-2"></i>Daftar Sekarang!
            </a>
        </section>
    </div>

    <div class="container my-5 py-4" id="info-pendaftaran">
        <h2 class="section-title display-5">Informasi Pendaftaran</h2>
        <div class="row g-4">
            <div class="col-md-6 col-lg-4">
                <div class="card info-card h-100">
                    <div class="card-body text-center">
                        <i class="bi bi-calendar2-week-fill fs-1 text-primary mb-3"></i>
                        <h5 class="card-title fw-bold">Jadwal Penting</h5>
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item">Pendaftaran: <strong>1 - 30 Juni <?php echo date("Y"); ?></strong></li>
                            <li class="list-group-item">Seleksi Berkas: <strong>1 - 5 Juli <?php echo date("Y"); ?></strong></li>
                            <li class="list-group-item">Pengumuman Hasil: <strong>10 Juli <?php echo date("Y"); ?></strong></li>
                            <li class="list-group-item">Daftar Ulang: <strong>15 - 20 Juli <?php echo date("Y"); ?></strong></li>
                        </ul>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-lg-4">
                <div class="card info-card h-100">
                    <div class="card-body text-center">
                        <i class="bi bi-card-checklist fs-1 text-success mb-3"></i>
                        <h5 class="card-title fw-bold">Persyaratan Umum</h5>
                        <ul class="list-unstyled text-start">
                            <li><i class="bi bi-check-lg text-success me-2"></i>NISN Valid & Aktif</li>
                            <li><i class="bi bi-check-lg text-success me-2"></i>Scan Ijazah/SKL Asli</li>
                            <li><i class="bi bi-check-lg text-success me-2"></i>Pas Foto 3x4</li>
                            <li><i class="bi bi-check-lg text-success me-2"></i>Scan Kartu Keluarga Terbaru</li>
                            <li><i class="bi bi-check-lg text-success me-2"></i>Scan KTP Orang Tua/Wali</li>
                        </ul>
                    </div>
                </div>
            </div>
            <div class="col-md-12 col-lg-4">
                <div class="card info-card h-100">
                    <div class="card-body text-center">
                        <i class="bi bi-mortarboard-fill fs-1 text-info mb-3"></i>
                        <h5 class="card-title fw-bold">Program Keahlian</h5>
                        <ul class="list-unstyled text-start">
                            <li><i class="bi bi-wallet text-info me-2"></i>Bisnis Daring</li>
                            <li><i class="bi bi-building text-info me-2"></i>Otomisasi Dan Tata Kelola Perkantoran (OTKP)</li>
                            <li><i class="bi bi-bank text-info me-2"></i>Akuntansi & Keuangan Lembaga (AKL)</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="container my-5 py-4 bg-light rounded shadow-sm">
        <h2 class="section-title display-5 text-center mb-5">Alur Pendaftaran</h2>
        <div class="row text-center g-md-4 g-3 justify-content-center">
            <div class="col-lg-2 col-md-4 col-sm-6 alur-item">
                <div class="p-3 h-100 d-flex flex-column justify-content-center">
                    <i class="bi bi-input-cursor-text fs-1 text-warning mb-3"></i>
                    <h6 class="fw-semibold">1. Isi Formulir</h6>
                    <p class="text-muted small">Lengkapi Semua Data Diri, Orang tua, Dan Asal Sekolah Dengan Benar.</p>
                </div>
            </div>
            <div class="col-lg-2 col-md-4 col-sm-6 alur-item">
                <div class="p-3 h-100 d-flex flex-column justify-content-center">
                    <i class="bi bi-box-arrow-in-right fs-1 text-warning mb-3"></i>
                    <h6 class="fw-semibold">2. Login</h6>
                    <p class="text-muted small">Masuk Ke Akun Anda Dengan NISN Dan Tanggal Lahir Untuk Mengakses Dashboard Pendaftaran.</p>
                </div>
            </div>
            <div class="col-lg-2 col-md-4 col-sm-6 alur-item">
                <div class="p-3 h-100 d-flex flex-column justify-content-center">
                    <i class="bi bi-file-earmark-check fs-1 text-warning mb-3"></i>
                    <h6 class="fw-semibold">3. Cek Berkas</h6>
                    <p class="text-muted small">Periksa Kelengkapan Data Diri Dan Dokumen Yang Telah Anda Upload.</p>
                </div>
            </div>
            <div class="col-lg-2 col-md-4 col-sm-6 alur-item">
                <div class="p-3 h-100 d-flex flex-column justify-content-center">
                    <i class="bi bi-patch-question-fill fs-1 text-warning mb-3"></i>
                    <h6 class="fw-semibold">4. Cek Status</h6>
                    <p class="text-muted small">Pantau Status Pendaftaran Anda Secara Berkala Melalui Dashboard Atau Melalui Website</p>
                </div>
            </div>
            <div class="col-lg-2 col-md-4 col-sm-6 alur-item">
                <div class="p-3 h-100 d-flex flex-column justify-content-center">
                    <i class="bi bi-envelope-at fs-1 text-warning mb-3"></i>
                    <h6 class="fw-semibold">6. Notifikasi Email</h6>
                    <p class="text-muted small">Terima Pemberitahuan Hasil Pendaftaran Melalui Email.</p>
                </div>
            </div>
        </div>
    </div>

    <footer class="footer">
        <div class="container">
            <div class="row">
                <div class="col-md-4 mb-3">
                    <h5>SMK ArdsentrA</h5>
                    <p class="small">SMK ArdsentrA berkomitmen untuk menyediakan pendidikan kejuruan berkualitas yang relevan dengan kebutuhan industri, mencetak lulusan yang siap kerja dan berdaya saing global.</p>
                </div>
                <div class="col-md-4 mb-3">
                    <h5>Kontak Kami</h5>
                    <ul class="list-unstyled small">
                        <li><i class="bi bi-geo-alt-fill me-2"></i>Jl. Raya Citayam (Tugu Macan), RT 02/RW 03, Desa Ragajaya, Kecamatan Bojonggede, Kabupaten Bogor, Jawa Barat</li>
                        <li><i class="bi bi-telephone-fill me-2"></i>0895-4159-73545</li>
                        <li><i class="bi bi-envelope-fill me-2"></i>bardsentra@yahoo.com</li>
                    </ul>
                </div>
                <div class="col-md-4 mb-3">
                    <h5>Ikuti Kami</h5>
                    <div class="social-icons">
                        <a href="" title="Facebook"><i class="bi bi-facebook"></i></a>
                        <a href="https://www.instagram.com/smk_ardsentra_2024/" title="Instagram"><i class="bi bi-instagram"></i></a>
                        <a href="https://www.youtube.com/@penyejuk-n6t" title="YouTube"><i class="bi bi-youtube"></i></a>
                    </div>
                </div>
            </div>
            <hr class="my-3" style="border-color: #4A5A6A;">
            <p class="text-center small mb-0">&copy; <?php echo date("Y"); ?> SPMB SMK ArdsentrA. Hak Cipta Dilindungi.</p>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
    <script>
        // Script JavaScript untuk scroll navbar tidak diperlukan lagi untuk mengubah warna
    </script>
</body>

</html>