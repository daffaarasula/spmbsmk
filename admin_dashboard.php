<?php
// admin_dashboard.php
session_start();

include 'db_config.php';

// --- Handle Delete Action ---
if (isset($_POST['delete_id']) && is_numeric($_POST['delete_id'])) {
    $delete_id = (int)$_POST['delete_id'];
    
    // Begin transaction
    $conn->begin_transaction();
    
    try {
        // Delete related records first (due to foreign key constraints)
        $delete_data_ortu = $conn->prepare("DELETE FROM data_ortu WHERE id_pendaftaran = ?");
        $delete_data_ortu->bind_param("i", $delete_id);
        $delete_data_ortu->execute();
        
        $delete_dokumen = $conn->prepare("DELETE FROM dokumen WHERE id_pendaftaran = ?");
        $delete_dokumen->bind_param("i", $delete_id);
        $delete_dokumen->execute();
        
        // Delete main registration record
        $delete_pendaftaran = $conn->prepare("DELETE FROM pendaftaran WHERE id_pendaftaran = ?");
        $delete_pendaftaran->bind_param("i", $delete_id);
        $delete_pendaftaran->execute();
        
        // Commit transaction
        $conn->commit();
        
        // Set success message
        $_SESSION['success_message'] = "Data pendaftar berhasil dihapus.";
        
    } catch (Exception $e) {
        // Rollback transaction on error
        $conn->rollback();
        $_SESSION['error_message'] = "Gagal menghapus data pendaftar: " . $e->getMessage();
    }
    
    // Redirect to prevent form resubmission
    header("Location: admin_dashboard.php");
    exit();
}

// --- Ambil Data Statistik ---
$total_pendaftar_sql = "SELECT COUNT(*) AS total FROM pendaftaran";
$total_pendaftar_result = $conn->query($total_pendaftar_sql);
$total_pendaftar = $total_pendaftar_result->fetch_assoc()['total'] ?? 0;

$jurusan_stats_sql = "SELECT pilih_jurusan, COUNT(*) AS jumlah FROM pendaftaran GROUP BY pilih_jurusan ORDER BY jumlah DESC";
$jurusan_stats_result = $conn->query($jurusan_stats_sql);
$jurusan_stats = [];
if ($jurusan_stats_result && $jurusan_stats_result->num_rows > 0) {
    while ($row_js = $jurusan_stats_result->fetch_assoc()) {
        $jurusan_stats[] = $row_js;
    }
}

$status_counts_sql = "SELECT status_pendaftaran, COUNT(*) as count FROM pendaftaran GROUP BY status_pendaftaran";
$status_counts_result = $conn->query($status_counts_sql);
$status_counts = [];
if ($status_counts_result) {
    while ($row_sc = $status_counts_result->fetch_assoc()) {
        $status_counts[$row_sc['status_pendaftaran']] = $row_sc['count'];
    }
}
$accepted_count = $status_counts['Accepted'] ?? 0;
$pending_review_count = $status_counts['Pending Review'] ?? 0;
$rejected_count = $status_counts['Rejected'] ?? 0;

// --- Pengaturan Pagination ---
$limit = 10;
$page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;

// --- Handle Search dan Filter ---
$search_query = isset($_GET['search']) ? trim($_GET['search']) : "";
$filter_jurusan = isset($_GET['filter_jurusan']) ? trim($_GET['filter_jurusan']) : "";

$sql_select_pendaftar = "SELECT 
            p.id_pendaftaran, p.nisn, p.nama_siswa, p.asal_sekolah, p.pilih_jurusan, p.status_pendaftaran,
            d.pasfoto, d.ijazah
        FROM pendaftaran p
        LEFT JOIN dokumen d ON p.id_pendaftaran = d.id_pendaftaran";

$sql_where_clauses = " WHERE 1=1";
$params = [];
$types = "";

if ($search_query) {
    $sql_where_clauses .= " AND (p.nama_siswa LIKE ? OR p.nisn LIKE ?)";
    $searchTerm = "%" . $search_query . "%";
    array_push($params, $searchTerm, $searchTerm);
    $types .= "ss";
}
if ($filter_jurusan) {
    $sql_where_clauses .= " AND p.pilih_jurusan = ?";
    array_push($params, $filter_jurusan);
    $types .= "s";
}

$count_sql = "SELECT COUNT(*) AS total_filtered FROM pendaftaran p " . $sql_where_clauses;
$stmt_count = $conn->prepare($count_sql);
if (!empty($params)) {
    $stmt_count->bind_param($types, ...$params);
}
$stmt_count->execute();
$total_filtered_records = $stmt_count->get_result()->fetch_assoc()['total_filtered'] ?? 0;
$total_pages = ceil($total_filtered_records / $limit);
$stmt_count->close();

$sql_pendaftar_paginated = $sql_select_pendaftar . $sql_where_clauses . " ORDER BY p.id_pendaftaran DESC LIMIT ? OFFSET ?";
$current_params_for_main_query = $params;
array_push($current_params_for_main_query, $limit, $offset);
$current_types_for_main_query = $types . "ii";

$stmt_main = $conn->prepare($sql_pendaftar_paginated);
if (false === $stmt_main) die("Error preparing main query: " . $conn->error);
if (!empty($current_params_for_main_query)) {
    $stmt_main->bind_param($current_types_for_main_query, ...$current_params_for_main_query);
}
$stmt_main->execute();
$result_pendaftar = $stmt_main->get_result();

?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - SPMB SMK XYZ</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <style>
        body { background-color: #f8f9fa; }
        .stats-card {
            border: none;
            border-radius: 0.5rem;
            box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
            transition: transform 0.2s ease-in-out;
        }
        .stats-card:hover { transform: translateY(-5px); }
        .stats-icon-wrapper {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.75rem;
        }
        .table thead th { background-color: #e9ecef; }
        .badge.rounded-pill { padding: 0.4em 0.8em; font-size: 0.85em; }
        .btn-group-actions .btn {
            margin-right: 2px;
        }
        .btn-group-actions .btn:last-child {
            margin-right: 0;
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark sticky-top shadow-sm">
        <div class="collapse navbar-collapse" id="adminNavbar">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                <li class="nav-item">
                    <a class="nav-link active" aria-current="page" href="admin_dashboard.php"><i class="bi bi-speedometer2 me-1"></i>Dashboard Pendaftar</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="admin_kelola_user.php"><i class="bi bi-person-plus-fill me-1"></i>Kelola Admin</a>
                </li>
            </ul>
            <ul class="navbar-nav ms-auto mb-2 mb-lg-0">
                <li class="nav-item">
                    <span class="navbar-text me-3">Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?>!</span>
                </li>
                <li class="nav-item">
                    <a class="btn btn-outline-light btn-sm" href="admin_logout.php"><i class="bi bi-box-arrow-right me-1"></i>Logout</a>
                </li>
            </ul>
        </div>
    </nav>

    <div class="container-fluid mt-4">
        <h2 class="mb-4 display-6">Dashboard Admin SPMB</h2>

        <!-- Alert Messages -->
        <?php if (isset($_SESSION['success_message'])): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="bi bi-check-circle-fill me-2"></i>
                <?php echo htmlspecialchars($_SESSION['success_message']); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            <?php unset($_SESSION['success_message']); ?>
        <?php endif; ?>

        <?php if (isset($_SESSION['error_message'])): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="bi bi-exclamation-triangle-fill me-2"></i>
                <?php echo htmlspecialchars($_SESSION['error_message']); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            <?php unset($_SESSION['error_message']); ?>
        <?php endif; ?>

        <div class="row g-4 mb-4">
            <div class="col-xl-3 col-md-6">
                <div class="card stats-card bg-light">
                    <div class="card-body d-flex align-items-center">
                        <div class="stats-icon-wrapper bg-primary bg-opacity-10 text-primary me-3">
                            <i class="bi bi-people-fill"></i>
                        </div>
                        <div>
                            <h6 class="card-subtitle text-muted mb-1">Total Pendaftar</h6>
                            <h3 class="card-title fw-bold mb-0"><?php echo $total_pendaftar; ?></h3>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-md-6">
                <div class="card stats-card bg-light">
                     <div class="card-body d-flex align-items-center">
                        <div class="stats-icon-wrapper bg-success bg-opacity-10 text-success me-3">
                            <i class="bi bi-person-check-fill"></i>
                        </div>
                        <div>
                            <h6 class="card-subtitle text-muted mb-1">Diterima</h6>
                            <h3 class="card-title fw-bold mb-0"><?php echo $accepted_count; ?></h3>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-md-6">
                <div class="card stats-card bg-light">
                    <div class="card-body d-flex align-items-center">
                        <div class="stats-icon-wrapper bg-warning bg-opacity-10 text-warning me-3">
                            <i class="bi bi-hourglass-split"></i>
                        </div>
                        <div>
                            <h6 class="card-subtitle text-muted mb-1">Pending Review</h6>
                            <h3 class="card-title fw-bold mb-0"><?php echo $pending_review_count; ?></h3>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-md-6">
                 <div class="card stats-card bg-light">
                    <div class="card-body d-flex align-items-center">
                        <div class="stats-icon-wrapper bg-danger bg-opacity-10 text-danger me-3">
                            <i class="bi bi-person-x-fill"></i>
                        </div>
                        <div>
                            <h6 class="card-subtitle text-muted mb-1">Ditolak</h6>
                            <h3 class="card-title fw-bold mb-0"><?php echo $rejected_count; ?></h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-light py-3">
                <h4 class="mb-0">Data Pendaftar <small class="text-muted fs-6">(Halaman <?php echo $page; ?> dari <?php echo $total_pages > 0 ? $total_pages : 1; ?>)</small></h4>
            </div>
            <div class="card-body">
                <form method="GET" action="admin_dashboard.php" class="row g-3 align-items-center mb-4">
                    <div class="col-md-5">
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-search"></i></span>
                            <input type="text" name="search" class="form-control" placeholder="Cari Nama/NISN..." value="<?php echo htmlspecialchars($search_query); ?>">
                        </div>
                    </div>
                    <div class="col-md-4">
                         <select name="filter_jurusan" class="form-select">
                            <option value="">Semua Jurusan</option>
                            <?php
                                $distinct_jurusan_sql = "SELECT DISTINCT pilih_jurusan FROM pendaftaran WHERE pilih_jurusan IS NOT NULL AND pilih_jurusan != '' ORDER BY pilih_jurusan";
                                $distinct_jurusan_result = $conn->query($distinct_jurusan_sql);
                                if($distinct_jurusan_result && $distinct_jurusan_result->num_rows > 0){
                                    while($j_row = $distinct_jurusan_result->fetch_assoc()){
                                        $selected = ($filter_jurusan == $j_row['pilih_jurusan']) ? 'selected' : '';
                                        echo "<option value=\"".htmlspecialchars($j_row['pilih_jurusan'])."\" $selected>".htmlspecialchars($j_row['pilih_jurusan'])."</option>";
                                    }
                                }
                            ?>
                        </select>
                    </div>
                    <div class="col-md-auto">
                        <button type="submit" class="btn btn-primary"><i class="bi bi-funnel-fill me-1"></i>Filter</button>
                    </div>
                    <div class="col-md-auto">
                        <a href="admin_dashboard.php" class="btn btn-outline-secondary"><i class="bi bi-arrow-repeat me-1"></i>Reset</a>
                    </div>
                     <div class="col-md-auto ms-md-auto text-end">
                        <a href="admin_export_csv.php?search=<?php echo urlencode($search_query); ?>&filter_jurusan=<?php echo urlencode($filter_jurusan); ?>" class="btn btn-success">
                            <i class="bi bi-file-earmark-spreadsheet-fill me-1"></i>Export CSV
                        </a>
                    </div>
                </form>

                <div class="table-responsive">
                    <table class="table table-hover table-bordered align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>ID</th>
                                <th>NISN</th>
                                <th>Nama Siswa</th>
                                <th>Asal Sekolah</th>
                                <th>Jurusan</th>
                                <th>Status</th>
                                <th>Pasfoto</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if ($result_pendaftar && $result_pendaftar->num_rows > 0): ?>
                                <?php while($row = $result_pendaftar->fetch_assoc()): ?>
                                <tr>
                                    <td><?php echo $row['id_pendaftaran']; ?></td>
                                    <td><?php echo htmlspecialchars($row['nisn']); ?></td>
                                    <td><?php echo htmlspecialchars($row['nama_siswa']); ?></td>
                                    <td><?php echo htmlspecialchars($row['asal_sekolah']); ?></td>
                                    <td><?php echo htmlspecialchars($row['pilih_jurusan']); ?></td>
                                    <td>
                                        <span class="badge rounded-pill bg-<?php
                                            $status_text = strtolower(str_replace(' ', '-', $row['status_pendaftaran'] ?? 'unknown'));
                                            $badge_class = 'secondary';
                                            if ($status_text == 'accepted') $badge_class = 'success';
                                            else if ($status_text == 'rejected') $badge_class = 'danger';
                                            else if ($status_text == 'pending-review') $badge_class = 'warning text-dark';
                                            else if ($status_text == 'documents-verified') $badge_class = 'info text-dark';
                                            else if ($status_text == 'documents-incomplete') $badge_class = 'warning text-dark';
                                            else if ($status_text == 'waiting-list') $badge_class = 'dark';
                                            echo $badge_class; ?>">
                                            <?php echo htmlspecialchars($row['status_pendaftaran'] ?? 'N/A'); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <?php if($row['pasfoto'] && file_exists($row['pasfoto'])): ?>
                                            <img src="<?php echo htmlspecialchars($row['pasfoto']); ?>" alt="Pasfoto" width="40" height="50" class="img-thumbnail">
                                        <?php else: ?>
                                            <span class="text-muted small">N/A</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <div class="btn-group-actions d-flex flex-wrap">
                                            <a href="admin_view_detail.php?id=<?php echo $row['id_pendaftaran']; ?>" class="btn btn-sm btn-outline-primary" title="Lihat Detail">
                                                <i class="bi bi-eye-fill"></i>
                                            </a>
                                            <a href="admin_edit_registration.php?id=<?php echo $row['id_pendaftaran']; ?>" class="btn btn-sm btn-outline-warning" title="Edit Data">
                                                <i class="bi bi-pencil-fill"></i>
                                            </a>
                                            <button type="button" class="btn btn-sm btn-outline-danger" title="Hapus Data" 
                                                    onclick="confirmDelete(<?php echo $row['id_pendaftaran']; ?>, '<?php echo htmlspecialchars($row['nama_siswa'], ENT_QUOTES); ?>')">
                                                <i class="bi bi-trash-fill"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr><td colspan="8" class="text-center py-4">Tidak ada data pendaftar yang ditemukan.</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
                
                <?php if ($total_pages > 1): ?>
                <nav aria-label="Page navigation" class="mt-4">
                    <ul class="pagination justify-content-center">
                        <li class="page-item <?php if($page <= 1){ echo 'disabled'; } ?>">
                            <a class="page-link" href="?page=<?php echo $page-1; ?>&search=<?php echo urlencode($search_query); ?>&filter_jurusan=<?php echo urlencode($filter_jurusan); ?>">Previous</a>
                        </li>
                        <?php for($i = 1; $i <= $total_pages; $i++): ?>
                        <li class="page-item <?php if($page == $i) {echo 'active'; } ?>">
                            <a class="page-link" href="?page=<?php echo $i; ?>&search=<?php echo urlencode($search_query); ?>&filter_jurusan=<?php echo urlencode($filter_jurusan); ?>"><?php echo $i; ?></a>
                        </li>
                        <?php endfor; ?>
                        <li class="page-item <?php if($page >= $total_pages) { echo 'disabled'; } ?>">
                            <a class="page-link" href="?page=<?php echo $page+1; ?>&search=<?php echo urlencode($search_query); ?>&filter_jurusan=<?php echo urlencode($filter_jurusan); ?>">Next</a>
                        </li>
                    </ul>
                </nav>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="deleteModalLabel">
                        <i class="bi bi-exclamation-triangle-fill text-danger me-2"></i>
                        Konfirmasi Hapus Data
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>Apakah Anda yakin ingin menghapus data pendaftar berikut?</p>
                    <div class="alert alert-warning">
                        <strong>Nama:</strong> <span id="deleteName"></span><br>
                        <strong>ID:</strong> <span id="deleteId"></span>
                    </div>
                    <p class="text-danger small">
                        <i class="bi bi-info-circle-fill me-1"></i>
                        <strong>Peringatan:</strong> Tindakan ini akan menghapus semua data terkait pendaftar ini termasuk data orang tua dan dokumen. Tindakan ini tidak dapat dibatalkan.
                    </p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="bi bi-x-circle me-1"></i>Batal
                    </button>
                    <form method="POST" style="display: inline;">
                        <input type="hidden" name="delete_id" id="deleteIdInput">
                        <button type="submit" class="btn btn-danger">
                            <i class="bi bi-trash-fill me-1"></i>Ya, Hapus Data
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <footer class="py-4 bg-light mt-auto">
        <div class="container-fluid px-4">
            <hr class="my-3" style="border-color: #4A5A6A;">
            <p class="text-center small mb-0">&copy; <?php echo date("Y"); ?> SPMB SMK ArdsentrA. Hak Cipta Dilindungi.</p>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function confirmDelete(id, name) {
            document.getElementById('deleteId').textContent = id;
            document.getElementById('deleteName').textContent = name;
            document.getElementById('deleteIdInput').value = id;
            
            var deleteModal = new bootstrap.Modal(document.getElementById('deleteModal'));
            deleteModal.show();
        }
    </script>
</body>
</html>
<?php
if (isset($stmt_main)) $stmt_main->close();
$conn->close();
?>