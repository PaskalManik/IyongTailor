<?php
session_start();
require 'inc/db_connect.php';

if (!isset($_SESSION['loggedin']) || $_SESSION['role'] !== 'employee') {
    header('Location: login.php');
    exit();
}

// Ambil daftar request produk yang statusnya 'approved' dan jumlah lebih dari 0 saja
$query = "SELECT * FROM request WHERE status = 'approved' AND jumlah > 0";
$result = mysqli_query($conn, $query);

include './inc/header.php';
include './inc/sidebar.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Employee - Request Produk</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-5">
        <h2 class="mb-4">Daftar Request Produk</h2>
        <div class="row">
            <?php while ($row = mysqli_fetch_assoc($result)): ?>
                <div class="col-md-4 mb-4">
                    <div class="card">
                        <img src="../img/<?php echo htmlspecialchars($row['gambar'] ?: 'default.jpg'); ?>" class="card-img-top" alt="Product Image">
                        <div class="card-body">
                            <h5 class="card-title"><?php echo htmlspecialchars($row['nama']); ?></h5>
                            <p class="card-text">
                                <strong>Jumlah:</strong> <?php echo $row['jumlah']; ?><br>
                                <strong>Ukuran Lengan:</strong> <?php echo $row['ukuran_lengan']; ?><br>
                                <strong>Ukuran Baju:</strong> <?php echo $row['ukuran_baju']; ?><br>
                                <strong>Status:</strong> <?php echo $row['status']; ?><br>
                                <strong>Saran:</strong> <?php echo $row['saran']; ?><br>
                            </p>
                            <?php if ($row['jumlah'] > 0): ?>
                                <form action="process_request.php" method="POST">
                                    <input type="hidden" name="request_id" value="<?php echo $row['request_id']; ?>">
                                    <div class="form-group">
                                        <label for="ambil_jumlah">Jumlah yang Diambil</label>
                                        <input 
                                            type="number" 
                                            name="ambil_jumlah" 
                                            id="ambil_jumlah" 
                                            class="form-control" 
                                            min="1" 
                                            max="<?php echo $row['jumlah']; ?>" 
                                            required>
                                    </div>

                                    <button type="submit" class="btn btn-primary btn-block">Ambil Request</button>
                                </form>
                            <?php else: ?>
                                <p class="text-muted">Stok Habis</p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>
    </div>
</body>
</html>

<?php
include './inc/footer.php';
?>
