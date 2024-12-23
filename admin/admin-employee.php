<?php
session_start();
require 'inc/db_connect.php';

// Pastikan user adalah admin
if (!isset($_SESSION['loggedin']) || $_SESSION['role'] !== 'admin') {
    header('Location: login.php');
    exit();
}

$log_query = "
    SELECT rl.log_id, r.nama, rl.jumlah_diambil, rl.bukti_selesai, rl.user_id
    FROM request_log rl
    INNER JOIN request r ON rl.request_id = r.request_id
    WHERE rl.status = 'waiting_admin' AND rl.bukti_selesai IS NOT NULL
";
$log_result = mysqli_query($conn, $log_query);

include './inc/header.php';
include './inc/sidebar.php';

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Admin - Bukti Penyelesaian</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
<div class="container mt-5">
    <h2 class="mb-4">Bukti Penyelesaian</h2>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>No</th>
                <th>User ID</th>
                <th>Nama Produk</th>
                <th>Jumlah Diambil</th>
                <th>Bukti Gambar</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $counter = 1;
            while ($log = mysqli_fetch_assoc($log_result)): ?>
                <tr>
                    <td><?php echo $counter++; ?></td>
                    <td><?php echo htmlspecialchars($log['user_id']); ?></td>
                    <td><?php echo htmlspecialchars($log['nama']); ?></td>
                    <td><?php echo htmlspecialchars($log['jumlah_diambil']); ?></td>
                    <td>
                        <?php if (!empty($log['bukti_selesai'])): ?>
                            <img src="../img/<?php echo htmlspecialchars($log['bukti_selesai']); ?>" alt="Bukti Selesai" style="max-width: 200px;" class="img-thumbnail">
                        <?php else: ?>
                            <span class="text-muted">Belum ada bukti</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <a href="approve_completion.php?log_id=<?php echo $log['log_id']; ?>" class="btn btn-success btn-sm">Approve</a>
                        <a href="reject_completion.php?log_id=<?php echo $log['log_id']; ?>" class="btn btn-danger btn-sm">Reject</a>
                    </td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>
</body>
</html>
