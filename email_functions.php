<?php
// email_functions.php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php'; // Pastikan sudah install PHPMailer via Composer

class EmailService {
    private $mailer;
    
    public function __construct() {
        $this->mailer = new PHPMailer(true);
        $this->setupSMTP();
    }
    
    private function setupSMTP() {
        try {
            // Server settings
            $this->mailer->isSMTP();
            $this->mailer->Host       = 'smtp.gmail.com'; // Ganti dengan SMTP server Anda
            $this->mailer->SMTPAuth   = true;
            $this->mailer->Username   = 'daffarasula1122@gmail.com'; // Email pengirim
            $this->mailer->Password   = 'magg tcre ndyl zbxa'; // App password Gmail
            $this->mailer->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $this->mailer->Port       = 587;
            
            // Recipients
            $this->mailer->setFrom('daffarasula1122@gmail.com', 'SPMB SMK ArdsentrA');
            $this->mailer->isHTML(true);
            
        } catch (Exception $e) {
            error_log("Email setup error: " . $e->getMessage());
        }
    }
    
    public function sendAcceptanceEmail($studentData) {
        try {
            $this->mailer->clearAddresses();
            $this->mailer->addAddress($studentData['email'], $studentData['nama_siswa']);
            
            $this->mailer->Subject = 'Selamat! Anda Diterima di SMK ArdsentrA';
            
            $emailBody = $this->getAcceptanceEmailTemplate($studentData);
            $this->mailer->Body = $emailBody;
            
            // Plain text version
            $this->mailer->AltBody = $this->getAcceptanceEmailPlainText($studentData);
            
            $this->mailer->send();
            return true;
            
        } catch (Exception $e) {
            error_log("Email sending error: " . $e->getMessage());
            return false;
        }
    }
    
    public function sendRejectionEmail($studentData, $reason = '') {
        try {
            $this->mailer->clearAddresses();
            $this->mailer->addAddress($studentData['email'], $studentData['nama_siswa']);
            
            $this->mailer->Subject = 'Informasi Status Pendaftaran - SMK ArdsentrA';
            
            $emailBody = $this->getRejectionEmailTemplate($studentData, $reason);
            $this->mailer->Body = $emailBody;
            
            $this->mailer->AltBody = $this->getRejectionEmailPlainText($studentData, $reason);
            
            $this->mailer->send();
            return true;
            
        } catch (Exception $e) {
            error_log("Email sending error: " . $e->getMessage());
            return false;
        }
    }
    
    public function sendWaitingListEmail($studentData) {
        try {
            $this->mailer->clearAddresses();
            $this->mailer->addAddress($studentData['email'], $studentData['nama_siswa']);
            
            $this->mailer->Subject = 'Status Waiting List - SMK ArdsentrA';
            
            $emailBody = $this->getWaitingListEmailTemplate($studentData);
            $this->mailer->Body = $emailBody;
            
            $this->mailer->AltBody = $this->getWaitingListEmailPlainText($studentData);
            
            $this->mailer->send();
            return true;
            
        } catch (Exception $e) {
            error_log("Email sending error: " . $e->getMessage());
            return false;
        }
    }
    
    private function getAcceptanceEmailTemplate($data) {
        return "
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset='UTF-8'>
            <style>
                body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
                .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                .header { background: #28a745; color: white; padding: 20px; text-align: center; border-radius: 5px 5px 0 0; }
                .content { background: #f8f9fa; padding: 30px; border-radius: 0 0 5px 5px; }
                .success-badge { background: #28a745; color: white; padding: 10px 20px; border-radius: 25px; display: inline-block; margin: 20px 0; }
                .info-box { background: white; padding: 20px; border-left: 4px solid #28a745; margin: 20px 0; }
                .footer { text-align: center; margin-top: 30px; font-size: 12px; color: #666; }
            </style>
        </head>
        <body>
            <div class='container'>
                <div class='header'>
                    <h1>🎉 SELAMAT! 🎉</h1>
                    <h2>SMK ArdsentrA</h2>
                </div>
                <div class='content'>
                    <p>Kepada Yth,<br><strong>" . htmlspecialchars($data['nama_siswa']) . "</strong></p>
                    
                    <div class='success-badge'>
                        ✅ DITERIMA
                    </div>
                    
                    <p>Kami dengan senang hati mengumumkan bahwa Anda telah <strong>DITERIMA</strong> sebagai siswa baru di SMK ArdsentrA untuk tahun ajaran 2025/2026.</p>
                    
                    <div class='info-box'>
                        <h3>Detail Penerimaan:</h3>
                        <ul>
                            <li><strong>Nama:</strong> " . htmlspecialchars($data['nama_siswa']) . "</li>
                            <li><strong>NISN:</strong> " . htmlspecialchars($data['nisn']) . "</li>
                            <li><strong>Jurusan:</strong> " . htmlspecialchars($data['pilih_jurusan']) . "</li>
                            <li><strong>ID Pendaftaran:</strong> " . htmlspecialchars($data['id_pendaftaran']) . "</li>
                        </ul>
                    </div>
                    
                    <h3>Langkah Selanjutnya:</h3>
                    <ol>
                        <li>Konfirmasi kehadiran dalam 7 hari kerja</li>
                        <li>Siapkan berkas-berkas yang diperlukan</li>
                        <li>Hadiri orientasi siswa baru</li>
                        <li>Pembayaran biaya pendaftaran</li>
                    </ol>
                    
                    <p><strong>Batas waktu konfirmasi:</strong> " . date('d F Y', strtotime('+7 days')) . "</p>
                    
                    <p>Untuk informasi lebih lanjut, silakan hubungi:</p>
                    <ul>
                        <li>📞 Telepon: (021) 1234-5678</li>
                        <li>📧 Email: info@smkardsentra.sch.id</li>
                        <li>🌐 Website: www.smkardsentra.sch.id</li>
                    </ul>
                    
                    <p>Selamat bergabung dengan keluarga besar SMK ArdsentrA!</p>
                    
                    <div class='footer'>
                        <p>Email ini dikirim secara otomatis. Mohon tidak membalas email ini.</p>
                        <p>&copy; " . date('Y') . " SMK ArdsentrA. Semua hak dilindungi.</p>
                    </div>
                </div>
            </div>
        </body>
        </html>";
    }
    
    private function getAcceptanceEmailPlainText($data) {
        return "
SELAMAT! ANDA DITERIMA DI SMK ArdsentrA

Kepada Yth,
" . $data['nama_siswa'] . "

Kami dengan senang hati mengumumkan bahwa Anda telah DITERIMA sebagai siswa baru di SMK ArdsentrA untuk tahun ajaran 2025/2026.

Detail Penerimaan:
- Nama: " . $data['nama_siswa'] . "
- NISN: " . $data['nisn'] . "
- Jurusan: " . $data['pilih_jurusan'] . "
- ID Pendaftaran: " . $data['id_pendaftaran'] . "

Langkah Selanjutnya:
1. Konfirmasi kehadiran dalam 7 hari kerja
2. Siapkan berkas-berkas yang diperlukan
3. Hadiri orientasi siswa baru
4. Pembayaran biaya pendaftaran

Batas waktu konfirmasi: " . date('d F Y', strtotime('+7 days')) . "

Untuk informasi lebih lanjut:
- Telepon: (021) 1234-5678
- Email: info@smkardsentra.sch.id
- Website: www.smkardsentra.sch.id

Selamat bergabung dengan keluarga besar SMK ArdsentrA!

---
Email otomatis - Mohon tidak membalas
(c) " . date('Y') . " SMK ArdsentrA
        ";
    }
    
    private function getRejectionEmailTemplate($data, $reason) {
        return "
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset='UTF-8'>
            <style>
                body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
                .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                .header { background: #dc3545; color: white; padding: 20px; text-align: center; border-radius: 5px 5px 0 0; }
                .content { background: #f8f9fa; padding: 30px; border-radius: 0 0 5px 5px; }
                .info-box { background: white; padding: 20px; border-left: 4px solid #dc3545; margin: 20px 0; }
                .footer { text-align: center; margin-top: 30px; font-size: 12px; color: #666; }
            </style>
        </head>
        <body>
            <div class='container'>
                <div class='header'>
                    <h2>SMK ArdsentrA</h2>
                    <h3>Informasi Status Pendaftaran</h3>
                </div>
                <div class='content'>
                    <p>Kepada Yth,<br><strong>" . htmlspecialchars($data['nama_siswa']) . "</strong></p>
                    
                    <p>Terima kasih atas minat Anda untuk bergabung dengan SMK ArdsentrA. Setelah melalui proses seleksi yang ketat, dengan berat hati kami informasikan bahwa pendaftaran Anda belum dapat kami terima pada periode ini.</p>
                    
                    <div class='info-box'>
                        <h3>Detail Pendaftaran:</h3>
                        <ul>
                            <li><strong>Nama:</strong> " . htmlspecialchars($data['nama_siswa']) . "</li>
                            <li><strong>NISN:</strong> " . htmlspecialchars($data['nisn']) . "</li>
                            <li><strong>Jurusan:</strong> " . htmlspecialchars($data['pilih_jurusan']) . "</li>
                            <li><strong>ID Pendaftaran:</strong> " . htmlspecialchars($data['id_pendaftaran']) . "</li>
                        </ul>
                        " . (!empty($reason) ? "<p><strong>Catatan:</strong> " . htmlspecialchars($reason) . "</p>" : "") . "
                    </div>
                    
                    <p>Kami menghargai usaha dan waktu yang telah Anda investasikan dalam proses pendaftaran ini. Keputusan ini tidak mengurangi potensi dan kemampuan Anda.</p>
                    
                    <p>Kami mendorong Anda untuk terus mengembangkan diri dan mencoba peluang lain yang tersedia.</p>
                    
                    <p>Untuk informasi lebih lanjut, silakan hubungi:</p>
                    <ul>
                        <li>📞 Telepon: (021) 1234-5678</li>
                        <li>📧 Email: info@smkardsentra.sch.id</li>
                    </ul>
                    
                    <p>Terima kasih dan semoga sukses selalu.</p>
                    
                    <div class='footer'>
                        <p>Email ini dikirim secara otomatis. Mohon tidak membalas email ini.</p>
                        <p>&copy; " . date('Y') . " SMK ArdsentrA. Semua hak dilindungi.</p>
                    </div>
                </div>
            </div>
        </body>
        </html>";
    }
    
    private function getRejectionEmailPlainText($data, $reason) {
        return "
SMK ArdsentrA - Informasi Status Pendaftaran

Kepada Yth,
" . $data['nama_siswa'] . "

Terima kasih atas minat Anda untuk bergabung dengan SMK ArdsentrA. Setelah melalui proses seleksi yang ketat, dengan berat hati kami informasikan bahwa pendaftaran Anda belum dapat kami terima pada periode ini.

Detail Pendaftaran:
- Nama: " . $data['nama_siswa'] . "
- NISN: " . $data['nisn'] . "
- Jurusan: " . $data['pilih_jurusan'] . "
- ID Pendaftaran: " . $data['id_pendaftaran'] . "

" . (!empty($reason) ? "Catatan: " . $reason . "\n" : "") . "

Kami menghargai usaha dan waktu yang telah Anda investasikan dalam proses pendaftaran ini. Keputusan ini tidak mengurangi potensi dan kemampuan Anda.

Untuk informasi lebih lanjut:
- Telepon: (021) 1234-5678
- Email: info@smkardsentra.sch.id

Terima kasih dan semoga sukses selalu.

---
Email otomatis - Mohon tidak membalas
(c) " . date('Y') . " SMK ArdsentrA
        ";
    }
    
    private function getWaitingListEmailTemplate($data) {
        return "
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset='UTF-8'>
            <style>
                body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
                .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                .header { background: #ffc107; color: #212529; padding: 20px; text-align: center; border-radius: 5px 5px 0 0; }
                .content { background: #f8f9fa; padding: 30px; border-radius: 0 0 5px 5px; }
                .warning-badge { background: #ffc107; color: #212529; padding: 10px 20px; border-radius: 25px; display: inline-block; margin: 20px 0; }
                .info-box { background: white; padding: 20px; border-left: 4px solid #ffc107; margin: 20px 0; }
                .footer { text-align: center; margin-top: 30px; font-size: 12px; color: #666; }
            </style>
        </head>
        <body>
            <div class='container'>
                <div class='header'>
                    <h2>SMK ArdsentrA</h2>
                    <h3>Status Waiting List</h3>
                </div>
                <div class='content'>
                    <p>Kepada Yth,<br><strong>" . htmlspecialchars($data['nama_siswa']) . "</strong></p>
                    
                    <div class='warning-badge'>
                        ⏳ WAITING LIST
                    </div>
                    
                    <p>Terima kasih atas minat Anda untuk bergabung dengan SMK ArdsentrA. Kami informasikan bahwa pendaftaran Anda saat ini berada dalam <strong>WAITING LIST</strong>.</p>
                    
                    <div class='info-box'>
                        <h3>Detail Pendaftaran:</h3>
                        <ul>
                            <li><strong>Nama:</strong> " . htmlspecialchars($data['nama_siswa']) . "</li>
                            <li><strong>NISN:</strong> " . htmlspecialchars($data['nisn']) . "</li>
                            <li><strong>Jurusan:</strong> " . htmlspecialchars($data['pilih_jurusan']) . "</li>
                            <li><strong>ID Pendaftaran:</strong> " . htmlspecialchars($data['id_pendaftaran']) . "</li>
                        </ul>
                    </div>
                    
                    <p>Artinya:</p>
                    <ul>
                        <li>Anda memenuhi syarat untuk diterima</li>
                        <li>Menunggu ketersediaan slot/kursi</li>
                        <li>Akan dihubungi jika ada slot tersedia</li>
                        <li>Mohon bersabar menunggu informasi lebih lanjut</li>
                    </ul>
                    
                    <p>Kami akan segera menghubungi Anda jika ada perkembangan. Pastikan nomor telepon dan email Anda selalu aktif.</p>
                    
                    <p>Untuk informasi lebih lanjut, silakan hubungi:</p>
                    <ul>
                        <li>📞 Telepon: (021) 1234-5678</li>
                        <li>📧 Email: info@smkardsentra.sch.id</li>
                    </ul>
                    
                    <div class='footer'>
                        <p>Email ini dikirim secara otomatis. Mohon tidak membalas email ini.</p>
                        <p>&copy; " . date('Y') . " SMK ArdsentrA. Semua hak dilindungi.</p>
                    </div>
                </div>
            </div>
        </body>
        </html>";
    }
    
    private function getWaitingListEmailPlainText($data) {
        return "
SMK ArdsentrA - Status Waiting List

Kepada Yth,
" . $data['nama_siswa'] . "

Terima kasih atas minat Anda untuk bergabung dengan SMK ArdsentrA. Kami informasikan bahwa pendaftaran Anda saat ini berada dalam WAITING LIST.

Detail Pendaftaran:
- Nama: " . $data['nama_siswa'] . "
- NISN: " . $data['nisn'] . "
- Jurusan: " . $data['pilih_jurusan'] . "
- ID Pendaftaran: " . $data['id_pendaftaran'] . "

Artinya:
- Anda memenuhi syarat untuk diterima
- Menunggu ketersediaan slot/kursi
- Akan dihubungi jika ada slot tersedia
- Mohon bersabar menunggu informasi lebih lanjut

Kami akan segera menghubungi Anda jika ada perkembangan. Pastikan nomor telepon dan email Anda selalu aktif.

Untuk informasi lebih lanjut:
- Telepon: (021) 1234-5678
- Email: info@smkardsentra.sch.id

---
Email otomatis - Mohon tidak membalas
(c) " . date('Y') . " SMK ArdsentrA
        ";
    }
}

// Fungsi helper untuk logging
function logEmailActivity($id_pendaftaran, $email, $status, $success) {
    global $conn;
    
    $log_status = $success ? 'sent' : 'failed';
    $sql = "INSERT INTO email_log (id_pendaftaran, email_recipient, email_type, status, sent_at) 
            VALUES (?, ?, ?, ?, NOW())";
    
    $stmt = $conn->prepare($sql);
    if ($stmt) {
        $stmt->bind_param("isss", $id_pendaftaran, $email, $status, $log_status);
        $stmt->execute();
        $stmt->close();
    }
}
?>