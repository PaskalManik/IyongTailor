<?php
ob_start();

require 'inc/db_connect.php';
require 'inc/header.php';

// Pengecekan apakah pengguna sudah login
if (!isset($_SESSION['loggedin']) || !$_SESSION['loggedin']) {
    header('Location: login.php');
    exit;
}

$user_id = $_SESSION['user_id'];

// Query untuk mendapatkan request sesuai user yang login
$query = "SELECT r.* 
          FROM request r 
          WHERE r.status IN ('cancelled', 'completed', 'approved', 'in_progress' , 'pending') 
          AND r.user_id = ?";

$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <title>Info Request - Request Produk</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>

<body>
    <div class="container mt-5">
        <h2 class="mb-4">Informasi Request Produk</h2>

        <?php if ($result->num_rows === 0): ?>
            <div class="alert alert-info">Anda tidak memiliki request dengan status tersebut.</div>
        <?php else: ?>
            <table class="table table-striped table-bordered">
                <thead class="thead-dark">
                    <tr>
                        <th>No</th>
                        <th>Nama Produk</th>
                        <th>Status</th>
                        <th>Detail</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $counter = 1;
                    while ($row = $result->fetch_assoc()):
                    ?>
                        <tr>
                            <td><?php echo $counter++; ?></td>
                            <td><?php echo htmlspecialchars($row['nama']); ?></td>
                            <td>
                                <?php
                                switch ($row['status']) {
                                    case 'completed':
                                        echo '<span class="badge badge-success">Selesai</span>';
                                        break;
                                    case 'approved':
                                        echo '<span class="badge badge-primary">Disetujui</span>';
                                        break;
                                    case 'pending':
                                        echo '<span class="badge badge-warning">Menunggu Persetujuan Admin</span>';
                                        break;
                                    case 'cancelled':
                                        echo '<span class="badge badge-danger">Order Dibatalkan</span>';
                                        break;
                                    case 'in_progress':
                                        echo '<span class="badge badge-info">Dalam Proses</span>';
                                        break;
                                    default:
                                        echo '<span class="badge badge-secondary">Status Tidak Dikenal</span>';
                                        break;
                                }
                                ?>
                            </td>

                            <td>
                                <!-- Tombol untuk membuka modal -->
                                <button type="button" class="btn btn-info btn-sm" data-toggle="modal" data-target="#detailModal<?php echo $row['request_id']; ?>">
                                    Lihat Detail
                                </button>
                            </td>
                        </tr>

                        <!-- Modal Detail -->
                        <div class="modal fade" id="detailModal<?php echo $row['request_id']; ?>" tabindex="-1" aria-labelledby="detailModalLabel" aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="detailModalLabel">Detail Request Produk</h5>
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                    <div class="modal-body">
                                        <!-- Menampilkan gambar -->
                                        <div class="text-center mb-3">
                                            <?php if (!empty($row['gambar'])): ?>
                                                <img src="img/<?php echo htmlspecialchars($row['gambar']); ?>" class="img-thumbnail" style="max-width: 200px;" alt="Gambar Produk">
                                            <?php else: ?>
                                                <img src="img/default.jpg" class="img-thumbnail" style="max-width: 200px;" alt="Default Image">
                                            <?php endif; ?>
                                        </div>
                                        <!-- Detail informasi -->
                                        <p><strong>Request ID:</strong> <?php echo htmlspecialchars($row['request_id']); ?></p>
                                        <p><strong>Nama Produk:</strong> <?php echo htmlspecialchars($row['nama']); ?></p>
                                        <p><strong>Saran:</strong> <?php echo htmlspecialchars($row['saran']); ?></p>
                                        <p><strong>Ukuran Baju:</strong> <?php echo htmlspecialchars($row['ukuran_baju']); ?></p>
                                        <p><strong>Ukuran Lengan:</strong> <?php echo htmlspecialchars($row['ukuran_lengan']); ?></p>
                                        <p><strong>Status:</strong> <?php echo htmlspecialchars($row['status']); ?></p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- End Modal -->
                    <?php endwhile; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>

    <?php   require 'inc/footer.php'; ?>

    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>