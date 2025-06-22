<?php
session_start();
require_once 'db_config.php';

// Pastikan admin yang login
if (!isset($_SESSION['id_user']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

if (isset($_GET['id_pendaftaran'])) {
    $id_pendaftaran = intval($_GET['id_pendaftaran']);

    // Ambil NISN dari tabel pendaftaran
    $stmt = $conn->prepare("SELECT nisn FROM pendaftaran WHERE id_pendaftaran = ?");
    $stmt->bind_param("i", $id_pendaftaran);
    $stmt->execute();
    $stmt->bind_result($nisn);
    $stmt->fetch();
    $stmt->close();

    // Hapus dari tabel user (asumsi username = nisn)
    $stmtUser = $conn->prepare("DELETE FROM user WHERE username = ?");
    $stmtUser->bind_param("s", $nisn);
    $stmtUser->execute();
    $stmtUser->close();

    // Hapus data terkait (jika tidak pakai ON DELETE CASCADE)
    $conn->query("DELETE FROM dokumen WHERE id_pendaftaran = $id_pendaftaran");
    $conn->query("DELETE FROM data_ortu WHERE id_pendaftaran = $id_pendaftaran");

    // Hapus data pendaftaran
    $stmtHapus = $conn->prepare("DELETE FROM pendaftaran WHERE id_pendaftaran = ?");
    $stmtHapus->bind_param("i", $id_pendaftaran);
    $stmtHapus->execute();
    $stmtHapus->close();

    header("Location: admin_dashboard.php?hapus=berhasil");
    exit();
} else {
    echo "ID pendaftaran tidak valid.";
}
?>
