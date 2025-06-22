<?php
session_start();
// Pastikan hanya admin yang sudah login bisa mengakses
if (!isset($_SESSION['id_user']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

include 'db_config.php'; // Koneksi database

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $new_username = trim($_POST['new_username']);
    $new_password = $_POST['new_password']; // Tidak di-trim agar spasi di awal/akhir dianggap
    $confirm_password = $_POST['confirm_password'];
    $status_user = $_POST['status_user'];

    // --- Validasi Server-Side ---
    $errors = [];

    // Validasi Username
    if (empty($new_username)) {
        $errors[] = "Username tidak boleh kosong.";
    } elseif (strlen($new_username) < 5) {
        $errors[] = "Username minimal 5 karakter.";
    } elseif (!preg_match('/^[a-zA-Z0-9_]+$/', $new_username)) {
        $errors[] = "Username hanya boleh mengandung huruf, angka, dan underscore (_).";
    } else {
        // Cek keunikan username
        $stmt_check_username = $conn->prepare("SELECT id_user FROM user WHERE username = ?");
        $stmt_check_username->bind_param("s", $new_username);
        $stmt_check_username->execute();
        $result_check_username = $stmt_check_username->get_result();
        if ($result_check_username->num_rows > 0) {
            $errors[] = "Username '" . htmlspecialchars($new_username) . "' sudah digunakan. Silakan pilih username lain.";
        }
        $stmt_check_username->close();
    }

    // Validasi Password
    if (empty($new_password)) {
        $errors[] = "Password tidak boleh kosong.";
    } elseif (strlen($new_password) < 8) {
        $errors[] = "Password minimal 8 karakter.";
    }

    // Validasi Konfirmasi Password
    if (empty($confirm_password)) {
        $errors[] = "Konfirmasi password tidak boleh kosong.";
    } elseif ($new_password !== $confirm_password) {
        $errors[] = "Password dan Konfirmasi Password tidak cocok.";
    }

    // Validasi Status
    if ($status_user !== 'aktif' && $status_user !== 'tidak aktif') {
        $errors[] = "Status user tidak valid.";
    }

    // Jika ada error, kembali ke form dengan pesan error
    if (!empty($errors)) {
        $error_message = implode("<br>", $errors);
        header("Location: admin_add_user.php?error=" . urlencode($error_message));
        exit();
    }

    // --- Jika Validasi Lolos ---
    // Hashing Password - Ini SANGAT PENTING!
    $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
    if ($hashed_password === false) {
        // Gagal hashing, bisa jadi karena konfigurasi PHP yang sangat salah
        header("Location: admin_add_user.php?error=" . urlencode("Terjadi kesalahan sistem saat memproses password."));
        exit();
    }

    // Insert ke database
    $stmt_insert_user = $conn->prepare("INSERT INTO user (username, password, status_user) VALUES (?, ?, ?)");
    $stmt_insert_user->bind_param("sss", $new_username, $hashed_password, $status_user);

    if ($stmt_insert_user->execute()) {
        $success_message = "Admin baru '" . htmlspecialchars($new_username) . "' berhasil ditambahkan.";
        header("Location: admin_add_user.php?success=" . urlencode($success_message));
        exit();
    } else {
        // Jika ada error saat insert (misal karena constraint unik gagal lagi karena race condition, meski jarang)
        $error_message = "Gagal menambahkan admin ke database: " . $stmt_insert_user->error;
        // Log error ini di server: error_log("SQL Error: " . $stmt_insert_user->error);
        header("Location: admin_add_user.php?error=" . urlencode($error_message));
        exit();
    }
    $stmt_insert_user->close();
    $conn->close();

} else {
    // Bukan metode POST, redirect
    header("Location: admin_add_user.php");
    exit();
}
?>