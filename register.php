<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Formulir Pendaftaran SPMB SMK ArdsentrA</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f4f7f6;
            /* Senada dengan index.php */
        }

        /* === CSS Kustom untuk Navbar Oranye === */
        .navbar-custom-orange {
            background-color: #fd7e14 !important;
            /* Warna oranye kustom */
        }

        /* === Akhir CSS Kustom === */

        .form-container {
            max-width: 800px;
            /* Lebar form agar tidak terlalu stretched */
            margin: 30px auto;
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

        .required-asterisk {
            color: var(--bs-danger);
            margin-left: 2px;
        }

        .footer {
            background-color: #343a40;
            color: white;
            padding: 2rem 0;
            margin-top: 4rem;
        }
    </style>
</head>

<body>
    <nav class="navbar navbar-expand-lg navbar-dark navbar-custom-orange shadow-sm">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center" href="index.php">
                <img src="img/logo.png" alt="Logo SMK ArdsentrA" height="40" class="me-2">
                <span class="fw-bold text-white">SPMB SMK ArdsentrA</span>
            </a>
            <ul class="navbar-nav ms-auto">
                <li class="nav-item"><a class="nav-link" href="index.php"><i class="bi bi-house-door me-1"></i>Beranda</a></li>
                <li class="nav-item"><a class="nav-link active" aria-current="page" href="register.php"><i class="bi bi-person-plus me-1"></i>Daftar</a></li>
                <li class="nav-item"><a class="nav-link" href="cek_status.php"><i class="bi bi-patch-check me-1"></i>Cek Status</a></li>
                <li class="nav-item"><a class="nav-link" href="login.php"><i class="bi bi-box-arrow-in-right me-1"></i>Login</a></li>
            </ul>
        </div>
    </nav>

    <div class="container form-container">
        <div class="text-center mb-5 mt-4">
            <h1 class="display-5 fw-bold text-warning">Formulir Pendaftaran Siswa Baru</h1>
            <p class="lead text-muted">Lengkapi semua data dengan benar dan cermat.</p>
        </div>

        <form action="submit_registration.php" method="POST" enctype="multipart/form-data" class="needs-validation" novalidate>

            <div class="card mb-4 shadow-sm">
                <div class="card-header bg-info text-white">
                    <h4 class="mb-0"><i class="bi bi-person-fill"></i>Data Calon Siswa</h4>
                </div>
                <div class="card-body p-4">
                    <div class="mb-3">
                        <label for="nisn" class="form-label">NISN<span class="required-asterisk">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-person-vcard"></i></span>
                            <input type="text" class="form-control form-control-lg" id="nisn" name="nisn" maxlength="10" placeholder="Masukkan 10 digit NISN" required>
                        </div>
                        <div class="invalid-feedback">NISN wajib diisi (10 digit).</div>
                    </div>
                    <div class="mb-3">
                        <label for="nama_siswa" class="form-label">Nama Lengkap Siswa<span class="required-asterisk">*</span></label>
                        <input type="text" class="form-control form-control-lg" id="nama_siswa" name="nama_siswa" placeholder="Sesuai Akta Kelahiran" maxlength="100" required>
                        <div class="invalid-feedback">Nama siswa wajib diisi.</div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="tempat_lahir" class="form-label">Tempat Lahir<span class="required-asterisk">*</span></label>
                            <input type="text" class="form-control form-control-lg" id="tempat_lahir" name="tempat_lahir" maxlength="50" required>
                            <div class="invalid-feedback">Tempat lahir wajib diisi.</div>
                        </div>
                        <div class="col-md-6">
                            <label for="tanggal_lahir" class="form-label">Tanggal Lahir<span class="required-asterisk">*</span></label>
                            <input type="date" class="form-control form-control-lg" id="tanggal_lahir" name="tanggal_lahir" required>
                            <div class="invalid-feedback">Tanggal lahir wajib dipilih.</div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="id_jk" class="form-label">Jenis Kelamin<span class="required-asterisk">*</span></label>
                            <select class="form-select form-select-lg" id="id_jk" name="id_jk" required>
                                <option value="" disabled selected>Pilih...</option>
                                <?php
                                include_once 'db_config.php'; // Gunakan include_once untuk mencegah re-definisi
                                if (isset($conn)) { // Pastikan $conn ada
                                    $sql_jk = "SELECT id_jk, jeniskelamin FROM jenis_kelamin ORDER BY jeniskelamin";
                                    $result_jk = $conn->query($sql_jk);
                                    if ($result_jk && $result_jk->num_rows > 0) {
                                        while ($row_jk = $result_jk->fetch_assoc()) {
                                            echo "<option value='" . $row_jk["id_jk"] . "'>" . $row_jk["jeniskelamin"] . "</option>";
                                        }
                                    }
                                } else {
                                    echo "<option value=''>Database Error</option>";
                                }
                                ?>
                            </select>
                            <div class="invalid-feedback">Jenis kelamin wajib dipilih.</div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="id_agama" class="form-label">Agama<span class="required-asterisk">*</span></label>
                            <select class="form-select form-select-lg" id="id_agama" name="id_agama" required>
                                <option value="" disabled selected>Pilih...</option>
                                <?php
                                if (isset($conn)) { // Pastikan $conn ada
                                    $sql_agama = "SELECT id_agama, nama_agama FROM agama ORDER BY nama_agama";
                                    $result_agama = $conn->query($sql_agama);
                                    if ($result_agama && $result_agama->num_rows > 0) {
                                        while ($row_agama = $result_agama->fetch_assoc()) {
                                            echo "<option value='" . $row_agama["id_agama"] . "'>" . $row_agama["nama_agama"] . "</option>";
                                        }
                                    }
                                } else {
                                    echo "<option value=''>Database Error</option>";
                                }
                                ?>
                            </select>
                            <div class="invalid-feedback">Agama wajib dipilih.</div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="alamat_siswa" class="form-label">Alamat Lengkap Siswa<span class="required-asterisk">*</span></label>
                        <textarea class="form-control form-control-lg" id="alamat_siswa" name="alamat_siswa" rows="3" placeholder="Jl. Contoh No. 123, RT/RW, Kelurahan, Kecamatan, Kota/Kab, Kode Pos" required></textarea>
                        <div class="invalid-feedback">Alamat siswa wajib diisi.</div>
                    </div>
                    <div class="mb-3">
                        <label for="asal_sekolah" class="form-label">Asal Sekolah (SMP/MTs)<span class="required-asterisk">*</span></label>
                        <input type="text" class="form-control form-control-lg" id="asal_sekolah" name="asal_sekolah" placeholder="Nama SMP/MTs Asal" maxlength="100" required>
                        <div class="invalid-feedback">Asal sekolah wajib diisi.</div>
                    </div>
                    <div class="mb-3">
                        <label for="pilih_jurusan" class="form-label">Pilihan Jurusan<span class="required-asterisk">*</span></label>
                        <select class="form-select form-select-lg" id="pilih_jurusan" name="pilih_jurusan" required>
                            <option value="" disabled selected>Pilih Jurusan Unggulan...</option>
                            <option value="Bisnis Daring">Bisnis Daring </option>
                            <option value="Otomisasi Dan Tata Kelola Perkantoran">Otomisasi Dan Tata Kelola Perkantoran (OTKP) </option>
                            <option value="Akuntansi dan Keuangan Lembaga">Akuntansi dan Keuangan Lembaga (AKL)</option>
                        </select>
                        <div class="invalid-feedback">Pilihan jurusan wajib dipilih.</div>
                    </div>
                    <div class="mb-3">
                        <label for="no_handphone" class="form-label">No. Handphone Siswa (WhatsApp Aktif)<span class="required-asterisk">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-whatsapp"></i></span>
                            <input type="tel" class="form-control form-control-lg" id="no_handphone" name="no_handphone" placeholder="Contoh: 081234567890" maxlength="15" required>
                        </div>
                        <div class="invalid-feedback">No. handphone wajib diisi.</div>
                    </div>
                    <div class="mb-3">
                        <label for="email" class="form-label">Email Siswa<span class="required-asterisk">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-envelope-at-fill"></i></span>
                            <input type="email" class="form-control form-control-lg" id="email" name="email" placeholder="siswa@example.com" maxlength="100" required>
                        </div>
                        <div class="invalid-feedback">Email wajib diisi dan valid.</div>
                    </div>
                </div>
            </div>

            <div class="card mb-4 shadow-sm">
                <div class="card-header bg-warning text-dark">
                    <h4 class="mb-0"><i class="bi bi-people"></i>Data Orang Tua/Wali</h4>
                </div>
                <div class="card-body p-4">
                    <div class="mb-3">
                        <label for="nama_ayah" class="form-label">Nama Ayah<span class="required-asterisk">*</span></label>
                        <input type="text" class="form-control form-control-lg" id="nama_ayah" name="nama_ayah" placeholder="Nama Lengkap Ayah" maxlength="100" required>
                        <div class="invalid-feedback">Nama ayah wajib diisi.</div>
                    </div>
                    <div class="mb-3">
                        <label for="pekerjaan_ayah" class="form-label">Pekerjaan Ayah</label>
                        <input type="text" class="form-control form-control-lg" id="pekerjaan_ayah" name="pekerjaan_ayah" placeholder="Contoh: Wiraswasta, PNS, Karyawan Swasta" maxlength="100">
                    </div>
                    <div class="mb-3">
                        <label for="nama_ibu" class="form-label">Nama Ibu<span class="required-asterisk">*</span></label>
                        <input type="text" class="form-control form-control-lg" id="nama_ibu" name="nama_ibu" placeholder="Nama Lengkap Ibu Kandung" maxlength="100" required>
                        <div class="invalid-feedback">Nama ibu wajib diisi.</div>
                    </div>
                    <div class="mb-3">
                        <label for="pekerjaan_ibu" class="form-label">Pekerjaan Ibu</label>
                        <input type="text" class="form-control form-control-lg" id="pekerjaan_ibu" name="pekerjaan_ibu" placeholder="Contoh: Ibu Rumah Tangga, Wiraswasta" maxlength="100">
                    </div>
                    <div class="mb-3">
                        <label for="alamat_ortu" class="form-label">Alamat Lengkap Orang Tua/Wali<span class="required-asterisk">*</span></label>
                        <textarea class="form-control form-control-lg" id="alamat_ortu" name="alamat_ortu" rows="3" placeholder="Sesuai Kartu Keluarga" required></textarea>
                        <div class="invalid-feedback">Alamat orang tua wajib diisi.</div>
                    </div>
                </div>
            </div>

            <div class="card mb-4 shadow-sm">
                <div class="card-header bg-secondary text-white">
                    <h4 class="mb-0"><i class="bi bi-file-earmark-arrow-up"></i> Unggah Dokumen</h4>
                </div>
                <div class="card-body p-4">
                    <div class="mb-3">
                        <label for="pasfoto" class="form-label">Pas Foto 3x4 (Format JPG/PNG)<span class="required-asterisk">*</span></label>
                        <input class="form-control" type="file" name="pasfoto" id="pasfoto" accept=".jpg,.jpeg,.png" required>
                        <small class="form-text text-muted">*Pastikan Foto Jelas dan Tidak Menggunakan Aksesoris.</small>
                    </div>
                    <div class="mb-3">
                        <label for="ijazah" class="form-label">Scan Ijazah (PDF/JPG/PNG)</label>
                        <input class="form-control" type="file" name="ijazah" id="ijazah" accept=".pdf,.jpg,.jpeg,.png">
                        <small class="form-text text-muted">*Dokumen ini dapat diunggah nanti melalui dashboard.</small>
                    </div>
                    <div class="mb-3">
                        <label for="ktp" class="form-label">KTP Orang Tua/Wali</label>
                        <input class="form-control" type="file" name="ktp" id="ktp" accept=".pdf,.jpg,.jpeg,.png">
                        <small class="form-text text-muted">*Dokumen ini dapat diunggah nanti melalui dashboard.</small>
                    </div>
                    <div class="mb-3">
                        <label for="kk" class="form-label">Kartu Keluarga (KK)</label>
                        <input class="form-control" type="file" name="kk" id="kk" accept=".pdf,.jpg,.jpeg,.png">
                        <small class="form-text text-muted">*Dokumen ini dapat diunggah nanti melalui dashboard.</small>
                    </div>
                </div>
            </div>

            <div class="form-check mb-4 ps-0">
                <div class="card shadow-sm">
                    <div class="card-body bg-light p-4">
                        <div class="d-flex align-items-start">
                            <input class="form-check-input mt-1 me-2" type="checkbox" value="" id="agreement" style="width: 1.5em; height: 1.5em;" required>
                            <div>
                                <label class="form-check-label fw-semibold" for="agreement">
                                    Pernyataan Kebenaran Data
                                </label>
                                <p class="small text-muted mb-0">Saya menyatakan bahwa seluruh data yang saya isikan dalam formulir pendaftaran ini adalah benar dan dapat dipertanggungjawabkan. Jika di kemudian hari ditemukan ketidaksesuaian, saya bersedia menerima sanksi sesuai dengan ketentuan yang berlaku.</p>
                            </div>
                        </div>
                        <div class="invalid-feedback d-block ps-4 ms-2">Anda harus menyetujui pernyataan ini.</div>
                    </div>
                </div>
            </div>

            <button type="submit" class="btn btn-warning btn-lg w-100 py-3 fw-bold shadow text-dark">
                <i class="bi bi-send-check-fill me-2"></i>Kirim Formulir Pendaftaran
            </button>
        </form>
    </div>

    <footer class="footer">
        <div class="container text-center">
            <p class="mb-0">&copy; <?php echo date("Y"); ?> SPMB SMK ArdsentrA. All Rights Reserved.</p>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
    <script>
        (function() {
            'use strict'
            var forms = document.querySelectorAll('.needs-validation')
            Array.prototype.slice.call(forms)
                .forEach(function(form) {
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