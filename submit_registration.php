<?php
include 'db_config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $conn->begin_transaction();

    try {
        // Ambil data form
        $nisn = $_POST['nisn'];
        $nama_siswa = $_POST['nama_siswa'];
        $tempat = $_POST['tempat_lahir'];
        $tanggal = $_POST['tanggal_lahir'];
        $tempat_tgl_lahir = $tempat . ', ' . date('d-m-Y', strtotime($tanggal));
        $id_jk = $_POST['id_jk'];
        $id_agama = $_POST['id_agama'];
        $alamat_siswa = $_POST['alamat_siswa'];
        $asal_sekolah = $_POST['asal_sekolah'];
        $pilih_jurusan = $_POST['pilih_jurusan'];
        $no_handphone = $_POST['no_handphone'];
        $email_siswa = $_POST['email'];

        // Insert ke tabel pendaftaran
        $stmt1 = $conn->prepare("INSERT INTO pendaftaran (nisn, nama_siswa, tempat_tgl_lahir, id_jk, id_agama, alamat, asal_sekolah, pilih_jurusan, no_handphone, email) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt1->bind_param("sssiisssss", $nisn, $nama_siswa, $tempat_tgl_lahir, $id_jk, $id_agama, $alamat_siswa, $asal_sekolah, $pilih_jurusan, $no_handphone, $email_siswa);
        $stmt1->execute();
        $id_pendaftaran = $conn->insert_id;
        $stmt1->close();

        // Insert ke tabel data_ortu
        $stmt2 = $conn->prepare("INSERT INTO data_ortu (id_pendaftaran, nama_ayah, nama_ibu, pekerjaan_ayah, pekerjaan_ibu, alamat) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt2->bind_param("isssss", $id_pendaftaran, $_POST['nama_ayah'], $_POST['nama_ibu'], $_POST['pekerjaan_ayah'], $_POST['pekerjaan_ibu'], $_POST['alamat_ortu']);
        $stmt2->execute();
        $stmt2->close();

        // Upload file
        $uploadDir = "uploads/";
        if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);

        function uploadFile($input, $dir)
        {
            $tmp = $_FILES[$input]['tmp_name'];
            $name = uniqid() . "_" . preg_replace("/[^a-zA-Z0-9.]/", "_", basename($_FILES[$input]['name']));
            $path = $dir . $name;
            if (!move_uploaded_file($tmp, $path)) throw new Exception("Gagal mengunggah file $input.");
            return $path;
        }

        $pasfoto = uploadFile("pasfoto", $uploadDir);
        $ijazah  = uploadFile("ijazah", $uploadDir);
        $ktp     = uploadFile("ktp", $uploadDir);
        $kk      = uploadFile("kk", $uploadDir);

        // Insert ke tabel dokumen
        $stmt3 = $conn->prepare("INSERT INTO dokumen (id_pendaftaran, ijazah, pasfoto, ktp, kk) VALUES (?, ?, ?, ?, ?)");
        $stmt3->bind_param("issss", $id_pendaftaran, $ijazah, $pasfoto, $ktp, $kk);
        $stmt3->execute();
        $stmt3->close();

        // Tambahkan ke tabel user (login pendaftar)
        $password_plain = date("dmY", strtotime($tanggal));
        $password_hashed = password_hash($password_plain, PASSWORD_DEFAULT);
        $role = 'pendaftar';

        $stmt4 = $conn->prepare("INSERT INTO user (username, password, status_user, role) VALUES (?, ?, 'aktif', ?)");
        $stmt4->bind_param("sss", $nisn, $password_hashed, $role);
        $stmt4->execute();
        $stmt4->close();

        // Selesai
        $conn->commit();

        // Tampilkan pesan sukses (tanpa email)
        header("Location: success.php?id=" . $id_pendaftaran);
        exit();
    } catch (Exception $e) {
        $conn->rollback();
        echo "Pendaftaran gagal: " . htmlspecialchars($e->getMessage());
        echo '<br><a href="register.php">Kembali</a>';
    }

    $conn->close();
} else {
    header("Location: register.php");
    exit();
}
