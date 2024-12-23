<?php
session_start();
require 'inc/db_connect.php';

if (!isset($_SESSION['loggedin']) || $_SESSION['role'] !== 'employee') {
    header('Location: login.php');
    exit();
}

$user_id = $_SESSION['user_id'];

// Ambil data log request untuk user login
$query = "
    SELECT rl.log_id, r.nama, rl.jumlah_diambil, rl.status
    FROM request_log rl
    INNER JOIN request r ON rl.request_id = r.request_id
    WHERE rl.user_id = ?
";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

include './inc/header.php';
include './inc/sidebar.php';
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <title>Employee - Work</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</head>

<body>
    <div class="container mt-5">
        <h2 class="mb-4">Daftar Request Anda</h2>
        <table class="table table-striped table-bordered">
            <thead class="thead-dark">
                <tr>
                    <th>No</th>
                    <th>Nama</th>
                    <th>Jumlah Diambil</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $no = 1;
                while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo $no++; ?></td>
                        <td><?php echo htmlspecialchars($row['nama']); ?></td>
                        <td><?php echo htmlspecialchars($row['jumlah_diambil']); ?></td>
                        <td>
                            <?php
                            // Menampilkan status sesuai kondisi
                            if ($row['status'] == 'in_progress'): ?>
                                <span class="text-info">In Progress</span>
                            <?php elseif ($row['status'] == 'waiting_admin'): ?>
                                <span class="text-warning">Waiting Admin</span>
                            <?php elseif ($row['status'] == 'completed'): ?>
                                <span class="text-success">Completed</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if ($row['status'] == 'in_progress'): ?> 
                                <!-- Tombol untuk upload gambar jika statusnya in_progress -->
                                <button class="btn btn-success btn-sm" data-toggle="modal" data-target="#uploadModal<?php echo $row['log_id']; ?>">
                                    Upload Bukti Selesai
                                </button>

                                <!-- Modal Upload Gambar -->
                                <div class="modal fade" id="uploadModal<?php echo $row['log_id']; ?>" tabindex="-1" role="dialog">
                                    <div class="modal-dialog" role="document">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title">Upload Bukti Penyelesaian</h5>
                                                <button type="button" class="close" data-dismiss="modal">&times;</button>
                                            </div>
                                            <div class="modal-body">
                                                <form action="upload_completion.php" method="POST" enctype="multipart/form-data">
                                                    <input type="hidden" name="log_id" value="<?php echo htmlspecialchars($row['log_id']); ?>">
                                                    <div class="form-group">
                                                        <label for="bukti_selesai">Pilih Gambar</label>
                                                        <input type="file" name="bukti_selesai" class="form-control" required>
                                                    </div>
                                                    <button type="submit" class="btn btn-primary">Upload</button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php else: ?>
                                <span class="text-muted">Menunggu</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</body>

</html>
