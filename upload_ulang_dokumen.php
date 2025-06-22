<?php
session_start();

// Koneksi database
require_once 'db_config.php';

if (!isset($_SESSION['id_user']) || $_SESSION['role'] !== 'pendaftar') {
    header("Location: login.php");
    exit();
}

$username = $_SESSION['username'];

// Ambil id_pendaftaran berdasarkan NISN
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

// Ambil data pendaftar untuk nama
$stmt = $conn->prepare("SELECT nama_siswa FROM pendaftaran WHERE id_pendaftaran = ?");
$stmt->bind_param("i", $id_pendaftaran);
$stmt->execute();
$result = $stmt->get_result();
$pendaftar = $result->fetch_assoc();
$stmt->close();

// Ambil data dokumen yang ada
$stmt_dokumen = $conn->prepare("SELECT * FROM dokumen WHERE id_pendaftaran = ?");
$stmt_dokumen->bind_param("i", $id_pendaftaran);
$stmt_dokumen->execute();
$result_dokumen = $stmt_dokumen->get_result();
$dokumen_existing = $result_dokumen->fetch_assoc();
$stmt_dokumen->close();

$message = "";
$message_type = "";

// Fungsi untuk menghapus file lama
function hapusFileLama($file_path) {
    if ($file_path && file_exists($file_path)) {
        return unlink($file_path);
    }
    return true;
}

// Fungsi untuk upload file
function uploadFile($file, $allowed_types, $max_size, $upload_dir) {
    $file_name = $file['name'];
    $file_tmp = $file['tmp_name'];
    $file_size = $file['size'];
    $file_error = $file['error'];
    
    // Cek error upload
    if ($file_error !== UPLOAD_ERR_OK) {
        return ['success' => false, 'message' => 'Error saat upload file.'];
    }
    
    // Cek ukuran file
    if ($file_size > $max_size) {
        return ['success' => false, 'message' => 'Ukuran file terlalu besar. Maksimal ' . ($max_size / 1024 / 1024) . 'MB.'];
    }
    
    // Cek tipe file
    $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
    if (!in_array($file_ext, $allowed_types)) {
        return ['success' => false, 'message' => 'Tipe file tidak diizinkan. Hanya: ' . implode(', ', $allowed_types)];
    }
    
    // Generate nama file unik
    $new_file_name = time() . '_' . uniqid() . '.' . $file_ext;
    $upload_path = $upload_dir . $new_file_name;
    
    // Pastikan direktori exists
    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0777, true);
    }
    
    // Upload file
    if (move_uploaded_file($file_tmp, $upload_path)) {
        return ['success' => true, 'file_path' => $upload_path];
    } else {
        return ['success' => false, 'message' => 'Gagal memindahkan file.'];
    }
}

// Proses upload
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $upload_dir = 'uploads/dokumen/';
    $allowed_types = ['jpg', 'jpeg', 'png', 'pdf'];
    $max_size = 5 * 1024 * 1024; // 5MB
    
    $updated_fields = [];
    $update_values = [];
    $update_types = "";
    
    $has_upload = false;
    $upload_errors = [];
    
    // Proses setiap jenis dokumen
    $dokumen_types = ['ijazah', 'pasfoto', 'ktp', 'kk'];
    
    foreach ($dokumen_types as $doc_type) {
        if (isset($_FILES[$doc_type]) && $_FILES[$doc_type]['error'] !== UPLOAD_ERR_NO_FILE) {
            $has_upload = true;
            
            $upload_result = uploadFile($_FILES[$doc_type], $allowed_types, $max_size, $upload_dir);
            
            if ($upload_result['success']) {
                // Hapus file lama jika ada
                if ($dokumen_existing && $dokumen_existing[$doc_type]) {
                    hapusFileLama($dokumen_existing[$doc_type]);
                }
                
                $updated_fields[] = "$doc_type = ?";
                $update_values[] = $upload_result['file_path'];
                $update_types .= "s";
            } else {
                $upload_errors[] = ucfirst($doc_type) . ": " . $upload_result['message'];
            }
        }
    }
    
    if ($has_upload) {
        if (empty($upload_errors)) {
            // Update status verifikasi menjadi 'Menunggu' dan hapus catatan admin
            $updated_fields[] = "status_verifikasi = ?";
            $updated_fields[] = "catatan_admin = ?";
            $update_values[] = "Menunggu";
            $update_values[] = "";
            $update_types .= "ss";
            
            if ($dokumen_existing) {
                // Update dokumen yang sudah ada
                $sql = "UPDATE dokumen SET " . implode(", ", $updated_fields) . " WHERE id_pendaftaran = ?";
                $update_values[] = $id_pendaftaran;
                $update_types .= "i";
                
                $stmt = $conn->prepare($sql);
                $stmt->bind_param($update_types, ...$update_values);
                
                if ($stmt->execute()) {
                    $message = "Dokumen berhasil diperbarui! Status verifikasi direset ke 'Menunggu'.";
                    $message_type = "success";
                } else {
                    $message = "Gagal memperbarui dokumen: " . $conn->error;
                    $message_type = "danger";
                }
                $stmt->close();
            } else {
                // Insert dokumen baru
                $fields = ["id_pendaftaran"];
                $placeholders = ["?"];
                $values = [$id_pendaftaran];
                $types = "i";
                
                foreach ($dokumen_types as $doc_type) {
                    $fields[] = $doc_type;
                    $placeholders[] = "?";
                    if (in_array("$doc_type = ?", $updated_fields)) {
                        $key = array_search("$doc_type = ?", $updated_fields);
                        $values[] = $update_values[$key];
                    } else {
                        $values[] = null;
                    }
                    $types .= "s";
                }
                
                $fields[] = "status_verifikasi";
                $placeholders[] = "?";
                $values[] = "Menunggu";
                $types .= "s";
                
                $sql = "INSERT INTO dokumen (" . implode(", ", $fields) . ") VALUES (" . implode(", ", $placeholders) . ")";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param($types, ...$values);
                
                if ($stmt->execute()) {
                    $message = "Dokumen berhasil diupload!";
                    $message_type = "success";
                } else {
                    $message = "Gagal mengupload dokumen: " . $conn->error;
                    $message_type = "danger";
                }
                $stmt->close();
            }
            
            // Refresh data dokumen
            $stmt_dokumen = $conn->prepare("SELECT * FROM dokumen WHERE id_pendaftaran = ?");
            $stmt_dokumen->bind_param("i", $id_pendaftaran);
            $stmt_dokumen->execute();
            $result_dokumen = $stmt_dokumen->get_result();
            $dokumen_existing = $result_dokumen->fetch_assoc();
            $stmt_dokumen->close();
            
        } else {
            $message = "Terjadi kesalahan: " . implode(", ", $upload_errors);
            $message_type = "danger";
        }
    } else {
        $message = "Tidak ada file yang dipilih untuk diupload.";
        $message_type = "warning";
    }
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Upload Ulang Dokumen - PPDB SMK</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .main-content {
            background-color: #f8f9fa;
            min-height: 100vh;
            padding: 2rem 0;
        }

        .upload-card {
            max-width: 800px;
            margin: 0 auto;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
        }

        .card-header {
            background: linear-gradient(135deg, #3498db, #2980b9);
            color: white;
        }

        .file-input-wrapper {
            position: relative;
            overflow: hidden;
            display: inline-block;
            width: 100%;
        }

        .file-input {
            position: absolute;
            left: -9999px;
        }

        .file-input-label {
            display: flex;
            align-items: center;
            padding: 0.5rem 1rem;
            background-color: #e9ecef;
            border: 2px dashed #ced4da;
            border-radius: 0.5rem;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .file-input-label:hover {
            background-color: #dee2e6;
            border-color: #3498db;
        }

        .file-input-label.has-file {
            background-color: #d1ecf1;
            border-color: #17a2b8;
            color: #0c5460;
        }

        .file-input-label.has-error {
            background-color: #f8d7da;
            border-color: #dc3545;
            color: #721c24;
        }

        .file-preview {
            margin-top: 0.5rem;
            padding: 0.5rem;
            background-color: #f8f9fa;
            border-radius: 0.25rem;
            font-size: 0.9em;
        }

        .btn-back {
            background-color: #6c757d;
            border-color: #6c757d;
        }

        .btn-back:hover {
            background-color: #5a6268;
            border-color: #545b62;
        }

        .current-file {
            background-color: #d4edda;
            border: 1px solid #c3e6cb;
            border-radius: 0.25rem;
            padding: 0.5rem;
            margin-bottom: 0.5rem;
        }

        .requirements {
            background-color: #fff3cd;
            border: 1px solid #ffeaa7;
            border-radius: 0.25rem;
            padding: 1rem;
            margin-bottom: 1rem;
        }
    </style>
</head>

<body>
    <div class="main-content">
        <div class="container">
            <div class="upload-card">
                <div class="card">
                    <div class="card-header">
                        <h4 class="mb-0">
                            <i class="fas fa-upload me-2"></i>
                            Upload Ulang Dokumen
                        </h4>
                        <small>Selamat datang, <?= htmlspecialchars($pendaftar['nama_siswa']) ?></small>
                    </div>
                    <div class="card-body">
                        <?php if ($message): ?>
                            <div class="alert alert-<?= $message_type ?> alert-dismissible fade show" role="alert">
                                <?= htmlspecialchars($message) ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        <?php endif; ?>

                        <div class="requirements">
                            <h6><i class="fas fa-info-circle me-2"></i>Persyaratan File:</h6>
                            <ul class="mb-0">
                                <li>Format file: JPG, JPEG, PNG, atau PDF</li>
                                <li>Ukuran maksimal: 5MB per file</li>
                                <li>Pastikan file dapat dibaca dengan jelas</li>
                                <li>File lama akan otomatis terhapus setelah upload berhasil</li>
                            </ul>
                        </div>

                        <form method="POST" enctype="multipart/form-data" id="uploadForm">
                            <div class="row">
                                <!-- Ijazah -->
                                <div class="col-md-6 mb-4">
                                    <label class="form-label fw-bold">
                                        <i class="fas fa-certificate me-2"></i>Ijazah
                                    </label>
                                    
                                    <?php if ($dokumen_existing && $dokumen_existing['ijazah']): ?>
                                        <div class="current-file">
                                            <small class="text-success">
                                                <i class="fas fa-check-circle me-1"></i>
                                                File saat ini: <?= basename($dokumen_existing['ijazah']) ?>
                                            </small>
                                            <a href="<?= htmlspecialchars($dokumen_existing['ijazah']) ?>" 
                                               target="_blank" class="btn btn-sm btn-outline-primary ms-2">
                                                <i class="fas fa-eye"></i> Lihat
                                            </a>
                                        </div>
                                    <?php endif; ?>
                                    
                                    <div class="file-input-wrapper">
                                        <input type="file" name="ijazah" id="ijazah" class="file-input" 
                                               accept=".jpg,.jpeg,.png,.pdf">
                                        <label for="ijazah" class="file-input-label">
                                            <i class="fas fa-cloud-upload-alt me-2"></i>
                                            <span>Pilih file ijazah baru...</span>
                                        </label>
                                    </div>
                                    <div class="file-preview" id="ijazah-preview" style="display: none;"></div>
                                </div>

                                <!-- Pas Foto -->
                                <div class="col-md-6 mb-4">
                                    <label class="form-label fw-bold">
                                        <i class="fas fa-camera me-2"></i>Pas Foto
                                    </label>
                                    
                                    <?php if ($dokumen_existing && $dokumen_existing['pasfoto']): ?>
                                        <div class="current-file">
                                            <small class="text-success">
                                                <i class="fas fa-check-circle me-1"></i>
                                                File saat ini: <?= basename($dokumen_existing['pasfoto']) ?>
                                            </small>
                                            <a href="<?= htmlspecialchars($dokumen_existing['pasfoto']) ?>" 
                                               target="_blank" class="btn btn-sm btn-outline-primary ms-2">
                                                <i class="fas fa-eye"></i> Lihat
                                            </a>
                                        </div>
                                    <?php endif; ?>
                                    
                                    <div class="file-input-wrapper">
                                        <input type="file" name="pasfoto" id="pasfoto" class="file-input" 
                                               accept=".jpg,.jpeg,.png">
                                        <label for="pasfoto" class="file-input-label">
                                            <i class="fas fa-cloud-upload-alt me-2"></i>
                                            <span>Pilih pas foto baru...</span>
                                        </label>
                                    </div>
                                    <div class="file-preview" id="pasfoto-preview" style="display: none;"></div>
                                </div>

                                <!-- KTP -->
                                <div class="col-md-6 mb-4">
                                    <label class="form-label fw-bold">
                                        <i class="fas fa-id-card me-2"></i>KTP
                                    </label>
                                    
                                    <?php if ($dokumen_existing && $dokumen_existing['ktp']): ?>
                                        <div class="current-file">
                                            <small class="text-success">
                                                <i class="fas fa-check-circle me-1"></i>
                                                File saat ini: <?= basename($dokumen_existing['ktp']) ?>
                                            </small>
                                            <a href="<?= htmlspecialchars($dokumen_existing['ktp']) ?>" 
                                               target="_blank" class="btn btn-sm btn-outline-primary ms-2">
                                                <i class="fas fa-eye"></i> Lihat
                                            </a>
                                        </div>
                                    <?php endif; ?>
                                    
                                    <div class="file-input-wrapper">
                                        <input type="file" name="ktp" id="ktp" class="file-input" 
                                               accept=".jpg,.jpeg,.png,.pdf">
                                        <label for="ktp" class="file-input-label">
                                            <i class="fas fa-cloud-upload-alt me-2"></i>
                                            <span>Pilih file KTP baru...</span>
                                        </label>
                                    </div>
                                    <div class="file-preview" id="ktp-preview" style="display: none;"></div>
                                </div>

                                <!-- Kartu Keluarga -->
                                <div class="col-md-6 mb-4">
                                    <label class="form-label fw-bold">
                                        <i class="fas fa-users me-2"></i>Kartu Keluarga
                                    </label>
                                    
                                    <?php if ($dokumen_existing && $dokumen_existing['kk']): ?>
                                        <div class="current-file">
                                            <small class="text-success">
                                                <i class="fas fa-check-circle me-1"></i>
                                                File saat ini: <?= basename($dokumen_existing['kk']) ?>
                                            </small>
                                            <a href="<?= htmlspecialchars($dokumen_existing['kk']) ?>" 
                                               target="_blank" class="btn btn-sm btn-outline-primary ms-2">
                                                <i class="fas fa-eye"></i> Lihat
                                            </a>
                                        </div>
                                    <?php endif; ?>
                                    
                                    <div class="file-input-wrapper">
                                        <input type="file" name="kk" id="kk" class="file-input" 
                                               accept=".jpg,.jpeg,.png,.pdf">
                                        <label for="kk" class="file-input-label">
                                            <i class="fas fa-cloud-upload-alt me-2"></i>
                                            <span>Pilih file KK baru...</span>
                                        </label>
                                    </div>
                                    <div class="file-preview" id="kk-preview" style="display: none;"></div>
                                </div>
                            </div>

                            <div class="d-flex justify-content-between mt-4">
                                <a href="pendaftar_dashboard.php?page=dokumen" class="btn btn-back">
                                    <i class="fas fa-arrow-left me-2"></i>
                                    Kembali
                                </a>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-upload me-2"></i>
                                    Upload Dokumen
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Handle file input changes
        document.addEventListener('DOMContentLoaded', function() {
            const fileInputs = ['ijazah', 'pasfoto', 'ktp', 'kk'];
            
            fileInputs.forEach(function(inputName) {
                const input = document.getElementById(inputName);
                const label = document.querySelector(`label[for="${inputName}"]`);
                const preview = document.getElementById(`${inputName}-preview`);
                
                input.addEventListener('change', function() {
                    const file = this.files[0];
                    const span = label.querySelector('span');
                    
                    if (file) {
                        // Validasi ukuran file (5MB)
                        if (file.size > 5 * 1024 * 1024) {
                            label.classList.add('has-error');
                            label.classList.remove('has-file');
                            span.textContent = 'File terlalu besar! Maksimal 5MB';
                            preview.style.display = 'none';
                            return;
                        }
                        
                        // Validasi tipe file
                        const allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'application/pdf'];
                        if (!allowedTypes.includes(file.type)) {
                            label.classList.add('has-error');
                            label.classList.remove('has-file');
                            span.textContent = 'Tipe file tidak diizinkan!';
                            preview.style.display = 'none';
                            return;
                        }
                        
                        // File valid
                        label.classList.add('has-file');
                        label.classList.remove('has-error');
                        span.textContent = file.name;
                        
                        // Show preview info
                        preview.innerHTML = `
                            <small class="text-muted">
                                <i class="fas fa-file me-1"></i>
                                ${file.name} (${(file.size / 1024 / 1024).toFixed(2)} MB)
                            </small>
                        `;
                        preview.style.display = 'block';
                    } else {
                        label.classList.remove('has-file', 'has-error');
                        span.textContent = `Pilih file ${inputName} baru...`;
                        preview.style.display = 'none';
                    }
                });
            });
            
            // Form validation before submit
            document.getElementById('uploadForm').addEventListener('submit', function(e) {
                const hasFile = fileInputs.some(inputName => {
                    return document.getElementById(inputName).files.length > 0;
                });
                
                if (!hasFile) {
                    e.preventDefault();
                    alert('Pilih minimal satu file untuk diupload!');
                    return false;
                }
                
                // Show loading state
                const submitBtn = this.querySelector('button[type="submit"]');
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Mengupload...';
            });
        });
    </script>
</body>
</html>