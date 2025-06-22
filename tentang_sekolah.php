<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tentang Kami - SMK ArdsentrA</title>
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
            background: linear-gradient(rgba(0, 86, 179, 0.8), rgba(139, 51, 0, 0.85)), url('https://images.unsplash.com/photo-1523050854058-8df90110c9d1?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=1920&q=80') no-repeat center center;
            background-size: cover;
            color: white;
            padding: 6rem 2rem;
            margin-bottom: 3rem;
            text-align: center;
        }

        .hero-section h1 {
            font-weight: 700;
            font-size: calc(2.5rem + 2vw);
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.5);
            margin-bottom: 1rem;
        }

        .hero-section .lead {
            font-size: calc(1rem + 0.5vw);
            margin-bottom: 2.5rem;
            max-width: 700px;
            margin-left: auto;
            margin-right: auto;
            text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.3);
        }

        .section-title {
            text-align: center;
            margin-bottom: 3rem;
            font-weight: 700;
            color: #fd7e14;
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
            border-radius: 2px;
        }

        .content-card {
            background: white;
            border-radius: 15px;
            padding: 2.5rem;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            margin-bottom: 2rem;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .content-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 40px rgba(0, 0, 0, 0.15);
        }

        .visi-misi-card {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 15px;
            padding: 2.5rem;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
            margin-bottom: 2rem;
        }

        .visi-misi-card h3 {
            color: #ffffff;
            font-weight: 700;
            margin-bottom: 1.5rem;
        }

        .sambutan-card {
            background: linear-gradient(135deg, #ffecd2 0%, #fcb69f 100%);
            border-radius: 15px;
            padding: 2.5rem;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            margin-bottom: 2rem;
        }

        .kepala-sekolah-img {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            object-fit: cover;
            border: 4px solid #fd7e14;
            margin-bottom: 1rem;
        }

        .stats-section {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 4rem 0;
            margin: 4rem 0;
        }

        .stats-item {
            text-align: center;
            padding: 1rem;
        }

        .stats-number {
            font-size: 3rem;
            font-weight: 700;
            display: block;
            margin-bottom: 0.5rem;
        }

        .stats-label {
            font-size: 1.1rem;
            opacity: 0.9;
        }

        .timeline {
            position: relative;
            padding: 2rem 0;
        }

        .timeline::before {
            content: '';
            position: absolute;
            left: 50%;
            top: 0;
            bottom: 0;
            width: 4px;
            background: #fd7e14;
            transform: translateX(-50%);
        }

        .timeline-item {
            position: relative;
            margin-bottom: 3rem;
            padding: 0 2rem;
        }

        .timeline-item:nth-child(odd) {
            text-align: right;
            padding-right: 50%;
        }

        .timeline-item:nth-child(even) {
            text-align: left;
            padding-left: 50%;
        }

        .timeline-marker {
            position: absolute;
            left: 50%;
            top: 0;
            width: 20px;
            height: 20px;
            background: #fd7e14;
            border-radius: 50%;
            transform: translateX(-50%);
            border: 4px solid white;
            box-shadow: 0 0 0 4px #fd7e14;
        }

        .timeline-content {
            background: white;
            padding: 1.5rem;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }

        .timeline-year {
            font-size: 1.2rem;
            font-weight: 700;
            color: #fd7e14;
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

        @media (max-width: 768px) {
            .timeline::before {
                left: 20px;
            }

            .timeline-item {
                padding-left: 50px;
                padding-right: 1rem;
                text-align: left;
            }

            .timeline-item:nth-child(odd) {
                padding-left: 50px;
                padding-right: 1rem;
                text-align: left;
            }

            .timeline-marker {
                left: 20px;
            }
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
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto fw-semibold">
                    <li class="nav-item"><a class="nav-link" href="index.php"><i class="bi bi-house-door me-1"></i>Beranda</a></li>
                    <li class="nav-item"><a class="nav-link active" aria-current="page" href="tentang_sekolah.php"><i class="bi bi-file-person"></i>Tentang Kami</a></li>
                    <li class="nav-item"><a class="nav-link" href="galeri.php"><i class="bi bi-images me-1"></i>Galeri</a></li>
                    <li class="nav-item"><a class="nav-link" href="register.php"><i class="bi bi-person-plus me-1"></i>Daftar</a></li>
                    <li class="nav-item"><a class="nav-link" href="cek_status.php"><i class="bi bi-patch-check me-1"></i>Cek Status</a></li>
                    <li class="nav-item"><a class="nav-link" href="login.php"><i class="bi bi-box-arrow-in-right me-1"></i>Login</a></li>
                </ul>
            </div>
        </div>
    </nav>
    <!-- Profil Sekolah -->
    <div class="container my-5">
        <div class="content-card">
            <div class="row align-items-center">
                <div class="col-lg-6">
                    <h2 class="fw-bold text-primary mb-4">
                        <img src="img/logo.png" alt="Logo SMK ArdsentrA" height="40" class="me-2">
                        Profil SMK ArdsentrA
                    </h2>
                    <p class="text-muted mb-4" style="font-size: 1.1rem; line-height: 1.8;">
                        SMK Ardsentra adalah Sekolah Menengah Kejuruan swasta yang berdiri sejak 7 Maret 2013 dan berlokasi di Jl. Raya Citayam, Ragajaya, Bojong Gede, Kabupaten Bogor, Jawa Barat. Berada di bawah naungan Kementerian Pendidikan dan Kebudayaan Republik Indonesia, SMK Ardsentra hadir dengan komitmen untuk memberikan pendidikan kejuruan yang berkualitas dan sesuai dengan kebutuhan dunia kerja.
                    </p>
                    <p class="text-muted mb-4" style="font-size: 1.1rem; line-height: 1.8;">
                        Pada tahun 2022, SMK Ardsentra berhasil meraih Akreditasi B dari BAN-S/M sebagai bentuk pengakuan atas konsistensinya dalam menyelenggarakan pendidikan yang layak dan bermutu. Di bawah kepemimpinan Suhadi, S.Pd. selaku kepala sekolah, SMK Ardsentra terus berupaya mencetak lulusan yang kompeten, siap kerja, dan memiliki daya saing tinggi baik di dunia industri maupun dalam bidang wirausaha.
                    </p>
                    <div class="row mt-4">
                        <div class="col-sm-6">
                            <div class="d-flex align-items-center mb-3">
                                <i class="bi bi-award-fill text-warning fs-3 me-3"></i>
                                <div>
                                    <h6 class="fw-bold mb-0">Terakreditasi B</h6>
                                    <small class="text-muted">BAN-SM Nasional</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6 text-center">
                    <img src="img/fotbar.jpg" alt="SMK ArdsentrA" class="img-fluid rounded-3 shadow-lg">
                </div>
            </div>
        </div>
    </div>

    <!-- Visi Misi -->
    <div class="container my-5">
        <div class="row">
            <div class="col-lg-6 mb-4">
                <div class="visi-misi-card">
                    <h3><i class="bi bi-eye-fill me-2"></i>Visi</h3>
                    <p style="font-size: 1.1rem; line-height: 1.8;">
                        Menciptakan generasi penerus bangsa yang cerdas, terampil, mandiri, kreatif dan inovatif guna menciptakan sumber daya manusia yang handal dan berkualitas dengan tetap berpegangan teguh pada iman dan taqwa
                    </p>
                </div>
            </div>
            <div class="col-lg-6 mb-4">
                <div class="visi-misi-card">
                    <h3><i class="bi bi-flag-fill me-2"></i>Misi</h3>
                    <ul style="font-size: 1rem; line-height: 1.8;">
                        <li>Menyelenggarakan Pendidikan kejuruan yang berkualitas pada kebutuhan dunia kerja dan perkembangan teknologi</li>
                        <li>Mendorong peserta didik untuk mengembangkan potensi diri secara optimal dalam bidang akademik maupun keterampilan praktis</li>
                        <li>Menanamkan nilai-nilai keimanan dan ketakwaan sebagai landasan moral dalam setiap asepk kehidupan</li>
                        <li>Menciptakan lingkungan belajar yang kondusif, inovatif, dan mendukung pengembangan kreatifitas serta kemandirian peserta didik</li>
                        <li>Menjalin kerja sama yang erat dengan duinia industri dan dunia usaha untuk memperluas peluang kerja lulusan</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <!-- Sambutan Kepala Sekolah -->
    <div class="container my-5">
        <div class="sambutan-card">
            <div class="row align-items-center">
                <div class="col-lg-3 text-center mb-4 mb-lg-0">
                    <img src="img/kepalasklhj.jpg" alt="Kepala Sekolah" class="kepala-sekolah-img">
                    <h5 class="fw-bold mb-1">Suhadi, S.Pd.</h5>
                    <p class="text-muted mb-0">Kepala Sekolah SMK ArdsentrA</p>
                </div>
                <div class="col-lg-9">
                    <h3 class="fw-bold text-primary mb-4">
                        <i class="bi bi-chat-quote-fill me-2"></i>
                        Sambutan Kepala Sekolah
                    </h3>
                    <p class="text-dark mb-0 fw-semibold">
                        Assalamu’alaikum Warahmatullahi Wabarakatuh,
                    </p>
                    <p class="text-dark mb-3" style="font-size: 1.1rem; line-height: 1.8;">
                        Selamat datang di SMK Ardsentra, tempat terbaik untuk mempersiapkan masa depan yang cerah. Kami menghadirkan pendidikan kejuruan yang tidak hanya fokus pada teori, tetapi juga keterampilan nyata yang dibutuhkan dunia kerja dan wirausaha.
                    </p>
                    <p class="text-dark mb-3" style="font-size: 1.1rem; line-height: 1.8;">
                        Dengan bimbingan guru-guru profesional, lingkungan belajar yang nyaman, serta program unggulan berbasis industri, kami siap membentuk generasi muda yang siap kerja, kreatif, dan berdaya saing. Mari bergabung bersama kami dan wujudkan cita-citamu di SMK Ardsentra.
                    </p>
                    <p class="text-dark mb-0 fw-semibold">
                        Wassalamu'alaikum warahmatullahi wabarakatuh
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- Sejarah Singkat -->
    <div class="container my-5">
        <h2 class="section-title display-5">Sejarah Singkat</h2>
        <div class="timeline">
            <div class="timeline-item">
                <div class="timeline-marker"></div>
                <div class="timeline-content">
                    <div class="timeline-year">2013</div>
                    <h5 class="fw-bold">Pendirian SMK ArdsentrA</h5>
                    <p>Sekolah didirikan atas nama Yayasan Pendidikan Aktifitas Guru dan Dosen Nusantara, berlokasi di Jalan Raya Citayam RT. 02/03, Desa Ragajaya, Kecamatan Bojonggede, Kabupaten Bogor.</p>
                </div>
            </div>
            <div class="timeline-item">
                <div class="timeline-marker"></div>
                <div class="timeline-content">
                    <div class="timeline-year">2014-2021</div>
                    <h5 class="fw-bold">Perjalanan SMK ArdsentrA</h5>
                    <p>SMK Ardsentra mulai aktif melaksanakan kegiatan pendidikan vokasi dan berfokus pada penguatan kurikulum, peningkatan kualitas tenaga pendidik, dan pembentukan jaringan kerjasama dengan dunia usaha dan dunia industri</p>
                </div>
            </div>
            <div class="timeline-item">
                <div class="timeline-marker"></div>
                <div class="timeline-content">
                    <div class="timeline-year">2022</div>
                    <h5 class="fw-bold">Terakreditasi B</h5>
                    <p>SMK Ardsentra berhasil meraih Akreditasi B berdasarkan SK BAN-S/M Nomor: 1857/BAN-SM/SK/2022, sebagai bentuk pengakuan terhadap kualitas manajemen dan mutu pendidikan yang dijalankan sekolah.</p>
                </div>
            </div>
            <div class="timeline-item">
                <div class="timeline-marker"></div>
                <div class="timeline-content">
                    <div class="timeline-year">2025</div>
                    <h5 class="fw-bold">Era Teknologi</h5>
                    <p>Di bawah kepemimpinan Suhadi, S.Pd. sebagai Kepala Sekolah, dan staff pengajar yang terus mendukung, SMK Ardsentra terus berkembang. Masih terus meningkatkan fasilitas belajar, pelatihan kewirausahaan, serta penguatan program kerja praktik industri guna membentuk lulusan yang siap kerja, kompeten, dan berdaya saing tinggi.</p>
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
</body>

</html>