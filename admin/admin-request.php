<?php
ob_start();
session_start();
include './inc/db_connect.php';

// Pastikan user adalah admin
if (!isset($_SESSION['loggedin']) || $_SESSION['role'] !== 'admin') {
    header('Location: login.php');
    exit();
}

// Ambil data request
$query = "SELECT * FROM request";
$result = mysqli_query($conn, $query);

// Ambil menu untuk dropdown
$sql_menu = "SELECT * FROM menu";
$result_menu = mysqli_query($conn, $sql_menu);

include './inc/header.php';
include './inc/sidebar.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Menangkap data dari form
    $jenis_barang_id = $_POST['jenis_barang'];
    $jenis_kelamin = $_POST['jenis_kelamin'];
    $ukuran_baju = $_POST['ukuran_baju'];
    $ukuran_lengan = $_POST['ukuran_lengan'];
    $jumlah = $_POST['jumlah'];
    $saran = $_POST['saran'];
    $gambar = ""; 
    $bukti_pembayaran = ""; 

    // Menangani upload gambar
    if (isset($_FILES['gambar']) && $_FILES['gambar']['error'] == 0) {
        $gambar_name = $_FILES['gambar']['name'];
        $gambar_tmp_name = $_FILES['gambar']['tmp_name'];
        $gambar_size = $_FILES['gambar']['size'];
        $gambar_ext = pathinfo($gambar_name, PATHINFO_EXTENSION);

        $target_dir = "../img/";
        $target_file = $target_dir . basename($gambar_name);

        $allowed_extensions = ['jpg', 'jpeg', 'png', 'gif'];
        if (in_array(strtolower($gambar_ext), $allowed_extensions) && $gambar_size <= 5000000) {
            if (move_uploaded_file($gambar_tmp_name, $target_file)) {
                $gambar = $gambar_name;
            } else {
                echo "<script>alert('Gambar gagal diunggah.');</script>";
            }
        } else {
            echo "<script>alert('File gambar tidak valid.');</script>";
        }
    }

    // Menangani upload bukti pembayaran
    if (isset($_FILES['bukti_pembayaran']) && $_FILES['bukti_pembayaran']['error'] == 0) {
        $bukti_name = $_FILES['bukti_pembayaran']['name'];
        $bukti_tmp_name = $_FILES['bukti_pembayaran']['tmp_name'];
        $bukti_size = $_FILES['bukti_pembayaran']['size'];
        $bukti_ext = pathinfo($bukti_name, PATHINFO_EXTENSION);

        $target_dir_bukti = "../img/bukti_pembayaran/";
        $target_file_bukti = $target_dir_bukti . basename($bukti_name);

        $allowed_extensions = ['jpg', 'jpeg', 'png', 'pdf'];
        if (in_array(strtolower($bukti_ext), $allowed_extensions) && $bukti_size <= 5000000) {
            if (move_uploaded_file($bukti_tmp_name, $target_file_bukti)) {
                $bukti_pembayaran = $bukti_name;
            } else {
                echo "<script>alert('Bukti pembayaran gagal diunggah.');</script>";
            }
        } else {
            echo "<script>alert('File bukti pembayaran tidak valid.');</script>";
        }
    }

    // Ambil nama barang berdasarkan jenis_barang_id
    $sql_barang = "SELECT nama_barang FROM menu WHERE menu_id = ?";
    $stmt_barang = $conn->prepare($sql_barang);
    $stmt_barang->bind_param("i", $jenis_barang_id);
    $stmt_barang->execute();
    $result_barang = $stmt_barang->get_result();
    $row_barang = $result_barang->fetch_assoc();
    $nama_barang = $row_barang['nama_barang'];

    // Memanggil prosedur insert_request
    $sql = "CALL insert_request(?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param(
        "ssssissssi",
        $nama_barang,
        $jenis_kelamin,
        $ukuran_baju,
        $ukuran_lengan,
        $jumlah,
        $saran,
        $gambar,
        $bukti_pembayaran,  // Menambahkan bukti_pembayaran
        $_SESSION['user_id'],
        $jenis_barang_id
    );

    if ($stmt->execute()) {
        header('Location: ' . $_SERVER['REQUEST_URI']);
        exit;
    } else {
        echo "<script>alert('Terjadi kesalahan. Silakan coba lagi.');</script>";
    }
}
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Request Product</title>
    <link href="https://fonts.googleapis.com/css?family=Roboto" rel="stylesheet">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
</head>

<body>
    <div class="container mt-5">
        <h2 class="mb-4">Daftar Request</h2>

        <button class="btn btn-success mb-3" data-toggle="modal" data-target="#addRequestModal">Tambah Request</button>

        <table class="table table-striped table-bordered">
            <thead class="thead-dark">
                <tr>
                    <th>No</th>
                    <th>Nama</th>
                    <th>Detail Barang</th>
                    <th>Bukti Pembayaran</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $counter = 1;
                while ($row = mysqli_fetch_assoc($result)): ?>
                    <tr>
                        <td><?php echo $counter++; ?></td>
                        <td><?php echo htmlspecialchars($row['nama']); ?></td>
                        <td>
                            <button class="btn btn-info btn-sm" data-toggle="modal" data-target="#detailModal<?php echo $row['request_id']; ?>">Detail</button>
                        </td>
                        <td>
                            <?php if (!empty($row['bukti_pembayaran'])): ?>
                                <button class="btn btn-link btn-sm" data-toggle="modal" data-target="#paymentProofModal<?php echo $row['request_id']; ?>">Lihat Bukti</button>
                            <?php else: ?>
                                <span class="text-muted">Tidak ada bukti pembayaran</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if ($row['status'] === 'pending'): ?>
                                <form action="assign_employee.php" method="POST" style="display:inline;">
                                    <input type="hidden" name="request_id" value="<?php echo htmlspecialchars($row['request_id']); ?>">
                                    <button class="btn btn-primary btn-sm" type="submit" name="action" value="assign">Approve</button>
                                </form>
                                <form action="cancel_request.php" method="POST" style="display:inline;">
                                    <input type="hidden" name="request_id" value="<?php echo htmlspecialchars($row['request_id']); ?>">
                                    <button class="btn btn-danger btn-sm" type="submit" name="action" value="cancel">Cancel</button>
                                </form>
                            <?php elseif ($row['status'] === 'completed'): ?>
                                <button class="btn btn-success btn-sm" disabled>Order Selesai</button>
                            <?php else: ?>
                                <span class="text-muted"><?php echo htmlspecialchars($row['status']); ?></span>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>

        <!-- Modal Detail Request -->
        <?php foreach ($result as $row): ?>
            <div class="modal fade" id="detailModal<?php echo $row['request_id']; ?>" tabindex="-1" role="dialog" aria-labelledby="detailModalLabel<?php echo $row['request_id']; ?>" aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="detailModalLabel<?php echo $row['request_id']; ?>">Detail Request #<?php echo $row['request_id']; ?></h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <p><strong>Nama Barang:</strong> <?php echo htmlspecialchars($row['nama']); ?></p>
                            <p><strong>Jenis Kelamin:</strong> <?php echo htmlspecialchars($row['jenis_kelamin']); ?></p>
                            <p><strong>Ukuran Baju:</strong> <?php echo htmlspecialchars($row['ukuran_baju']); ?></p>
                            <p><strong>Ukuran Lengan:</strong> <?php echo htmlspecialchars($row['ukuran_lengan']); ?></p>
                            <p><strong>Jumlah:</strong> <?php echo htmlspecialchars($row['jumlah']); ?></p>
                            <p><strong>Saran:</strong> <?php echo htmlspecialchars($row['saran']); ?></p>
                            <?php if ($row['gambar']): ?>
                                <p><strong>Gambar:</strong><br><img src="../img/<?php echo $row['gambar']; ?>" alt="Gambar Request" style="max-width: 100%; height: auto;"></p>
                            <?php else: ?>
                                <p><strong>Gambar:</strong> Tidak ada gambar yang diunggah.</p>
                            <?php endif; ?>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>

        <!-- Modal Bukti Pembayaran -->
        <?php foreach ($result as $row): ?>
            <?php if (!empty($row['bukti_pembayaran'])): ?>
                <div class="modal fade" id="paymentProofModal<?php echo $row['request_id']; ?>" tabindex="-1" role="dialog" aria-labelledby="paymentProofModalLabel<?php echo $row['request_id']; ?>" aria-hidden="true">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="paymentProofModalLabel<?php echo $row['request_id']; ?>">Bukti Pembayaran Request #<?php echo $row['request_id']; ?></h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <p><strong>Bukti Pembayaran:</strong></p>
                                <img src="../img/bukti_pembayaran/<?php echo $row['bukti_pembayaran']; ?>" alt="Bukti Pembayaran" style="max-width: 100%; height: auto;">
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        <?php endforeach; ?>




        </tbody>
        </table>
    </div>

    <div class="modal fade" id="addRequestModal" tabindex="-1" role="dialog" aria-labelledby="addRequestModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addRequestModalLabel">Tambah Request Baru</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form action="admin-request.php" method="POST" enctype="multipart/form-data">
                    <div class="form-group">
                        <label for="jenis_barang">Jenis Barang</label>
                        <select class="form-control" id="jenis_barang" name="jenis_barang" required>
                            <option value="">Pilih Jenis Barang</option>
                            <?php
                            while ($row_menu = mysqli_fetch_assoc($result_menu)) {
                                echo "<option value='" . $row_menu['menu_id'] . "'>" . $row_menu['nama_barang'] . "</option>";
                            }
                            ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="jenis_kelamin">Jenis Kelamin</label>
                        <select class="form-control" id="jenis_kelamin" name="jenis_kelamin" required>
                            <option value="Laki-laki">Laki-laki</option>
                            <option value="Perempuan">Perempuan</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="ukuran_baju">Ukuran Baju</label>
                        <select class="form-control" id="ukuran_baju" name="ukuran_baju" required>
                            <option value="S">S</option>
                            <option value="M">M</option>
                            <option value="L">L</option>
                            <option value="XL">XL</option>
                            <option value="XXL">XXL</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="ukuran_lengan">Ukuran Lengan</label>
                        <select class="form-control" id="ukuran_lengan" name="ukuran_lengan" required>
                            <option value="Pendek">Pendek</option>
                            <option value="Panjang">Panjang</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="jumlah">Jumlah</label>
                        <input type="number" class="form-control" id="jumlah" name="jumlah" min="1" required>
                    </div>

                    <div class="form-group">
                        <label for="saran">Saran atau Permintaan Khusus (Opsional)</label>
                        <textarea class="form-control" id="saran" name="saran" rows="3"></textarea>
                    </div>

                    <div class="form-group">
                        <label for="gambar">Upload Gambar (Opsional)</label>
                        <input type="file" class="form-control-file" id="gambar" name="gambar">
                    </div>

                    <!-- Input untuk upload bukti pembayaran -->
                    <div class="form-group">
                        <label for="bukti_pembayaran">Upload Bukti Pembayaran</label>
                        <input type="file" class="form-control-file" id="bukti_pembayaran" name="bukti_pembayaran" accept="image/*">
                    </div>

                    <button type="submit" class="btn btn-primary">Kirim Request</button>
                </form>
            </div>
        </div>
    </div>
</div>


    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>
</body>

</html>