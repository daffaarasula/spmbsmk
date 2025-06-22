<?php
session_start();

// Pastikan hanya menerima request POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    $_SESSION['error'] = "Metode request tidak valid";
    header("Location: admin_kelola_user.php");
    exit();
}

// Check if admin is logged in
if (!isset($_SESSION['id_user']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

include 'db_config.php';

// Sanitize input
$username = trim($_POST['new_username']);
$password = $_POST['new_password'];
$confirm_password = $_POST['confirm_password'];
$status = $_POST['status_user'];
$errors = [];
$role = 'admin';
// Validasi username
if (empty($username)) {
    $errors[] = "Username harus diisi";
} elseif (strlen($username) < 5) {
    $errors[] = "Username minimal 5 karakter";
} else {
    // Check username exists
    $stmt = mysqli_prepare($conn, "SELECT id_user FROM user WHERE username = ?");
    mysqli_stmt_bind_param($stmt, "s", $username);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_store_result($stmt);
    
    if (mysqli_stmt_num_rows($stmt) > 0) {
        $errors[] = "Username sudah digunakan";
    }
    mysqli_stmt_close($stmt);
}

// Validasi password
if (empty($password)) {
    $errors[] = "Password harus diisi";
} elseif (strlen($password) < 8) {
    $errors[] = "Password minimal 8 karakter";
} elseif ($password !== $confirm_password) {
    $errors[] = "Konfirmasi password tidak cocok";
}

// Jika tidak ada error, proses insert
if (empty($errors)) {
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
    $role = 'admin'; 

    $stmt = mysqli_prepare($conn, "INSERT INTO user (username, password, status_user, role) VALUES (?, ?, ?, ?)");
    mysqli_stmt_bind_param($stmt, "ssss", $username, $hashed_password, $status, $role);
    
    if (mysqli_stmt_execute($stmt)) {
        $_SESSION['success'] = "Admin baru berhasil ditambahkan";
    } else {
        $_SESSION['error'] = "Gagal menambahkan admin: " . mysqli_error($conn);
    }
    mysqli_stmt_close($stmt);
} else {
    $_SESSION['error'] = implode("<br>", $errors);
}

// Redirect kembali
header("Location: admin_kelola_user.php");
exit();