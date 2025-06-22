<?php
session_start();
include 'db_config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password_attempt = $_POST['password'];

    $stmt = $conn->prepare("SELECT id_user, username, password, status_user, role FROM user WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 1) {
        $user = $result->fetch_assoc();

        if (password_verify($password_attempt, $user['password']) && $user['status_user'] === 'aktif') {
            // Simpan sesi
            $_SESSION['id_user'] = $user['id_user'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role'];

            // Arahkan berdasarkan role
            if ($user['role'] === 'admin') {
                header("Location: admin_dashboard.php");
            } elseif ($user['role'] === 'pendaftar') {
                header("Location: pendaftar_dashboard.php");
            } else {
                header("Location: login.php?error=" . urlencode("Role tidak dikenal."));
            }
            exit();
        } else {
            header("Location: login.php?error=" . urlencode("Password salah atau akun tidak aktif."));
            exit();
        }
    } else {
        header("Location: login.php?error=" . urlencode("Username tidak ditemukan."));
        exit();
    }

    $stmt->close();
    $conn->close();
} else {
    header("Location: login.php");
    exit();
}
