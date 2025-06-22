<?php
// admin_update_status.php
session_start();

if (!isset($_SESSION['id_user']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}
include 'db_config.php';
include 'email_functions.php'; // Include email functions

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: admin_dashboard.php?error=Invalid request method.");
    exit();
}

if (!isset($_POST['id_pendaftaran']) || !isset($_POST['status_pendaftaran'])) {
    header("Location: admin_dashboard.php?error=Missing required data.");
    exit();
}

$id_pendaftaran = filter_var($_POST['id_pendaftaran'], FILTER_SANITIZE_NUMBER_INT);
$new_status = htmlspecialchars(trim($_POST['status_pendaftaran']), ENT_QUOTES, 'UTF-8');

// Validasi status yang diperbolehkan
$allowed_statuses = ['Pending Review', 'Accepted', 'Rejected', 'Waiting List'];
if (!in_array($new_status, $allowed_statuses)) {
    header("Location: admin_view_detail.php?id=" . $id_pendaftaran . "&status_error=Invalid status selected.");
    exit();
}

// Ambil data lengkap siswa sebelum update
$sql_get_data = "SELECT 
                    p.*, 
                    jk.jeniskelamin, 
                    ag.nama_agama,
                    do.nama_ayah, do.pekerjaan_ayah, do.nama_ibu, do.pekerjaan_ibu, do.alamat AS alamat_ortu
                FROM pendaftaran p
                JOIN jenis_kelamin jk ON p.id_jk = jk.id_jk
                JOIN agama ag ON p.id_agama = ag.id_agama
                LEFT JOIN data_ortu do ON p.id_pendaftaran = do.id_pendaftaran
                WHERE p.id_pendaftaran = ?";

$stmt_get = $conn->prepare($sql_get_data);
if (!$stmt_get) {
    header("Location: admin_view_detail.php?id=" . $id_pendaftaran . "&status_error=Database prepare error.");
    exit();
}

$stmt_get->bind_param("i", $id_pendaftaran);
$stmt_get->execute();
$result = $stmt_get->get_result();

if ($result->num_rows == 0) {
    header("Location: admin_dashboard.php?error=Registration not found.");
    exit();
}

$student_data = $result->fetch_assoc();
$old_status = $student_data['status_pendaftaran'];
$stmt_get->close();

// Update status di database
$sql_update = "UPDATE pendaftaran SET status_pendaftaran = ?, updated_at = NOW() WHERE id_pendaftaran = ?";
$stmt_update = $conn->prepare($sql_update);

if (!$stmt_update) {
    header("Location: admin_view_detail.php?id=" . $id_pendaftaran . "&status_error=Database prepare error: " . $conn->error);
    exit();
}

$stmt_update->bind_param("si", $new_status, $id_pendaftaran);

if ($stmt_update->execute()) {
    $stmt_update->close();
    
    // Kirim email jika status berubah dan bukan "Pending Review"
    $should_send_email = ($old_status !== $new_status) && ($new_status !== 'Pending Review');
    $email_sent = false;
    
    if ($should_send_email) {
        $emailService = new EmailService();
        
        try {
            switch ($new_status) {
                case 'Accepted':
                    $email_sent = $emailService->sendAcceptanceEmail($student_data);
                    break;
                    
                case 'Rejected':
                    // Anda bisa menambahkan alasan penolakan jika diperlukan
                    $rejection_reason = isset($_POST['rejection_reason']) ? $_POST['rejection_reason'] : '';
                    $email_sent = $emailService->sendRejectionEmail($student_data, $rejection_reason);
                    break;
                    
                case 'Waiting List':
                    $email_sent = $emailService->sendWaitingListEmail($student_data);
                    break;
            }
            
            // Log aktivitas email
            logEmailActivity($id_pendaftaran, $student_data['email'], $new_status, $email_sent);
            
        } catch (Exception $e) {
            error_log("Email sending failed: " . $e->getMessage());
            $email_sent = false;
        }
    }
    
    // Redirect dengan pesan sukses
    $success_message = "Status berhasil diperbarui menjadi: " . $new_status;
    if ($should_send_email) {
        if ($email_sent) {
            $success_message .= " Email notifikasi telah dikirim ke " . $student_data['email'];
        } else {
            $success_message .= " Namun gagal mengirim email notifikasi.";
        }
    }
    
    header("Location: admin_view_detail.php?id=" . $id_pendaftaran . "&status_success=" . urlencode($success_message));
    
} else {
    $stmt_update->close();
    header("Location: admin_view_detail.php?id=" . $id_pendaftaran . "&status_error=Failed to update status: " . $conn->error);
}

$conn->close();
exit();
?>