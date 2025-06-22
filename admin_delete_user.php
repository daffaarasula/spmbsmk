<?php
session_start();

// Pastikan hanya admin yang bisa mengakses
if (!isset($_SESSION['id_user']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

include 'db_config.php';

// Validasi ID dari parameter
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    $_SESSION['error'] = "ID admin tidak valid";
    header("Location: admin_kelola_user.php");
    exit();
}

$admin_id = intval($_GET['id']);
$current_admin_id = $_SESSION['id_user']; // <- Ganti dari admin_user_id ke id_user

// Cegah admin menghapus akun sendiri
if ($admin_id == $current_admin_id) {
    $_SESSION['error'] = "Tidak dapat menghapus akun sendiri";
    header("Location: admin_kelola_user.php");
    exit();
}

// Cek apakah user yang akan dihapus adalah admin
$check_query = "SELECT id_user FROM user WHERE id_user = ? AND role = 'admin'";
$stmt = mysqli_prepare($conn, $check_query);
mysqli_stmt_bind_param($stmt, "i", $admin_id);
mysqli_stmt_execute($stmt);
mysqli_stmt_store_result($stmt);

if (mysqli_stmt_num_rows($stmt) == 0) {
    $_SESSION['error'] = "Admin tidak ditemukan atau bukan admin";
    header("Location: admin_kelola_user.php");
    exit();
}
mysqli_stmt_close($stmt);

// Lakukan penghapusan
$delete_query = "DELETE FROM user WHERE id_user = ?";
$stmt = mysqli_prepare($conn, $delete_query);
mysqli_stmt_bind_param($stmt, "i", $admin_id);
$result = mysqli_stmt_execute($stmt);

if ($result) {
    $_SESSION['success'] = "Admin berhasil dihapus";
} else {
    $_SESSION['error'] = "Gagal menghapus admin: " . mysqli_error($conn);
}

mysqli_close($conn);
header("Location: admin_kelola_user.php");
exit();
?>
