<?php
session_start();
require 'inc/db_connect.php';

// Pastikan user yang login adalah admin atau role yang sesuai
if (!isset($_SESSION['loggedin']) || $_SESSION['role'] !== 'admin') {
    header('Location: login.php');
    exit();
}

// Tangkap tanggal mulai dan tanggal akhir jika form sudah disubmit
$start_date = isset($_POST['start_date']) ? $_POST['start_date'] : '';  
$end_date = isset($_POST['end_date']) ? $_POST['end_date'] : '';

// Query untuk mengambil data gaji setiap employee hanya yang statusnya completed dan dalam rentang tanggal yang dipilih
$query = "
    SELECT u.firstname, SUM(gajiEmployee(rl.jumlah_diambil, m.harga_barang)) AS gaji
    FROM request_log rl
    JOIN users u ON rl.user_id = u.user_id
    JOIN request r ON rl.request_id = r.request_id
    JOIN menu m ON r.menu_id = m.menu_id
    WHERE u.role = 'employee' 
    AND rl.status = 'completed'
";

// Menambahkan filter rentang tanggal jika ada
if ($start_date && $end_date) {
    $query .= " AND rl.waktu_diambil BETWEEN '$start_date' AND '$end_date'";
}

$query .= " GROUP BY u.user_id, u.firstname"; // Mengelompokkan berdasarkan user_id dan firstname

$result = mysqli_query($conn, $query);

// Menampilkan data gaji
include './inc/header.php';
include './inc/sidebar.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Gaji Employee</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-5">
        <h2 class="mb-4">Daftar Gaji Employee</h2>
        
        <!-- Form untuk memilih rentang tanggal -->
        <form method="POST" class="mb-4">
            <div class="form-row">
                <div class="col">
                    <label for="start_date">Dari Tanggal</label>
                    <input type="date" name="start_date" class="form-control" value="<?php echo $start_date; ?>" required>
                </div>
                <div class="col">
                    <label for="end_date">Hingga Tanggal</label>
                    <input type="date" name="end_date" class="form-control" value="<?php echo $end_date; ?>" required>
                </div>
                <div class="col mt-4">
                    <button type="submit" class="btn btn-primary">Filter</button>
                </div>
            </div>
        </form>

        <!-- Tabel untuk menampilkan gaji employee -->
        <!-- Tabel untuk menampilkan gaji employee -->
<table class="table table-bordered">
    <thead>
        <tr>
            <th>Nama</th>
            <th>Gaji</th>
            <th>Aksi</th>
        </tr>
    </thead>
    <tbody>
        <?php while ($row = mysqli_fetch_assoc($result)): ?>
            <tr>
                <td><?php echo htmlspecialchars($row['firstname']); ?></td>
                <td>Rp <?php echo number_format($row['gaji'], 0, ',', '.'); ?></td>
                <td>
                    <!-- Tombol Cetak Bukti -->
                    <form method="POST" action="bukti_pembayaran.php" target="_blank">
                        <input type="hidden" name="name" value="<?php echo htmlspecialchars($row['firstname']); ?>">
                        <input type="hidden" name="gaji" value="<?php echo $row['gaji']; ?>">
                        <input type="hidden" name="start_date" value="<?php echo $start_date; ?>">
                        <input type="hidden" name="end_date" value="<?php echo $end_date; ?>">
                        <button type="submit" class="btn btn-success btn-sm">Cetak Bukti Gaji</button>
                    </form>
                </td>
            </tr>
        <?php endwhile; ?>
    </tbody>
</table>

    </div>
</body>
</html>

<?php
include './inc/footer.php';
?>
