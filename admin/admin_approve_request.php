<?php
session_start();
require 'inc/db_connect.php';

// Periksa apakah user adalah admin
if (!isset($_SESSION['loggedin']) || $_SESSION['role'] !== 'admin') {
    header('Location: login.php');  // Redirect ke login jika bukan admin
    exit();
}

$query = "SELECT * FROM request WHERE status = 'pending'";
$result = mysqli_query($conn, $query);

include './inc/header.php';
include './inc/sidebar.php';    
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Approve Request Produk</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
</head>
<body>

<div class="container mt-5">
    <h2 class="text-center mb-4">Daftar Request Produk - Pending</h2>

    <div class="row">
        <?php while ($row = mysqli_fetch_assoc($result)): ?>
            <div class="col-md-4 mb-4">
                <div class="card">
                    <?php if ($row['gambar']): ?>
                        <img src="../img/<?php echo htmlspecialchars($row['gambar']); ?>" class="card-img-top" alt="Product Image">
                    <?php else: ?>
                        <img src="../img/default.jpg" class="card-img-top" alt="Product Image">
                    <?php endif; ?>
                    <div class="card-body">
                        <h5 class="card-title"><?php echo htmlspecialchars($row['nama']); ?></h5>
                        <p class="card-text">
                            <strong>Jenis Kelamin:</strong> <?php echo $row['jenis_kelamin'] === 'L' ? 'Laki-laki' : 'Perempuan'; ?><br>
                            <strong>Ukuran Baju:</strong> <?php echo htmlspecialchars($row['ukuran_baju']); ?><br>
                            <strong>Ukuran Lengan:</strong> <?php echo htmlspecialchars($row['ukuran_lengan']); ?><br>
                            <strong>Jumlah:</strong> <?php echo $row['jumlah']; ?><br>
                        </p>
                        <form action="approve_request.php" method="POST">
                            <input type="hidden" name="request_id" value="<?php echo $row['request_id']; ?>">
                            <button type="submit" name="approve" class="btn btn-success">Setujui</button>
                        </form>
                    </div>
                </div>
            </div>
        <?php endwhile; ?>
    </div>
</div>

<?php include './inc/footer.php'; ?>

</body>
</html>
