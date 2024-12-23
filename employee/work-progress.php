<?php
session_start();
require 'inc/db_connect.php';

// Pastikan pengguna yang login adalah employee
if (!isset($_SESSION['loggedin']) || $_SESSION['role'] !== 'employee') {
    header('Location: login.php');
    exit();
}

// Ambil daftar request yang sudah diambil oleh employee ini dan statusnya "Dikerjakan"
$employee_id = $_SESSION['user_id']; // Mendapatkan ID employee dari session

$query = "SELECT * FROM request WHERE status = 'Dikerjakan' AND user_id = '$employee_id'";
$result = mysqli_query($conn, $query);

include './inc/header.php';  // Header untuk halaman
include './inc/sidebar.php'; // Sidebar untuk navigasi (opsional)
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Halaman Pengerjaan - Konveksi</title>
    <link href="https://fonts.googleapis.com/css?family=Roboto" rel="stylesheet">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
    <link rel="stylesheet" href="css/styles.css"> <!-- Ganti dengan path file CSS Anda -->
</head>
<body>

<div class="container my-5">
    <h2 class="text-center mb-4">Daftar Request yang Sedang Dikerjakan</h2>

    <div class="row">
        <?php if (mysqli_num_rows($result) > 0): ?>
            <?php while ($row = mysqli_fetch_assoc($result)): ?>
                <div class="col-md-4 mb-4">
                    <div class="card">
                        <!-- Menampilkan gambar produk -->
                        <?php if ($row['gambar']): ?>
                            <img src="uploads/<?php echo htmlspecialchars($row['gambar']); ?>" class="card-img-top" alt="Product Image">
                        <?php else: ?>
                            <img src="uploads/default.jpg" class="card-img-top" alt="Product Image">
                        <?php endif; ?>
                        <div class="card-body">
                            <h5 class="card-title"><?php echo htmlspecialchars($row['nama']); ?></h5>
                            <p class="card-text">
                                <strong>Jenis Kelamin:</strong> <?php echo $row['jenis_kelamin'] === 'L' ? 'Laki-laki' : 'Perempuan'; ?><br>
                                <strong>Ukuran Baju:</strong> <?php echo htmlspecialchars($row['ukuran_baju']); ?><br>
                                <strong>Ukuran Lengan:</strong> <?php echo htmlspecialchars($row['ukuran_lengan']); ?><br>
                                <strong>Jumlah:</strong> <?php echo $row['jumlah']; ?><br>
                            </p>

                            <!-- Form untuk mengubah status menjadi "Siap" -->
                            <?php if ($row['status'] == 'Dikerjakan'): ?>
                                <form action="update-status.php" method="POST">
                                    <input type="hidden" name="request_id" value="<?php echo $row['request_id']; ?>">
                                    <button type="submit" class="btn btn-success btn-block" name="mark_ready">Tandai Siap</button>
                                </form>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <p class="text-center">Tidak ada request yang sedang dikerjakan.</p>
        <?php endif; ?>
    </div>
</div>

<footer class="text-center py-3">
    <p class="text-muted"><small>&copy; 2024 Konveksi Usaha | Semua Hak Dilindungi</small></p>
</footer>

<script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>
</body>
</html>
