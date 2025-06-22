<?php
include 'db_config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = (int)$_POST['id_pendaftaran'];
    $status = $_POST['status_verifikasi'];
    $catatan = $_POST['catatan_admin'];

    $stmt = $conn->prepare("UPDATE dokumen SET status_verifikasi = ?, catatan_admin = ? WHERE id_pendaftaran = ?");
    $stmt->bind_param("ssi", $status, $catatan, $id);
    $stmt->execute();
    $stmt->close();

    header("Location: admin_view_detail.php?id=$id&verifikasi=success");
    exit;
}
?>
