<?php
session_start();
if (!isset($_SESSION['admin_user_id'])) {
    // Bisa redirect ke login atau kirim header 403 Forbidden
    header("HTTP/1.1 403 Forbidden");
    exit("Akses ditolak. Silakan login sebagai admin.");
}
include 'db_config.php';

// Ambil parameter filter jika ada
$search_query = isset($_GET['search']) ? trim($_GET['search']) : "";
$filter_jurusan = isset($_GET['filter_jurusan']) ? trim($_GET['filter_jurusan']) : "";

$sql = "SELECT 
            p.id_pendaftaran, p.nisn, p.nama_siswa, p.tempat_tgl_lahir, 
            jk.jeniskelamin, ag.nama_agama, p.alamat AS alamat_siswa, p.asal_sekolah, p.pilih_jurusan, 
            p.no_handphone, p.email, p.status_pendaftaran,
            do.nama_ayah, do.pekerjaan_ayah, do.nama_ibu, do.pekerjaan_ibu, do.alamat AS alamat_ortu,
            d.pasfoto, d.ijazah, d.ktp, d.kk
        FROM pendaftaran p
        JOIN jenis_kelamin jk ON p.id_jk = jk.id_jk
        JOIN agama ag ON p.id_agama = ag.id_agama
        LEFT JOIN data_ortu do ON p.id_pendaftaran = do.id_pendaftaran
        LEFT JOIN dokumen d ON p.id_pendaftaran = d.id_pendaftaran
        WHERE 1=1"; // Base condition

$params = [];
$types = "";

if ($search_query) {
    $sql .= " AND (p.nama_siswa LIKE ? OR p.nisn LIKE ? OR p.email LIKE ?)";
    $searchTerm = "%" . $search_query . "%";
    array_push($params, $searchTerm, $searchTerm, $searchTerm);
    $types .= "sss";
}
if ($filter_jurusan) {
    $sql .= " AND p.pilih_jurusan = ?";
    array_push($params, $filter_jurusan);
    $types .= "s";
}
$sql .= " ORDER BY p.id_pendaftaran ASC";

$stmt = $conn->prepare($sql);
if ($params) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$result = $stmt->get_result();

// Nama file CSV
$filename = "ppdb_data_smkxyz_" . date('YmdHis') . ".csv";

// Set header untuk download
header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename="' . $filename . '"');

// Buka output stream
$output = fopen('php://output', 'w');

// Header Kolom CSV (sesuaikan dengan data yang ingin diekspor)
$header_columns = [
    'ID Pendaftaran', 'NISN', 'Nama Siswa', 'Tempat Tgl Lahir', 'Jenis Kelamin', 'Agama', 
    'Alamat Siswa', 'Asal Sekolah', 'Pilihan Jurusan', 'No Handphone', 'Email Siswa', 'Status Pendaftaran',
    'Nama Ayah', 'Pekerjaan Ayah', 'Nama Ibu', 'Pekerjaan Ibu', 'Alamat Orang Tua',
    'Path Pasfoto', 'Path Ijazah', 'Path KTP Ortu', 'Path KK'
];
fputcsv($output, $header_columns);

// Tulis data baris per baris
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $csv_row = [
            $row['id_pendaftaran'], $row['nisn'], $row['nama_siswa'], $row['tempat_tgl_lahir'], $row['jeniskelamin'], $row['nama_agama'],
            $row['alamat_siswa'], $row['asal_sekolah'], $row['pilih_jurusan'], $row['no_handphone'], $row['email'], $row['status_pendaftaran'],
            $row['nama_ayah'], $row['pekerjaan_ayah'], $row['nama_ibu'], $row['pekerjaan_ibu'], $row['alamat_ortu'],
            $row['pasfoto'], $row['ijazah'], $row['ktp'], $row['kk']
        ];
        fputcsv($output, $csv_row);
    }
}

fclose($output);
$stmt->close();
$conn->close();
exit();
?>