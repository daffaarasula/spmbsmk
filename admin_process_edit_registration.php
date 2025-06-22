<?php
session_start();
if (!isset($_SESSION['id_user']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}
include 'db_config.php';
include 'email_helper.php'; // Jika ingin notifikasi email saat data diedit (opsional)

// Sertakan fungsi handle_upload dari submit_registration.php atau definisikan di sini
// Untuk kemudahan, kita asumsikan fungsi handle_upload sudah ada atau di-include
function handle_upload($file_input_name, $upload_subdir = '')
{
    // ... (Isi fungsi handle_upload sama seperti di submit_registration.php)
    $target_dir = "uploads/" . $upload_subdir;
    if (!file_exists($target_dir) && !mkdir($target_dir, 0777, true) && !is_dir($target_dir)) {
        throw new \RuntimeException(sprintf('Directory "%s" was not created', $target_dir));
    }

    if (isset($_FILES[$file_input_name]) && $_FILES[$file_input_name]['error'] == 0) {
        $safe_filename = preg_replace('/[^A-Za-z0-9.\-_]/', '', basename($_FILES[$file_input_name]["name"]));
        $extension = strtolower(pathinfo($safe_filename, PATHINFO_EXTENSION));
        $unique_filename = uniqid('', true) . '.' . $extension;
        $target_file = $target_dir . $unique_filename;

        $allowed_types_image = ['jpg', 'jpeg', 'png'];
        $allowed_types_doc = ['pdf', 'jpg', 'jpeg', 'png'];
        $max_size_general = 2 * 1024 * 1024; // 2MB general max size

        if (!in_array($extension, array_merge($allowed_types_image, $allowed_types_doc)) || $_FILES[$file_input_name]["size"] > $max_size_general) {
            error_log("Invalid file type or size for " . $file_input_name . ": " . $safe_filename);
            return ['error' => 'Invalid file type or size. Max 2MB. Allowed: jpg, jpeg, png, pdf.'];
        }

        if (move_uploaded_file($_FILES[$file_input_name]["tmp_name"], $target_file)) {
            return ['path' => $target_file];
        } else {
            error_log("Failed to move uploaded file: " . $safe_filename);
            return ['error' => 'Failed to move uploaded file.'];
        }
    } elseif (isset($_FILES[$file_input_name]) && $_FILES[$file_input_name]['error'] != UPLOAD_ERR_NO_FILE) {
        // Ada file tapi error selain 'no file uploaded'
        return ['error' => 'File upload error code: ' . $_FILES[$file_input_name]['error']];
    }
    return ['path' => null]; // Tidak ada file baru yang diunggah atau error UPLOAD_ERR_NO_FILE
}


if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['id_pendaftaran'])) {
    $id_pendaftaran = filter_var($_POST['id_pendaftaran'], FILTER_SANITIZE_NUMBER_INT);

    $conn->begin_transaction();
    try {
        // Ambil data lama dokumen untuk menghapus file lama jika diganti
        $stmt_old_docs = $conn->prepare("SELECT pasfoto, ijazah, ktp, kk FROM dokumen WHERE id_pendaftaran = ?");
        $stmt_old_docs->bind_param("i", $id_pendaftaran);
        $stmt_old_docs->execute();
        $old_docs_result = $stmt_old_docs->get_result();
        $old_docs = $old_docs_result->fetch_assoc();
        $stmt_old_docs->close();

        // 1. Update tabel pendaftaran
        $nisn = $_POST['nisn'];
        $nama_siswa = $_POST['nama_siswa'];
        $tempat = $_POST['tempat_lahir'];
        $tanggal = $_POST['tanggal_lahir'];
        $tempat_tgl_lahir = $tempat . ', ' . date('d-m-Y', strtotime($tanggal));
        $id_jk = $_POST['id_jk'];
        $id_agama = $_POST['id_agama'];
        $alamat_siswa = $_POST['alamat_siswa']; // Nama kolom di DB adalah 'alamat'
        $asal_sekolah = $_POST['asal_sekolah'];
        $pilih_jurusan = $_POST['pilih_jurusan'];
        $no_handphone = $_POST['no_handphone'];
        $email = $_POST['email'];

        $stmt_pendaftaran = $conn->prepare("UPDATE pendaftaran SET nisn=?, nama_siswa=?, tempat_tgl_lahir=?, id_jk=?, id_agama=?, alamat=?, asal_sekolah=?, pilih_jurusan=?, no_handphone=?, email=? WHERE id_pendaftaran=?");
        $stmt_pendaftaran->bind_param("sssiisssssi", $nisn, $nama_siswa, $tempat_tgl_lahir, $id_jk, $id_agama, $alamat_siswa, $asal_sekolah, $pilih_jurusan, $no_handphone, $email, $id_pendaftaran);
        if (!$stmt_pendaftaran->execute()) throw new Exception("Gagal update tabel pendaftaran: " . $stmt_pendaftaran->error);
        $stmt_pendaftaran->close();

        // 2. Update tabel data_ortu
        $nama_ayah = $_POST['nama_ayah'];
        $pekerjaan_ayah = $_POST['pekerjaan_ayah'] ?? NULL;
        $nama_ibu = $_POST['nama_ibu'];
        $pekerjaan_ibu = $_POST['pekerjaan_ibu'] ?? NULL;
        $alamat_ortu = $_POST['alamat_ortu'];

        // Cek apakah data ortu sudah ada atau belum
        $stmt_check_ortu = $conn->prepare("SELECT id_data_ortu FROM data_ortu WHERE id_pendaftaran = ?");
        $stmt_check_ortu->bind_param("i", $id_pendaftaran);
        $stmt_check_ortu->execute();
        $result_check_ortu = $stmt_check_ortu->get_result();
        $stmt_check_ortu->close();

        if ($result_check_ortu->num_rows > 0) {
            $stmt_data_ortu = $conn->prepare("UPDATE data_ortu SET nama_ayah=?, pekerjaan_ayah=?, nama_ibu=?, pekerjaan_ibu=?, alamat=? WHERE id_pendaftaran=?");
            $stmt_data_ortu->bind_param("sssssi", $nama_ayah, $pekerjaan_ayah, $nama_ibu, $pekerjaan_ibu, $alamat_ortu, $id_pendaftaran);
        } else {
            // Jika belum ada, insert baru (jarang terjadi jika alur pendaftaran normal)
            $stmt_data_ortu = $conn->prepare("INSERT INTO data_ortu (id_pendaftaran, nama_ayah, pekerjaan_ayah, nama_ibu, pekerjaan_ibu, alamat) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt_data_ortu->bind_param("isssss", $id_pendaftaran, $nama_ayah, $pekerjaan_ayah, $nama_ibu, $pekerjaan_ibu, $alamat_ortu);
        }
        if (!$stmt_data_ortu->execute()) throw new Exception("Gagal update/insert tabel data_ortu: " . $stmt_data_ortu->error);
        $stmt_data_ortu->close();

        // 3. Update tabel dokumen (handle file uploads)
        $update_doc_fields = [];
        $doc_params = [];
        $doc_types = "";

        $file_map = [
            'upload_pasfoto_siswa' => ['field' => 'pasfoto', 'subdir' => 'pasfoto/'],
            'upload_ijazah_siswa' => ['field' => 'ijazah', 'subdir' => 'ijazah/'],
            'upload_ktp_ortu' => ['field' => 'ktp', 'subdir' => 'ktp_kk/'],
            'upload_kk_ortu' => ['field' => 'kk', 'subdir' => 'ktp_kk/']
        ];

        foreach ($file_map as $input_name => $file_info) {
            if (isset($_FILES[$input_name]) && $_FILES[$input_name]['error'] == UPLOAD_ERR_OK) {
                $upload_result = handle_upload($input_name, $file_info['subdir']);
                if (isset($upload_result['path']) && $upload_result['path'] !== null) {
                    $new_file_path = $upload_result['path'];
                    // Hapus file lama jika ada dan file baru berhasil diunggah
                    if ($old_docs && !empty($old_docs[$file_info['field']]) && file_exists($old_docs[$file_info['field']])) {
                        unlink($old_docs[$file_info['field']]);
                    }
                    $update_doc_fields[] = $file_info['field'] . " = ?";
                    $doc_params[] = $new_file_path;
                    $doc_types .= "s";
                } elseif (isset($upload_result['error'])) {
                    throw new Exception("Gagal unggah " . $input_name . ": " . $upload_result['error']);
                }
            }
        }

        if (!empty($update_doc_fields)) {
            $doc_params[] = $id_pendaftaran;
            $doc_types .= "i";
            $stmt_dokumen = $conn->prepare("UPDATE dokumen SET " . implode(", ", $update_doc_fields) . " WHERE id_pendaftaran = ?");
            $stmt_dokumen->bind_param($doc_types, ...$doc_params);
            if (!$stmt_dokumen->execute()) throw new Exception("Gagal update tabel dokumen: " . $stmt_dokumen->error);
            $stmt_dokumen->close();
        }

        $conn->commit();
        header("Location: admin_edit_registration.php?id=" . $id_pendaftaran . "&update_success=1");
        exit();
    } catch (Exception $e) {
        $conn->rollback();
        $error_message = urlencode($e->getMessage());
        header("Location: admin_edit_registration.php?id=" . $id_pendaftaran . "&update_error=" . $error_message);
        exit();
    }
    $conn->close();
} else {
    header("Location: admin_dashboard.php?error=Invalid request.");
    exit();
}
