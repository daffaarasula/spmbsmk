<?php
// galeri.php

// Data untuk galeri. Ganti path gambar dan deskripsi sesuai kebutuhan Anda.
// Anda bisa mengambil data ini dari database di masa mendatang.
$galleryImages = [
    [
        'src' => 'img/upacara.jpg',
        'title' => 'Upacara Bendera Setiap Senin',
        'category' => 'Kegiatan Rutin'
    ],
    [
        'src' => 'img/BisnisDa.jpg',
        'title' => 'Kegiatan Bisnis Daring',
        'category' => 'Kegiatan Belajar'
    ],
    [
        'src' => 'img/OTKP.jpg',
        'title' => 'Kegiatan OTKP',
        'category' => 'Kegiatan Belajar'
    ],
    [
        'src' => 'img/AKL.jpg',
        'title' => 'Kegiatan AKL',
        'category' => 'Kegiatan Belajar'
    ],
    [
        'src' => 'img/BisnisD.jpg',
        'title' => 'Praktikum Kejuruan',
        'category' => 'Praktik Kejuruan'
    ],
    [
        'src' => 'img/kbm.jpg',
        'title' => 'Kegiatan Belajar Mengajar',
        'category' => 'Kegiatan Belajar'
    ],
    [
        'src' => 'img/pengadilana.jpg',
        'title' => 'Kerjasama Pengadilan Agama',
        'category' => 'Kerja Sama'
    ],
    [
        'src' => 'img/kerjasama.jpg',
        'title' => 'Kegiatan Kerjasama',
        'category' => 'Kerja Sama'
    ],
    [
        'src' => 'img/perpisahan.jpg',
        'title' => 'Pelepasan Murid Kelas 12',
        'category' => 'Perpisahan 2025'
    ],
];
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Galeri - SPMB SMK ArdsentrA</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">
    <style>
        body { 
            padding-top: 70px; 
            font-family: 'Poppins', sans-serif; 
            background-color: #f4f7f6; 
        }
        .navbar-custom-orange {
            background-color: #fd7e14 !important; 
        }
        .gallery-card {
            border: none;
            border-radius: 0.5rem;
            overflow: hidden; /* Agar gambar tidak keluar dari sudut card */
            box-shadow: 0 0.25rem 0.75rem rgba(0,0,0,0.1);
            transition: all 0.3s ease;
        }
        .gallery-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 0.5rem 1.5rem rgba(0,0,0,0.2);
        }
        .gallery-card .card-img-top {
            width: 100%;
            height: 250px; /* Tinggi gambar yang seragam */
            object-fit: cover; /* Memastikan gambar terisi penuh tanpa distorsi */
        }
        .gallery-card a {
            text-decoration: none;
            color: inherit;
            display: block;
        }
        .gallery-card .card-body {
            background-color: rgba(0,0,0,0.6);
            color: white;
            position: absolute;
            bottom: 0;
            width: 100%;
            padding: 1rem;
            transform: translateY(100%);
            transition: transform 0.4s ease;
        }
        .gallery-card:hover .card-body {
            transform: translateY(0);
        }
        .modal-lg { max-width: 80%; }
        .modal-content { border: none; }
        .footer {
            background-color: #2c3e50;
            color: #ecf0f1;
            padding: 3rem 0;
            margin-top: 4rem;
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
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto fw-semibold">
                    <li class="nav-item"><a class="nav-link" href="index.php"><i class="bi bi-house-door me-1"></i>Beranda</a></li>
                    <li class="nav-item"><a class="nav-link" href="tentang_sekolah.php"><i class="bi bi-file-person"></i>Tentang Kami</a></li>
                    <li class="nav-item"><a class="nav-link active" aria-current="page" href="galeri.php"><i class="bi bi-images me-1"></i>Galeri</a></li>
                    <li class="nav-item"><a class="nav-link" href="register.php"><i class="bi bi-person-plus me-1"></i>Daftar</a></li>
                    <li class="nav-item"><a class="nav-link" href="cek_status.php"><i class="bi bi-patch-check me-1"></i>Cek Status</a></li>
                    <li class="nav-item"><a class="nav-link" href="login.php"><i class="bi bi-box-arrow-in-right me-1"></i>Login</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container my-5">
        <div class="text-center mb-5">
            <h1 class="display-5 fw-bold text-custom-orange" style="color: #fd7e14;">Galeri Sekolah</h1>
            <p class="lead text-muted">Momen Kegiatan Yang Ada di SMK ArdsentrA</p>
        </div>

        <div class="row g-4">
            <?php foreach ($galleryImages as $image): ?>
                <div class="col-lg-4 col-md-6">
                    <div class="card gallery-card text-white">
                        <img src="<?php echo htmlspecialchars($image['src']); ?>" class="card-img-top" alt="<?php echo htmlspecialchars($image['title']); ?>">
                        <a href="#" class="stretched-link"
                           data-bs-toggle="modal" 
                           data-bs-target="#galleryModal"
                           data-bs-image-src="<?php echo htmlspecialchars($image['src']); ?>"
                           data-bs-image-title="<?php echo htmlspecialchars($image['title']); ?>">
                        </a>
                        <div class="card-body">
                            <h5 class="card-title"><?php echo htmlspecialchars($image['title']); ?></h5>
                            <p class="card-text small"><?php echo htmlspecialchars($image['category']); ?></p>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <div class="modal fade" id="galleryModal" tabindex="-1" aria-labelledby="galleryModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content bg-transparent">
                <div class="modal-header border-0">
                    <h5 class="modal-title text-white" id="galleryModalLabel"></h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-0">
                    <img src="" class="img-fluid w-100" id="modalImage" alt="Gambar Galeri">
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

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // JavaScript untuk mengisi modal secara dinamis
        const galleryModal = document.getElementById('galleryModal');
        galleryModal.addEventListener('show.bs.modal', event => {
            // Tombol/link yang memicu modal
            const triggerElement = event.relatedTarget;
            
            // Ekstrak informasi dari atribut data-*
            const imgSrc = triggerElement.getAttribute('data-bs-image-src');
            const imgTitle = triggerElement.getAttribute('data-bs-image-title');

            // Perbarui konten modal
            const modalTitle = galleryModal.querySelector('.modal-title');
            const modalImage = galleryModal.querySelector('#modalImage');

            modalTitle.textContent = imgTitle;
            modalImage.src = imgSrc;
            modalImage.alt = imgTitle;
        });
    </script>
</body>
</html>